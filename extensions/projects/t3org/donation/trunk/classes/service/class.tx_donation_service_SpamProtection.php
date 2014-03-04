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
 * Implements a thin wrapper around spam check services provided by "wt_spamshield" extension.
 *
 * @author Chetan Thapliyal <chetan.thapliyal@aoe.com>
 */
class tx_donation_service_SpamProtection implements t3lib_Singleton {

	/**
	 * @var array
	 */
	private $configuration = array();

	/**
	 * @var array
	 */
	private $methodConfiguration;

	/**
	 * @param array $configuration
	 */
	public function setConfiguration(array $configuration) {
		$this->configuration = $configuration;
	}

	/**
	 * @return tx_wtspamshield_method_honeypot
	 */
	public function getHoneypotField() {
		if ($this->isEnabledSpamProtectionMethod('honeypotCheck')) {
			/** @var tx_wtspamshield_method_honeypot $honeypotInstance */
			$honeypotInstance = t3lib_div::makeInstance('tx_wtspamshield_method_honeypot');

			$configuration = $this->getMethodConfiguration('honeypotCheck');
			if (count($configuration)) $honeypotInstance->additionalValues = $configuration;

			$honeyPotField = $honeypotInstance->createHoneypot();
		} else {
			$honeyPotField = '';
		}

		return $honeyPotField;
	}

	/**
	 * Checks if the given type of spam-protection is enabled.
	 *
	 * @param  string $method       Type of spam protection.
	 * @return bool
	 */
	protected function isEnabledSpamProtectionMethod($method) {
		$status = (isset($this->configuration[$method]) && $this->configuration[$method] === '1');
		return $status;
	}

	/**
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getConfiguration($key, $default = null) {
		$value = isset($this->configuration[$key]) ? $this->configuration[$key] : $default;
		return $value;
	}

	/**
	 * @param string $method
	 * @return array
	 */
	public function getMethodConfiguration($method = '') {
		if (!is_array($this->methodConfiguration)) {
			foreach ($this->configuration as $_method => $configuration) {
				if (substr($_method, -6) !== 'Check.') continue;
				$this->methodConfiguration[rtrim($_method, '.')] = $configuration;
			}
			if (!is_array($this->methodConfiguration)) $this->methodConfiguration = array();
		}

		if (strlen($method) && isset($this->methodConfiguration[$method])) {
			$configuration = $this->methodConfiguration[$method];
		} else {
			$configuration = $this->methodConfiguration;
		}

		return $configuration;
	}

	/**
	 * @return tx_wtspamshield_processor
	 */
	public function getProcessor() {
		$processor = $this->getDiv()->getProcessor();
		return $processor;
	}

	/**
	 * @return tx_wtspamshield_div
	 */
	protected function getDiv() {
		$div = t3lib_div::makeInstance('tx_wtspamshield_div');
		return $div;
	}

	/**
	 * @return array
	 */
	public function getEnabledMethods() {
		$enabledMethods = array();
		$settings = array_keys($this->configuration);

		foreach ($settings as $setting) {
			if (substr($setting, -5) !== 'Check') continue;
			$enabledMethods[] = $setting;
		}

		return $enabledMethods;
	}

	/**
	 * Sets the session time if sessionCheck method is enabled in configuration.
	 */
	public function setSessionTime() {
		if ($this->isEnabledSpamProtectionMethod('sessionCheck')) {
			$sessionInstance = t3lib_div::makeInstance('tx_wtspamshield_method_session');
			$sessionInstance->setSessionTime();
		}
	}
} 
