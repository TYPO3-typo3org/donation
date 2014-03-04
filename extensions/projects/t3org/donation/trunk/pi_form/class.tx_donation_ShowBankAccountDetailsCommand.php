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
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_Donation.php';
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_HtmlTemplateView.php';

/**
 * Class to show the bank account information when a user selects the wire transfer method
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_ShowBankAccountDetailsCommand extends tx_donation_AbstractCommand {

	protected $prefix;
	protected $plugin;
	protected $parameters;

	/**
	 * constructor for class tx_donation_ShowBankAccountDetailsCommand
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

		try {
			$this->saveDonation($account);

			/** @var tx_donation_HtmlTemplateView $view */
			$view = t3lib_div::makeInstance('tx_donation_HtmlTemplateView', $this->configuration['templateFile'], 'bank_account_details', $this->prefix);
			$view->setViewHelperIncludePath($GLOBALS['PATH_donation'] . 'classes/viewHelpers/');

			$view->loadViewHelper('LLL', array(
				'languageFile' => $GLOBALS['PATH_donation'] . 'pi_form/locallang.xml',
				'llKey'        => $this->plugin->LLkey
			));
			$view->loadViewHelper('NL2BR');

			$view->addVariable('account', $account);

			return $view->render();
		} catch (Exception $e ) {
			return '<div class="typo3-messages"><div class="typo3-message message-error"><div class="message-header">' . $e->getMessage() . '</div><div class="message-body"></div></div></div>';
		}

	}

	/**
	 * sets a prefixto be used as a means of namespace destinctions
	 *
	 * @param	string	prefix for namespace destinctions
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	/**
	 * @param $account
	 * @throw Exception - should be catched in execute
	 */
	protected function saveDonation($account) {
		$donation = t3lib_div::makeInstance('tx_donation_Donation');

		$this->checkForSpam(
		//fields not to be checked for urls
			array(
				'company',
				'comment',
				'email',
				'url'
			)
		);

		$donation->setPid($this->configuration['storagePid']);
		$donation->setDate(time());
		$donation->setHidden(true);
		$donation->setName($this->parameters['name']);
		$donation->setCompany($this->parameters['company']);

		$donation->setAddressCity($this->parameters['addressCity']);
		$donation->setAddressCountry($this->parameters['addressCountry']);
		$donation->setAddressState($this->parameters['addressState']);
		$donation->setAddressStreet($this->parameters['addressStreet']);
		$donation->setAddressZip($this->parameters['addressZip']);

		$donation->setEmail($this->parameters['email']);
		$donation->setUrl($this->parameters['url']);
		$donation->setAmount((float) str_replace(',', '', $this->parameters['amount']));
		$donation->setCurrency($this->parameters['currency_code']);
		$donation->setFeUser($this->parameters['user']);
		$donation->setComment($this->parameters['comment']);
		$donation->setAccount($account);
		$donation->setPaypalTransactionId('bankwire');

		$donation->save();
	}

	protected function checkForSpam($whiteSpacedFields) {
		foreach($this->parameters as $parameterName => $parameterContent) {
			if(!in_array($parameterName, $whiteSpacedFields)) {
				$matchedItems = preg_match(
					'@((https?://)?([-\w]+\.[a-z]+)+\w(:\d+)?(/([-\w/_\.\,]*(\?\S+)?)?)*)@i',
					$parameterContent
				);
				if($matchedItems == 1) {
					throw new Exception('There was at least one field, which contains a url, which should not contains an url. Please use the back button of your browser to change and resubmit the form.');
				}
				if(strpos($parameterContent, "\n") || strpos($parameterContent, "\r")) {
					throw new Exception('There was at least one field, which contains a newline character, which is not possible to insert. Please use the back button of your browser to change and resubmit the form.');
				}
			}
		}

		$this->performAdditionalSpamChecks();
	}

	/**
	 * @throws Exception
	 */
	protected function performAdditionalSpamChecks() {
		$spamProtectionService = $this->getSpamProtectionService();
		$enabledSpamCheckMethods = $spamProtectionService->getEnabledMethods();

		/** @var tx_wtspamshield_processor $processor */
		$processor = $spamProtectionService->getProcessor();
		$processor->fieldValues = $this->parameters;
		$processor->additionalValues = $spamProtectionService->getMethodConfiguration();
		$processor->failureRate = (int) $spamProtectionService->getMethodConfiguration('failureRate');
		$processor->methodes = $enabledSpamCheckMethods;

		$error = $processor->validate();

		if (strlen($error)) {
			throw new Exception($error);
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBankAccountDetailsCommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_ShowBankAccountDetailsCommand.php']);
}
