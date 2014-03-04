<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 AOE GmbH <dev@aoe.com>
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
 * Implements base class for command classes.
 *
 * @author Chetan Thapliyal <chetan.thapliyal@aoe.com>
 */
abstract class tx_donation_AbstractCommand implements tx_donation_Command {

	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var tx_donation_service_SpamProtection
	 */
	protected $spamProtectionService;

	/**
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return tx_donation_service_SpamProtection
	 */
	public function getSpamProtectionService() {
		if (!($this->spamProtectionService instanceof tx_donation_service_SpamProtection)) {
			$spamProtectionConfiguration = isset($this->configuration['spamProtection.']) ? $this->configuration['spamProtection.'] : array();
			$this->spamProtectionService = t3lib_div::makeInstance('tx_donation_service_SpamProtection');
			$this->spamProtectionService->setConfiguration($spamProtectionConfiguration);
		}

		return $this->spamProtectionService;
	}
}
