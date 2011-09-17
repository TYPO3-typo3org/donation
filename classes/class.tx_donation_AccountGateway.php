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

require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_Account.php';

/**
 * DB interface class to select and retrieve Account objects. Acts as a Table Data Gateway
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_AccountGateway {

	/**
	 * constructor for class tx_donation_AccountGateway
	 */
	public function __construct() {
		t3lib_div::loadTCA('tx_donation_account');
	}

	/**
	 * find Account objects by uid
	 *
	 * @param	integer	record unique ID
	 * @return	tx_donation_Account
	 */
	public function findByUid($uid) {
		$accountRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_donation_account',
			'uid = ' . $uid
		);
		$accountRow = $accountRow[0];

		$account = $this->createAccountFromRow($accountRow);

		return $account;
	}

	/**
	 * finds Account records by their page ID
	 *
	 * @param	integer	page id to get accounts from
	 * @return	array	array of tx_donation_Account objects
	 */
	public function findByPid($pid) {
		$accounts = array();
		$pidList  = implode(',', t3lib_div::intExplode(',', $pid));

		$accountRows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_donation_account',
			'pid IN (' . $pidList . ')'
		);

		foreach ($accountRows as $row) {
			$accounts[] = $this->createAccountFromRow($row);
		}

		return $accounts;
	}

	/**
	 * finds an account that matches the given email address
	 *
	 * @param	string	email address
	 * @return	tx_donation_Account
	 */
	public function findByPaypalEmail($email) {
		$accountRow = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			'tx_donation_account',
			'email_paypal = \'' . $email .'\''
		);
		$accountRow = $accountRow[0];

		$account = $this->createAccountFromRow($accountRow);

		return $account;
	}

	/**
	 * factory method that returns an account object from a row
	 *
	 * @param	array	database table row containing all fields
	 * @return	tx_donation_Account
	 */
	protected function createAccountFromRow(array $row) {
		$accountClass = t3lib_div::makeInstanceClassName('tx_donation_Account');

		$account = new $accountClass($row['uid']);
		$account->setPid($row['pid']);
		$account->setName($row['name']);
		$account->setImage($GLOBALS['TCA']['tx_donation_account']['columns']['image']['config']['uploadfolder'] . '/' . $row['image']);
		$account->setDescription($row['description']);
		$account->setBankAccount($row['bank_account']);
		$account->setEmailNotification($row['email_notification']);
		$account->setEmailPaypal($row['email_paypal']);
		return $account;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_AccountGateway.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_AccountGateway.php']);
}

?>