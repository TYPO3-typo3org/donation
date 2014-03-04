<?php

$extensionPath = t3lib_extMgm::extPath('donation');

return array(
	'tx_donation_command' => $extensionPath . 'interfaces/interface.tx_donation_Command.php',
	'tx_donation_abstractcommand' => $extensionPath . 'pi_form/class.tx_donation_AbstractCommand.php',
	'tx_donation_service_spamprotection' => $extensionPath . 'classes/service/class.tx_donation_service_SpamProtection.php'
);
