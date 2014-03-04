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

require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_AccountGateway.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_HtmlTemplateView.php';
require_once $GLOBALS['PATH_donation'] . 'classes/service/class.tx_donation_service_SpamProtection.php';

/**
 * class to show the form for collecting the donor's contact information
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_ShowBankwireFormCommand extends tx_donation_AbstractCommand {

	protected $prefix;
	protected $plugin;
	protected $parameters;

	/**
	 * constructor for class tx_donation_ShowContactDataFormCommand
	 */
	public function __construct($prefix = '') {
		$this->prefix = $prefix;

		$registry = tx_donation_Registry::getInstance($prefix);
		$this->plugin = $registry->get('plugin');
		$this->parameters = t3lib_div::_GP('tx_donation');

		$this->setConfiguration($registry->get('configuration', array()));
	}

	public function execute() {
		$gateway = t3lib_div::makeInstance('tx_donation_AccountGateway');
		$account = $gateway->findByUid((int) $this->parameters['account']);

		/** @var tx_donation_HtmlTemplateView $view */
		$view = t3lib_div::makeInstance('tx_donation_HtmlTemplateView', $this->configuration['templateFile'], 'contact_data_form_bwire', $this->prefix);
		$view->setViewHelperIncludePath($GLOBALS['PATH_donation'] . 'classes/viewHelpers/');

		$view->loadViewHelper('LLL', array(
			'languageFile' => $GLOBALS['PATH_donation'] . 'pi_form/locallang.xml',
			'llKey'        => $this->plugin->LLkey
		));

		$view->addMarker('form_action', $this->plugin->pi_getPageLink($GLOBALS['TSFE']->id));
		$view->addMarker('amount', number_format((float) $this->parameters['amount'], 2));
		$view->addMarker('honeypot_field', $this->getSpamProtectionService()->getHoneypotField());

		$view->addVariable('account', $account);
		$view->addVariable('user', $this->getUserData());

		return $view->render();
	}

	/**
	 * sets a prefixto be used as a means of namespace destinctions
	 *
	 * @param string $prefix for namespace distinction
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * @return array
	 */
	protected function getUserData() {
		$userData = array(
			'uid'    => 0,
			'name'   => '',
			'email'  => '',
			'url'    => ''
		);

		if (is_array($GLOBALS['TSFE']->fe_user->user)) {
			$user = $GLOBALS['TSFE']->fe_user->user;

			$userData = array(
				'uid'    => $user['uid'],
				'name'   => $user['name'],
				'email'  => $user['email'],
				'url'    => $user['www']
			);
		}

		return $userData;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBankwireFormCommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBankwireFormCommand.php']);
}

?>
