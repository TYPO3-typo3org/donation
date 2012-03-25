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
require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_Donation.php';

/**
 * class to log the instant payment notification (IPN) messages from PayPal
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_LogIpnCommand implements tx_donation_Command {

	protected $prefix;
	protected $plugin;
	protected $configuration;
	protected $parameters;
	protected $logTransactionTypes = array();

	/**
	 * constructor for class tx_donation_LogIpnCommand
	 */
	public function __construct($prefix = '') {
		$this->prefix = $prefix;

		$registry            = tx_donation_Registry::getInstance($prefix);
		$this->configuration = $registry->get('configuration');
		$this->plugin        = $registry->get('plugin');

		$this->parameters = t3lib_div::_POST();

			// log parameters coming from PayPal
		/*
		t3lib_div::devLog(
			'PayPal Request',
			'donation',
			0,
			array('server' => $_SERVER, 'post' => t3lib_div::_POST())
		);
		*/
		$this->logTransactionTypes = array('web_accept', 'subscr_payment');
	}

	public function execute() {
		$customParameters = explode('|', $this->parameters['custom']);

		$gateway = t3lib_div::makeInstance('tx_donation_AccountGateway');
		$account = $gateway->findByUid((int) $customParameters[0]);

		if (!in_array($this->parameters['txn_type'], $this->logTransactionTypes)) {
				// make sure we only add donation entries for payments
			return;
		}

			// Verify that the request came from Paypal, and not from some intrusion
		if (!$this->verifyPaypalIpn($this->parameters)) {
				// curl verification failed
			return;
		}

		if (t3lib_div::_POST('business') != $account->getEmailPaypal()) {
			t3lib_div::devLog(
				'PayPal Payment is not for the email address configured',
				'donation',
				2,
				array(
					'email_submitted'  => t3lib_div::_POST('business'),
					'email_configured' =>$account->getEmailPaypal()
				)
			);

			return;
		}

		$parameterCharset = strtolower($this->parameters['charset']);
		if ($parameterCharset != 'utf-8') {
			foreach ($this->parameters as $key => $parameter) {
				$this->parameters[$key] = $GLOBALS['TSFE']->csConvObj->utf8_encode($parameter, $parameterCharset);
			}
		}

		$donorName = $this->parameters['first_name'] .' '. $this->parameters['last_name'];


			// formatting fields
		$timestamp  = strtotime($this->parameters['payment_date']);
		$currency   = substr($this->parameters['mc_currency'], 0, 3);
		$feUserUid  = $customParameters[1];

		if (is_numeric($feUserUid) && $feUserUid > 0) {
			$feUserUid = (int) $feUserUid;
		} else {
			$feUserUid = $this->resolveFeUserByEmail($this->parameters['payer_email']);
		}

			// saving the donation
		$donation = t3lib_div::makeInstance('tx_donation_Donation');

		$donation->setPid($this->configuration['storagePid']);
		$donation->setHidden(true);
		$donation->setDate($timestamp);
		$donation->setName($donorName);
		$donation->setCompany($this->parameters['payer_business_name'] ? $this->parameters['payer_business_name'] : '');

		$donation->setAddressCity($this->parameters['address_city']);
		$donation->setAddressCountry($this->parameters['address_country']);
		$donation->setAddressCountryCode($this->parameters['address_country_code']);
		$donation->setAddressState($this->parameters['address_state']);
		$donation->setAddressStreet($this->parameters['address_street']);
		$donation->setAddressZip($this->parameters['address_zip']);

		$donation->setEmail($this->parameters['payer_email']);
		$donation->setUrl($this->findUrl($this->parameters['memo']));
		$donation->setAmount((float) $this->parameters['mc_gross']);
		$donation->setFee((float) $this->parameters['mc_fee']);
		$donation->setCurrency($currency);
		$donation->setFeUser($feUserUid);
		$donation->setComment($this->parameters['memo']);
		$donation->setAccount($account);
		$donation->setPaypalTransactionId($this->parameters['txn_id']);

		$donation->save();
			// saved

		t3lib_div::devLog(
			'Donation received from ' . $donorName . '(' . $this->parameters['payer_email'] . '), amount of ' . $donation->getAmount() . $donation->getCurrency(),
			'donation',
			0
		);

	}

	/**
	 * sets a prefixto be used as a means of namespace destinctions
	 *
	 * @param	string	prefix for namespace destinctions
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}

	protected function verifyPaypalIpn(array $parameters = array()) {
		$verified = false;

			// log parameters
		t3lib_div::devLog(
			'PayPal IPN verification',
			'donation',
			0,
			$parameters
		);

		$url = $this->configuration['paypalUrl'];
		$ch  = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_POST,           1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $this->getPaypalPostData($parameters));

		ob_start();

		if (curl_exec($ch)) {
			$info = ob_get_contents();
			curl_close($ch);
			ob_end_clean();

			if (eregi('VERIFIED', $info)) {
				$verified = true;

				t3lib_div::devLog(
					'PayPal IPN verification - VERIFIED',
					'donation',
					-1,
					$info
				);
			} else {
				t3lib_div::devLog(
					'PayPal IPN verification - FAILED',
					'donation',
					3,
					$info
				);
			}
		} else {
			t3lib_div::devLog(
				'Call to curl_exec() FAILED.',
				'donation',
				3,
				array(
					'parameters' => $parameters,
					'url'        => $url
				)
			);
		}

		return $verified;
	}

	protected function getPaypalPostData(array $data) {
		$post = '';

		foreach ($data as $key => $value) {
			$post .= $key . '=' . urlencode($value) . '&';
		}
		$post .= 'cmd=_notify-validate';

		return $post;
	}

	protected function resolveFeUserByEmail($email) {
		$userUid = '';

		$users = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, email',
			'fe_users',
			'email = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($email, 'fe_users')
		);

		if (is_array($users[0])) {
			$userUid = $users[0]['uid'];
		}

		return $userUid;
	}

	protected function findUrl($haystack = '') {
		$url     = '';
		$matches = array();

		if (preg_match('/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', $haystack, $matches)) {
			$url = $matches[0];
		}

		return $url;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_LogIpnCommand.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/pi_form/class.tx_donation_LogIpnCommand.php']);
}

?>
