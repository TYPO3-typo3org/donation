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
require_once $GLOBALS['PATH_donation'] . '/classes/class.tx_donation_MapCommandResolver.php';
require_once $GLOBALS['PATH_donation'] . '/classes/class.tx_donation_Registry.php';


/**
 * Plugin 'Donations - Form' for the 'donation' extension.
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package	TYPO3
 * @subpackage	tx_donation
 */
class tx_donation_pi_form extends tslib_pibase {
	public $prefixId      = 'tx_donation_pi_form';		// Same as class name
	public $scriptRelPath = 'pi_form/class.tx_donation_pi_form.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'donation';	// The extension key.

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf)	{
		$content = '';
		$this->initialize($conf);

		$includePath = t3lib_extMgm::extPath($this->extKey) . 'pi_form/';

		$registry = tx_donation_Registry::getInstance($this->prefixId);
		$registry->set('cObj', $this->cObj);
		$registry->set('configuration', $this->conf);
		$registry->set('plugin', $this);

		$resolverClass = t3lib_div::makeInstanceClassName('tx_donation_MapCommandResolver');
		$resolver      = new $resolverClass(
			$includePath,
			$this->conf['commandMap'],
			'buckets',
			$this->prefixId
		);

		$command = $resolver->getCommand();
		$content = $command->execute();

		$this->addCssToPage();

		return $this->pi_wrapInBaseClass($content);
	}

	protected function initialize($configuration) {
		$this->conf = $configuration;
		$this->tslib_pibase();
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1; // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->pi_initPIflexForm();

		$this->conf['commandMap'] = array(
			'bankaccountdetails'    => 'ShowBankAccountDetails',
			'buckets'               => 'ShowBuckets',
			'paypalform'            => 'ShowPaypalForm',
			'bankwireform'          => 'ShowBankwireForm',
			'ipnlog'                => 'LogIpn'
		);

			// flexform data
		$flexKeyMapping = array(
			'sDEF.pages'             => 'pages',
			'sDEF.recursive'         => 'recursive',
		);
		$this->conf['ffData'] = $this->getFlexFormConfig($flexKeyMapping);
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

	protected function addCssToPage() {
		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId] .=
			'<link rel="stylesheet" type="text/css" media="screen" href="'
			. $GLOBALS['TSFE']->tmpl->getFileName($this->conf['cssFile']) . '">';
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_pi_form.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_pi_form.php']);
}

?>