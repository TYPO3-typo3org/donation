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

require_once $GLOBALS['PATH_donation'] . 'classes/class.tx_donation_LanguageFileUnavailableException.php';

/**
 * view helper to replace label markers starting with "LLL:"
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_LllViewHelper implements tx_donation_ViewHelper {

	protected $languageFile;
	protected $llKey;
	protected $localLang;

	/**
	 * constructor for class tx_donation_LllViewHelper
	 */
	public function __construct(array $arguments = array()) {

		if (!isset($arguments['languageFile'])) {
			throw new tx_donation_LanguageFileUnavailableException(
				'No Language File given',
				1216132287
			);
		}
		$this->languageFile = $arguments['languageFile'];
		$this->llKey        = $arguments['llKey'];

		$this->localLang = t3lib_div::readLLfile(
			$arguments['languageFile'],
			$arguments['llKey'],
			$GLOBALS['TSFE']->renderCharset
		);
	}

	/**
	 * returns a label for the given key
	 *
	 * @param array $arguments
	 * @return	string
	 */
	public function execute(array $arguments = array()) {
		return $this->localLang[$this->llKey][$arguments[0]];
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_LllViewHelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_LllViewHelper.php']);
}

?>