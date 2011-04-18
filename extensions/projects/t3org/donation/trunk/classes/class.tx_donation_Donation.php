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


class tx_donation_Donation {

	protected $uid;
	protected $pid;
	protected $crdate;
	protected $hidden;
	protected $deleted;
	protected $name;
	protected $company;

	protected $addressStreet;
	protected $addressZip;
	protected $addressCity;
	protected $addressState;
	protected $addressCountry;
	protected $addressCountryCode;

	protected $email;
	protected $url;
	protected $amount;
	protected $fee;
	protected $currency;
	protected $feUser;
	protected $comment;
	protected $account;
	protected $paypalTransactionId;

	protected $changedFields = array();


	public function __construct($uid = null) {
		$this->uid     = $uid;
		$this->hidden  = 0;
		$this->deleted = 0;
	}

	public function save() {
		$timestamp = time();

			// also sets the tstamp field

		if (is_null($this->uid)) {

			if ($this->amount > 0) {
					// insert
				$GLOBALS['TYPO3_DB']->exec_INSERTquery(
					'tx_donation_donation',
					array(
						'pid'                  => $this->pid,
						'tstamp'               => $timestamp,
						'crdate'               => $this->crdate,
						'hidden'               => (int) $this->hidden,
						'name'                 => $this->name,
						'company'              => $this->company,
						'address_city'         => $this->addressCity,
						'address_country'      => $this->addressCountry,
						'address_country_code' => $this->addressCountryCode,
						'address_state'        => $this->addressState,
						'address_street'       => $this->addressStreet,
						'address_zip'          => $this->addressZip,
						'email'                => $this->email,
						'url'                  => $this->url,
						'amount'               => $this->amount,
						'fee'                  => $this->fee,
						'currency'             => $this->currency,
						'feuser'               => $this->feUser,
						'comment'              => $this->comment,
						'account'              => $this->account->getUid(),
						'paypal_txn_id'        => $this->paypalTransactionId
					)
				);

				$this->sendNotificationEmail();
			}
		} else {
			// update
		}
	}

	public function delete() {
		// set deleted to true, then save
	}

	protected function sendNotificationEmail() {
		$notifyEmails = $this->account->getEmailNotification();
		$subject = 'New Donation made to ' . $this->account->getName() . ' on site ' . t3lib_div::getIndpEnv('TYPO3_SITE_URL');

		$body = '
A new donation has been made to %s on site %s.

Donor: %s
Company: %s
Address:
%s

E-Mail: %s
URL: %s

Amount: %s
Fee: %s
Comment: %s

		';

		$message = sprintf($body,
			$this->account->getName(),
			t3lib_div::getIndpEnv('TYPO3_SITE_URL'),
			$this->name,
			$this->company,
			$this->addressStreet
				. chr(10) . $this->addressZip . ' ' . $this->addressCity
				. chr(10) . $this->addressCountry . ' ' . $this->addressState,
			$this->email,
			$this->url,
			$this->amount . ' ' . $this->currency,
			$this->fee . ' ' . $this->currency,
			$this->comment
		);

		t3lib_div::plainMailEncoded(
			$notifyEmails,
			$subject,
			trim($message),
			'From: ' . $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_donation_pi_form.']['notificationSenderEmail']
		);

	}

	public function getUid() {
		return $this->uid;
	}

	public function getPid() {
		return $this->pid;
	}

	public function setPid($pageUid) {
		$this->pid = (int) $pageUid;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = htmlspecialchars(strip_tags($name));
		$this->changedFields[] = 'name';
	}

	public function getAmount() {
		return $this->amount;
	}

	public function setAmount($amount) {
		$this->amount = (float) $amount;
		$this->changedFields[] = 'amount';
	}

	public function getFee() {
		return $this->fee;
	}

	public function setFee($fee) {
		$this->fee = (float) $fee;
		$this->changedFields[] = 'fee';
	}


	public function getCurrency() {
		return $this->currency;
	}

	public function setCurrency($currency) {
		$this->currency = htmlspecialchars(strip_tags($currency));
		$this->changedFields[] = 'currency';
	}

	public function getDate() {
		return $this->crdate;
	}

	public function setDate($date) {
		$this->crdate = (int) $date;
		$this->changedFields[] = 'crdate';
	}

	public function setHidden($status) {
		$this->hidden = (boolean) $status;
	}

	public function isHidden() {
		return $this->hidden;
	}

	public function setEmail($email) {
		$this->email = htmlspecialchars(strip_tags($email));
	}

	public function getEmail() {
		return $this->email;
	}

	public function setFeUser($feUserUid) {
		$this->feUser = (int) $feUserUid;
	}

	public function getFeUser() {
		return $this->feUser;
	}

	public function setComment($comment) {
		$this->comment = htmlspecialchars(strip_tags($comment));
	}

	public function getComment() {
		return $this->comment;
	}

	public function setAccount(tx_donation_Account $account) {
		$this->account = $account;
	}

	public function getAccount() {
		return $this->account;
	}

	public function setUrl($url) {

		if (!empty($url) && !t3lib_div::isFirstPartOfStr($url, 'http://') && !t3lib_div::isFirstPartOfStr($url, 'https://')) {
			$url = 'http://' . $url;
		}

		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setPaypalTransactionId($transactionId) {
		$this->paypalTransactionId = $transactionId;
	}

	public function getPaypaltransactionId() {
		return $this->paypalTransactionId;
	}

	public function setCompany($company) {
		$this->company = htmlspecialchars(strip_tags($company));
	}

	public function getCompany() {
		return $this->company;
	}

	public function setAddressCity($addressCity) {
		$this->addressCity = htmlspecialchars(strip_tags($addressCity));
	}

	public function getAddressCity() {
		return $this->addressCity;
	}

	public function setAddressCountry($addressCountry) {
		$this->addressCountry = htmlspecialchars(strip_tags($addressCountry));
	}

	public function getAddressCountry() {
		return $this->addressCountry;
	}

	public function setAddressCountryCode($addressCountryCode) {
		$this->addressCountryCode = htmlspecialchars(strip_tags($addressCountryCode));
	}

	public function getAddressCountryCode() {
		return $this->addressCountryCode;
	}

	public function setAddressState($addressState) {
		$this->addressState = htmlspecialchars(strip_tags($addressState));
	}

	public function getAddressState() {
		return $this->addressState;
	}

	public function setAddressStreet($addressStreet) {
		$this->addressStreet = htmlspecialchars(strip_tags($addressStreet));
	}

	public function getAddressStreet() {
		return $this->addressStreet;
	}

	public function setAddressZip($addressZip) {
		$this->addressZip = htmlspecialchars(strip_tags($addressZip));
	}

	public function getAddressZip() {
		return $this->addressZip;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Donation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Donation.php']);
}

?>