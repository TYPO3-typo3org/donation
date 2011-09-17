<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Ingo Renner <ingo@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once $GLOBALS['PATH_donation'] . 'interfaces/interface.tx_donation_ViewHelper.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_CobjUnavailableException.php';


class tx_donation_HtmlTemplateView {

	protected $prefix;
	protected $cObj;
	protected $template;
	protected $workOnSubpart;
	protected $viewHelperIncludePath;
	protected $helpers   = array();
	protected $variables = array();
	protected $markers   = array();
	protected $subparts  = array();
	protected $loops     = array();

	/**
	 * constructor for the html marker template view
	 *
	 * @param	string	path to the html template file
	 * @param	string	name of the subpart to work on
	 * @param	string	optional prefix to be used as means of namespace distinctions
	 */
	public function __construct($htmlFile, $subpart, $prefix = '') {
		$this->prefix = $prefix;

		$registry = tx_donation_Registry::getInstance($this->prefix);
		$cObj = $registry->get('cObj');

		if (!$cObj instanceof tslib_cObj) {
			throw new tx_donation_CobjUnavailableException(
				'No cObj found',
				1215670789
			);
		}
		$this->cObj = $cObj;

		$this->loadHtmlFile($htmlFile);
		$this->workOnSubpart($subpart);
	}

	/**
	 * loads the content of a html template file. Resolves paths beginning with EXT:
	 *
	 * @param	string	path to html template file
	 */
	public function loadHtmlFile($htmlFile) {
		$this->template = $this->cObj->fileResource($htmlFile);
	}

	public function setViewHelperIncludePath($path) {
		$this->viewHelperIncludePath = $path;
	}

	/**
	 * loads a view helper
	 *
	 * @param	string	view helper name
	 * @param	array	optional array of arguments
	 */
	public function loadViewHelper($helper, array $arguments = array()) {

		if (!isset($this->helpers[$helper])) {
			$className = 'tx_donation_' . ucfirst(strtolower($helper)) . 'ViewHelper';
			$fileName  = $this->viewHelperIncludePath . 'class.' . $className . '.php';

			if (!file_exists($fileName)) {
				return false;
			}

			include_once $fileName;
			$helperClass    = t3lib_div::makeInstanceClassName($className);
			$helperInstance = new $helperClass($arguments);

			if (!$helperInstance instanceof tx_donation_ViewHelper) {
				return false;
			}
			$this->helpers[$helper] = $helperInstance;
		}
	}

	/**
	 * renders the template and fills its markers
	 *
	 * @return	string the rendered html template with markers replaced with their content
	 */
	public function render() {

			// process loops
		foreach ($this->loops as $key => $loopVariables) {
			$this->renderLoop($key);
		}
			// process variables
		foreach ($this->variables as $variableKey => $variable) {
			$variableKey     = strtoupper($variableKey);
			$variableMarkers = $this->getVariableMarkers($variableKey, $this->workOnSubpart);

			$resolvedMarkers = $this->resolveVariableMarkers($variableMarkers, $variable);

			$this->workOnSubpart = $this->cObj->substituteMarkerArray(
				$this->workOnSubpart,
				$resolvedMarkers,
				'###|###'
			);
		}

			// process markers
		$this->workOnSubpart = $this->cObj->substituteMarkerArray(
			$this->workOnSubpart,
			$this->markers
		);

			// process subparts
		foreach ($this->subparts as $subpart => $content) {
			$this->workOnSubpart = $this->cObj->substituteSubpart(
				$this->workOnSubpart,
				$subpart,
				$content
			);
		}

			// process helpers, need to be the last objects processing the template
		foreach ($this->helpers as $helperKey => $helper) {
			$helperKey     = strtoupper($helperKey);
			$helperMarkers = $this->getHelperMarkers($helperKey, $this->workOnSubpart);

			foreach ($helperMarkers as $marker) {
				$arguments = explode('|', $marker);
				$content   = $helper->execute($arguments);

				$this->workOnSubpart = $this->cObj->substituteMarker(
					$this->workOnSubpart,
					'###' . $helperKey . ':' . $marker . '###',
					$content
				);
			}
		}


		return $this->workOnSubpart;
	}

	protected function renderLoop($loop) {
		$loopContent    = '';
		$loopTemplate   = $this->getSubpart('LOOP:' . $loop);
		$loopSingleItem = $this->getSubpart('loop_content', $loopTemplate);
		$variables      = $this->loops[$loop];
		$markers        = $this->getMarkersFromSubpart($loopSingleItem);

		foreach ($variables as $value) {
			$resolvedMarkers = $this->resolveVariableMarkers($markers, $value);

			$loopContent .= $this->cObj->substituteMarkerArray(
				$loopSingleItem,
				$resolvedMarkers,
				'###|###'
			);
		}

		$this->workOnSubpart = $this->cObj->substituteSubpart(
			$this->workOnSubpart,
			'###LOOP:' . strtoupper($loop) . '###',
			$loopContent
		);
	}

	protected function resolveVariableMarkers(array $markers, $variableValue) {
		$resolvedMarkers = array();

		foreach ($markers as $marker) {
			$dotPosition = strpos($marker, '.');

			if ($dotPosition !== false) {
					// the marker contains a dot, thus we have to resolve the second part of the marker
				$valueSelector = strtolower(substr($marker, $dotPosition + 1));

				if (is_array($variableValue)) {
					$resolvedValue = $variableValue[$valueSelector];
				} else if (is_object($variableValue)) {
					$resolveMethod = 'get' . $this->camelize($valueSelector);
					$resolvedValue = $variableValue->$resolveMethod();
				}
			} else {
				$resolvedValue = $variableValue[strtolower($marker)];
			}

			if (is_null($resolvedValue)) {
				$resolvedValue = '!!!Marker &quot;' . $marker . '&quot; could not be resolved.';
			}

			$resolvedMarkers[$marker] = $resolvedValue;
		}

		return $resolvedMarkers;
	}

	public function workOnSubpart($subpart) {
		$this->workOnSubpart = $this->getSubpart($subpart, $this->template);
	}

	/**
	 * retrievs a supart from the given html template
	 *
	 * @param	string	subpart marker name, can be lowercase, doesn't need the ### delimiters
	 * @return	string	the html subpart
	 */
	public function getSubpart($subpartName, $alternativeTemplate = '') {
		$template = $this->workOnSubpart;

			// set altenative template to work on
		if (!empty($alternativeTemplate)) {
			$template = $alternativeTemplate;
		}

		$subpart = $this->cObj->getSubpart(
			$template,
			'###' . strtoupper($subpartName) . '###'
		);

		return $subpart;
	}

	/**
	 * sets a marker's value
	 *
	 * @param	string	marker name, can be lower case, doesn't need the ### delimiters
	 * @param	string	the marker's value
	 */
	public function addMarker($marker, $content) {
		$this->markers['###' . strtoupper($marker) . '###'] = $content;
	}

	public function addMarkerArray(array $markers) {
		foreach ($markers as $marker => $content) {
			$this->addMarker($marker, $content);
		}
	}

	/**
	 * sets a subpart's value
	 *
	 * @param	string	subpart name, can be lower case, doesn't need the ### delimiters
	 * @param	string	the subpart's value
	 */
	public function addSubpart($subpartMarker, $content) {
		$this->subparts['###' . strtoupper($subpartMarker) . '###'] = $content;
	}

	/**
	 * assigns a variable to the html template.
	 * Simple variables can be used like regular markers or in the form VAR:"VARIABLE_NAME" (without the quotes).
	 * Objects can be used in the form VAR:"OBJECT_NAME"."PROPERTY_NAME" (without the quotes)
	 *
	 * @param	string	variable key
	 * @param	mixed	variable value
	 */
	public function addVariable($key, $value) {
		$this->variables[$key] = $value;
	}

	public function addLoop($loopName, array $variables) {

		$this->loops[$loopName] = $variables;

		// use foreach with an "Iterator" to run through $variables

	}

	public function getMarkersFromSubpart($subpart) {
		preg_match_all('!###([A-Z0-9_-|.]*)\###!is', $subpart, $match);
		$markers = array_unique($match[1]);

		return $markers;
	}

	public function getHelperMarkers($helperMarker, $subpart) {
			// '!###' . $helperMarker . ':([A-Z0-9_-|.]*)\###!is'
		preg_match_all(
			'!###' . $helperMarker . ':(.*?)\###!is',
			$subpart,
			$match
		);
		$markers = array_unique($match[1]);

		return $markers;
	}

	public function getVariableMarkers($variableMarker, $subpart) {
		preg_match_all(
			'!###(' . $variableMarker . '\.[A-Z0-9_-]*)\###!is',
			$subpart,
			$match
		);
		$markers = array_unique($match[1]);

		return $markers;
	}

	/**
	 * Returns given word as CamelCased
	 *
	 * Converts a word like "send_email" to "SendEmail". It
	 * will remove non alphanumeric characters from the word, so
	 * "who's online" will be converted to "WhoSOnline"
	 *
	 * @param	string	Word to convert to camel case
	 * @return	string	UpperCamelCasedWord
	 */
	protected function camelize($word)
	{
		return str_replace(' ', '', ucwords(preg_replace('![^A-Z^a-z^0-9]+!', ' ', $word)));
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_HtmlTemplateView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_HtmlTemplateView.php']);
}

?>