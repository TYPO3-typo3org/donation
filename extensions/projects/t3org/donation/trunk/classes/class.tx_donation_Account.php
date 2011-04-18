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


/**
 * class to represent a donation account
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_Account {

	protected $uid;
	protected $pid;
	protected $name;
	protected $image;
	protected $bankAccount;
	protected $emailNotification;
	protected $emailPaypal;

	/**
	 * constructor for class tx_donation_Account
	 */
	public function __construct($uid = null) {
		$this->uid = $uid;
	}

	public function getUid() {
		return $this->uid;
	}

	public function getPid() {
		return $this->pid;
	}

	public function setPid($pid) {
		$this->pid = (int) $pid;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getImage() {
		return '<img src="' . $this->image .'" alt="Donate through ' . $this->name . ' " />';
	}

	public function setImage($image) {
		$this->image = $image;
	}

	public function getBankAccount() {
		return $this->bankAccount;
	}

	public function setBankAccount($bankAccount) {
		$this->bankAccount = $bankAccount;
	}

	public function getEmailNotification() {
		return $this->emailNotification;
	}

	public function setEmailNotification($email) {
		$this->emailNotification = $email;
	}

	public function getEmailPaypal() {
		return $this->emailPaypal;
	}

	public function setEmailPaypal($email) {
		$this->emailPaypal = $email;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Account.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Account.php']);
}

?>