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

require_once $GLOBALS['PATH_donation'] . 'interfaces/interface.tx_donation_Command.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_HtmlTemplateView.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_AccountGateway.php';

/**
 *
 */
class tx_donation_ShowBucketsCommand implements tx_donation_Command  {

	protected $prefix;
	protected $plugin;
	protected $configuration;

	/**
	 * constructor for class tx_donation_ShowBucketsCommand
	 */
	public function __construct($prefix = '') {
		$this->prefix = $prefix;

		$registry            = tx_donation_Registry::getInstance($prefix);
		$this->configuration = $registry->get('configuration');
		$this->plugin        = $registry->get('plugin');
	}

	public function execute() {
		$pidList = $this->plugin->pi_getPidList(
			$this->configuration['ffData']['pages'],
			$this->configuration['ffData']['recursive']
		);

		$gateway  = t3lib_div::makeInstance('tx_donation_AccountGateway');
		$accounts = $gateway->findByPid($pidList);

		$viewClass = t3lib_div::makeInstanceClassName('tx_donation_HtmlTemplateView');
		$view = new $viewClass($this->configuration['templateFile'], 'select_account', $this->prefix);

		$view->addLoop('accounts', $accounts);
		$view->addMarker('form_action', $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id));
		$view->addMarker('url_thanks', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($this->configuration['thanksPid']));
		$view->addMarker('url_cancel', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id));
		$view->addMarker('url_notify', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id, '', array('tx_donation[cmd]' => 'ipnlog')));
		$view->addVariable('paypal', $this->getPaypalData());
		$view->addMarker('bankwire_action', $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id));

		$this->addJsToPage();

		return $view->render();
	}

	/**
	 * sets a prefix to be used as a means of namespace destinctions
	 *
	 * @param	string	prefix for namespace destinctions
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	protected function addJsToPage() {
		$siteRelPath = t3lib_extMgm::siteRelPath($this->plugin->extKey);

		if ($this->configuration['loadJsFramework']) {
			$prototype = $siteRelPath . 'resources/prototype/prototype.js';
			$scriptaculous = $siteRelPath . 'resources/scriptaculous/scriptaculous.js?load=effects';
			if ($this->configuration['useJsFrameworkFromContrib']) {
				$prototype = TYPO3_mainDir . 'contrib/prototype/prototype.js';
				$scriptaculous = TYPO3_mainDir . 'contrib/scriptaculous/scriptaculous.js?load=effects';
			}

			$GLOBALS['TSFE']->additionalHeaderData[$this->prefix] .=
				'<script type="text/javascript" src="' . $prototype . '"></script>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->prefix] .=
				'<script type="text/javascript" src="' . $scriptaculous . '"></script>';
		}

		$GLOBALS['TSFE']->additionalHeaderData[$this->prefix] .=
			'<script type="text/javascript" src="'
			. $siteRelPath
			. 'resources/form/pi_form.js"></script>';
	}

	/**
	 * compiles an array of data used in the PayPal forms
	 *
	 */
	protected function getPaypalData() {
		$userId    = 0;
		$accountId = 0;

		if (is_array($GLOBALS['TSFE']->fe_user->user)) {
			$userId = $GLOBALS['TSFE']->fe_user->user['uid'];
		}

		$paypalData = array(
			'action'                 => $this->configuration['paypalUrl'],
			'item_name'              => $this->configuration['paypalDonation.']['itemName'],
			'item_name_subscription' => $this->configuration['paypalSubscription.']['itemName'],
			'custom'                 => $accountId . '|' . $userId
		);

		return $paypalData;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBucketsCommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBucketsCommand.php']);
}

?>