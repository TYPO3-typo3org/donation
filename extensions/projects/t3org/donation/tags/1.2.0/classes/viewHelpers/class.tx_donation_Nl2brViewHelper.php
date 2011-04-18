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
 * view helper to replace label markers starting with "NL2BR:" with content that has been sent through nl2br()
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_Nl2brViewHelper implements tx_donation_ViewHelper {

	/**
	 * constructor for class tx_donation_Nl2brViewHelper
	 */
	public function __construct(array $arguments = array()) {

	}

	/**
	 * turns line breaks into <br /> tags
	 *
	 * @param array $arguments
	 * @return unknown
	 */
	public function execute(array $arguments = array()) {
		return nl2br($arguments[0]);
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_Nl2brViewHelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_Nl2brViewHelper.php']);
}

?>