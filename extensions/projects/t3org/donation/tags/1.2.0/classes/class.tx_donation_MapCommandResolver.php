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

require_once $GLOBALS['PATH_donation'] . 'interfaces/interface.tx_donation_CommandResolver.php';

/**
 * resolver class for the form plugin in EXT:donation
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_MapCommandResolver implements tx_donation_CommandResolver {

	protected $includePath;
	protected $map;
	protected $defaultCommand;
	protected $prefix;

	/**
	 * constructor for class tx_donation_PiFormCommandResolver
	 */
	public function __construct($includePath, array $map, $defaultCommand, $prefix = null) {
		$this->includePath    = $includePath;
		$this->map            = $map;
		$this->defaultCommand = $defaultCommand;

		$this->prefix = '';
		if (!is_null($prefix)) {
			$this->prefix = $prefix;
		}
	}

	public function getCommand() {
		$parameters = t3lib_div::_GP('tx_donation');
		$command    = null;

		if ($parameters['cmd'] && array_key_exists($parameters['cmd'], $this->map)) {
			$commandName = $this->map[$parameters['cmd']];
			$command     = $this->loadCommand($commandName);
		}

		if (!$command instanceof tx_donation_Command) {
			$command = $this->loadCommand($this->map[$this->defaultCommand]);
		}

		return $command;
	}

	protected function loadCommand($commandName) {
		$className = 'tx_donation_' . $commandName . 'Command';
		$fileName  = $this->includePath . 'class.' . $className . '.php';

		if (!file_exists($fileName)) {
			return false;
		}

		include_once $fileName;
		$commandClass = t3lib_div::makeInstanceClassName($className);
		$command      = new $commandClass($this->prefix);

		return $command;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_MapCommandResolver.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/class.tx_donation_MapCommandResolver.php']);
}

?>