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

require_once(PATH_tslib . 'class.tslib_pibase.php');
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_Registry.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_SortableTable.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_HtmlTemplateView.php';

/**
 * Plugin 'Donations - Donors List' for the 'donation' extension.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	tx_donation
 */
class tx_donation_pi_donorlist extends tslib_pibase {
	var $prefixId      = 'tx_donation_pi_donorlist';		// Same as class name
	var $scriptRelPath = 'pi_donorlist/class.tx_donation_pi_donorlist.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'donation';	// The extension key.
	var $pi_checkCHash = true;

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf)	{
		$content = '';
		$this->initialize($conf);

		$includePath = t3lib_extMgm::extPath($this->extKey) . 'pi_donorlist/';

		$registry = tx_donation_Registry::getInstance($this->prefixId);
		$registry->set('cObj', $this->cObj);
		$registry->set('configuration', $this->conf);
		$registry->set('plugin', $this);

		$content = $this->execute();

		return $this->pi_wrapInBaseClass($content);
	}

	protected function execute() {
		$tableClass = t3lib_div::makeInstanceClassName('tx_donation_SortableTable');
		$table = new $tableClass(
			$this->prefixId,
			'tx_donation_donation',
			array(
				array('label' => 'Name',   'field' => 'name'),
				array('label' => 'Amount', 'field' => 'amount'),
				array('label' => 'Date',   'field' => 'crdate', 'sort' => 'DESC')
			)
		);
		/* @var $table tx_donation_SortableTable */

		$currentpage    = max(0, intval($this->piVars['page']));
		$recordsPerPage = intval($this->conf['recordsPerPage']);
		$table->setOffset($currentpage * $recordsPerPage);
		$table->setRowLimit($recordsPerPage);

		$headerColumns = $table->getHeaderColumns();
		$rows          = $table->getRows();

		$viewClass = t3lib_div::makeInstanceClassName('tx_donation_HtmlTemplateView');
		$view = new $viewClass(
			$this->conf['templateFile'],
			'donor_list',
			$this->prefixId
		);
		$view->setViewHelperIncludePath($GLOBALS['PATH_donation'] . 'classes/viewHelpers/');

		$view->addLoop('header_columns', $headerColumns);
		$view->addLoop('rows', $rows);

		$view->addMarker('pagebrowser', $this->getPageBrowser($table->getTotalRowCount()));

		$view->loadViewHelper('LINK');
		$view->loadViewHelper('WRAP');
		$view->loadViewHelper('MONEY');
		$view->loadViewHelper('TIMEAGO');

		$this->addCss();

		return $view->render();
	}

	protected function initialize($configuration) {
		$this->conf = $configuration;
		$this->tslib_pibase();
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();

			// flexform data
		$flexKeyMapping = array(
			'sDEF.pages'             => 'pages',
			'sDEF.recursive'         => 'recursive',
		);
		$this->conf['ffData'] = $this->getFlexFormConfig($flexKeyMapping);
	}

	protected function getPageBrowser($numberOfRecords) {
		$numberOfPages = intval($numberOfRecords / $this->conf['recordsPerPage'])
			+ (($numberOfRecords % $this->conf['recordsPerPage']) == 0 ? 0 : 1);

		$pageBrowserConfiguration = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_pagebrowse_pi1.'];
		$pageBrowserConfiguration += array(
			'pageParameterName' => $this->prefixId . '|page',
			'numberOfPages'     => $numberOfPages,
		);

			// Get page browser
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array(), '');

		$pageBrowser = $cObj->cObjGetSingle('USER', $pageBrowserConfiguration);

		return $pageBrowser;
	}

	protected function addCss() {
		$file = $GLOBALS['TSFE']->tmpl->getFileName($this->conf['cssFile']);
		$css = '<link rel="stylesheet" type="text/css" href="' . $file . '" />';

		$GLOBALS['TSFE']->additionalHeaderData['tx_donation'] = $css;
	}

	/**
	 * gets the flexform values as an array like defined by $flexKeyMapping
	 *
	 * @param	array	$flexKeyMapping: mapping of sheet.flexformFieldName => variable name
	 * @return	array	flexform configuration as an array
	 */
	function getFlexFormConfig($flexKeyMapping) {
		$conf = array();
		foreach($flexKeyMapping as $k => $v) {
			list($sheet, $field) = explode('.', $k);
			$conf[$v] = $this->pi_getFFvalue(
				$this->cObj->data['pi_flexform'],
				$field,
				$sheet
			);
		}

		return $conf;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_donorlist/class.tx_donation_pi_donorlist.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_donorlist/class.tx_donation_pi_donorlist.php']);
}

?>