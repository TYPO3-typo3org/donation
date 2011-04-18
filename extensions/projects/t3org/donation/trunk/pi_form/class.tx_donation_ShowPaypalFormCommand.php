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
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_AccountGateway.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_HtmlTemplateView.php';

/**
 * class to show the form for collecting the donor's contact information
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_ShowPaypalFormCommand implements tx_donation_Command {

	protected $prefix;
	protected $plugin;
	protected $configuration;
	protected $parameters;

	/**
	 * constructor for class tx_donation_ShowContactDataFormCommand
	 */
	public function __construct($prefix = '') {
		$this->prefix = $prefix;

		$registry            = tx_donation_Registry::getInstance($prefix);
		$this->configuration = $registry->get('configuration');
		$this->plugin        = $registry->get('plugin');

		$this->parameters = t3lib_div::_GP('tx_donation');
	}

	public function execute() {
		$gateway = t3lib_div::makeInstance('tx_donation_AccountGateway');
		$account = $gateway->findByUid((int) $this->parameters['account']);

		$viewClass = t3lib_div::makeInstanceClassName('tx_donation_HtmlTemplateView');
		$view = new $viewClass($this->configuration['templateFile'], 'contact_data_form_pp', $this->prefix);
		$view->setViewHelperIncludePath($GLOBALS['PATH_donation'] . 'classes/viewHelpers/');

		$view->loadViewHelper('LLL', array(
			'languageFile' => $GLOBALS['PATH_donation'] . 'pi_form/locallang.xml',
			'llKey' => $this->plugin->LLkey
		));

		$view->addMarker('form_action', $this->configuration['paypalUrl']);
		$view->addMarker('amount', $this->getAmount());
		$view->addMarker('url_thanks', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($this->configuration['thanksPid']));
		$view->addMarker('url_cancel', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id));
		$view->addMarker('url_notify', t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id, '', array('tx_donation[cmd]' => 'ipnlog')));
		$view->addMarkerArray($this->getUserData());
		$view->addVariable('account', $account);
		$view->addVariable('paypal', $this->getPaypalData());

		if ($this->getDonationType() == 'paypal') {
				// hide the subscription subpart when going for a one-time donation
			$view->addSubpart('paypal_subscription_data', '');
		}

		return $view->render();
	}

	/**
	 * sets a prefixto be used as a means of namespace destinctions
	 *
	 * @param	string	prefix for namespace destinctions
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	protected function getUserData() {
		$userData = array(
			'userid' => 0,
			'name'   => '',
			'email'  => '',
			'url'    => ''
		);

		if (isset($GLOBALS['TSFE']->fe_user->user)) {
			$user = $GLOBALS['TSFE']->fe_user->user;

			$userData = array(
				'userid' => $user['uid'],
				'name'   => $user['name'],
				'email'  => $user['email'],
				'url'    => $user['www']
			);
		}

		return $userData;
	}

	protected function getAmount() {
		return (int) max(
			$this->parameters['amount_pp'],
			$this->parameters['amount_ppr'],
			$this->parameters['amount_bwire']
		);
	}

	protected function getAccountId() {
		return (int) $this->parameters['account'];
	}

	protected function getDonationType() {
		$type = 'bankwire'; // default fallback

		switch($this->getAmount()) {
			case $this->parameters['amount_pp']:
				$type = 'paypal';
				break;
			case $this->parameters['amount_ppr']:
				$type = 'recuringpaypal';
				break;
			case $this->parameters['amount_bwire']:
			default:
				$type = 'bankwire';
		}

		return $type;
	}

	protected function getPaypalData() {
		$donationType = $this->getDonationType();

		if ($donationType == 'paypal') {
			$paypalData = array(
				'command'      => '_donations',
				'item_name'    => $this->configuration['paypalDonation.']['itemName'],
				'custom'       => $this->getPaypalCustomValue(),
				'amount'       => $this->getAmount(),
				'amount_field' => 'amount',
				'bn'           => 'PP-DonationsBF'
			);
		} elseif ($donationType == 'recuringpaypal') {
			$paypalData = array(
				'command'      => '_xclick-subscriptions',
				'item_name'    => $this->configuration['paypalSubscription.']['itemName'],
				'custom'       => $this->getPaypalCustomValue(),
				'amount'       => $this->getAmount(),
				'amount_field' => 'a3',
				'bn'           => 'PP-SubscriptionsBF'
			);
		}

		return $paypalData;
	}

	protected function getPaypalCustomValue() {
		$userId = 0;

		$gateway = t3lib_div::makeInstance('tx_donation_AccountGateway');
		$account = $gateway->findByUid((int) $this->parameters['account']);

		if (is_array($GLOBALS['TSFE']->fe_user->user)) {
			$userId = $GLOBALS['TSFE']->fe_user->user['uid'];
		}

		return $account->getUid() . '|' . $userId;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowPaypalFormCommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowPaypalFormCommand.php']);
}

?>