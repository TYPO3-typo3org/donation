<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Christian Zenker <christian.zenker@599media.de>
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
 * a view helper that renders a donor in the donation list
 *
 * @author	Christian Zenker <christian.zenker@599media.de>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_DonatorViewHelper implements tx_donation_ViewHelper {

	/**
	 * constructor for class tx_donation_DonationViewHelper
	 */
	public function __construct(array $arguments = array()) {

	}

	public function execute(array $arguments = array()) {
		$name = trim($arguments[0]);
		$company = trim($arguments[1]);
		$link = trim($arguments[2]);
		
		$content = $name;
		if($company) {
			$content .= ' ('.$company.')';
		}
		if($link) {
			$content = sprintf('<a href="%s" target="_blank" class="s-external-link">%s</a>', $link, $content);
		}
		
		
		return $content;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_DonationViewHelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_DonationViewHelper.php']);
}

?>