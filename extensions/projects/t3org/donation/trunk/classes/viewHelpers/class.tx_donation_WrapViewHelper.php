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
 * a wrap view helper
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_WrapViewHelper implements tx_donation_ViewHelper {

	/**
	 * constructor for class tx_donation_WrapViewHelper
	 */
	public function __construct(array $arguments = array()) {

	}

	public function execute(array $arguments = array()) {
		$content = '';

		if (!empty($arguments[0])) {
			$content = $arguments[1] . $arguments[0] . $arguments[2];
		}

		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_WrapViewHelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_WrapViewHelper.php']);
}

?>