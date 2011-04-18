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
 * registry class to store objects and make them available across the layers
 * of the MVC Pattern
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_Registry {

	protected static $instances = array();

	protected $values = array();
	protected $name;


	/**
	 * singleton instance access method for the Registry class
	 *
	 * @return tx_donation_Registry
	 */
	public function getInstance($instanceName = 'global') {
		if (!isset(self::$instances[$instanceName])) {
			self::$instances[$instanceName] = new tx_donation_Registry($instanceName);
		}

		return self::$instances[$instanceName];
	}

	/**
	 * constructor for class tx_donation_Registry
	 *
	 */
	protected function __construct($name) {
		$this->name = $name;
	}

	/**
	 * clone interceptor method, declared private to implement the singleton pattern
	 *
	 */
	private function __clone() {}

	/**
	 * sets a value for a key in the registry
	 *
	 * @param	string	key to store the value under
	 * @param	mixed	value to store
	 */
	public function set($key, $value) {
		$this->values[$key] = $value;
	}

	/**
	 * gets the value stored under the given key. returns null if no value is
	 * available for the given key
	 *
	 * @param	string	the key to retrieve the value for
	 * @return	mixed
	 */
	public function get($key) {
		$value = null;

		if (isset($this->values[$key])) {
			$value = $this->values[$key];
		}

		return $value;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Registry.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_Registry.php']);
}

?>