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
 * a "time ago" view helper that calculates the difference between a timestamp and now
 *
 * @author	Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage donation
 */
class tx_donation_TimeAgoViewHelper implements tx_donation_ViewHelper {

	static protected $locallang = null;
	protected $llKey;

	/**
	 * constructor for class tx_donation_TimeAgoViewHelper
	 */
	public function __construct(array $arguments = array()) {
		$this->llKey = 'default';

		if ($GLOBALS['TSFE']->config['config']['language']) {
			$this->llKey = $GLOBALS['TSFE']->config['config']['language'];
		}

		if (is_null(self::$locallang)) {
			self::$locallang = t3lib_div::readLLfile(
				$GLOBALS['PATH_donation'] . 'pi_donorlist/locallang.xml',
				$this->llKey,
				$GLOBALS['TSFE']->renderCharset
			);
		}
	}

	public function execute(array $arguments = array()) {
		$timeAgo = $this->formatInterval(
			$_SERVER['REQUEST_TIME'] - $arguments[0],
			2
		);

		return str_replace('@time', $timeAgo, $this->getLL('time_ago'));
	}

	protected function formatInterval($interval, $granularity = 2) {
		$content = '';
		$units   = array(
			$this->getLL('time_year')   => 31536000,
			$this->getLL('time_month')  => 2419200,
			$this->getLL('time_week')   => 604800,
			$this->getLL('time_day')    => 86400,
			$this->getLL('time_hour')   => 3600,
			$this->getLL('time_minute') => 60,
			$this->getLL('time_second') => 1
		);

		foreach ($units as $label => $timespan) {
			$label = explode('|', $label);
			if ($interval >= $timespan) {
				if (!empty($content)) {
					$content .=  ' ';
				}

				$count = floor($interval / $timespan);
				if ($count > 1) {
					$content .= str_replace('@count', $count, $label[1]);
				} else {
					$content .= $label[0];
				}

				$interval %= $timespan;
				$granularity--;
			}

			if ($granularity == 0) {
				break;
			}
		}

		return !empty($content) ? $content : '0 sec';
	}

	protected function getLL($labelKey) {
		$label = self::$locallang['default'][$labelKey];
		if (isset(self::$locallang[$this->llKey][$labelKey])) {
			$label = self::$locallang[$this->llKey][$labelKey];
		}

		return $label;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_TimeagoViewHelper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/donation/classes/viewHelpers/class.tx_donation_TimeagoViewHelper.php.php']);
}

?>