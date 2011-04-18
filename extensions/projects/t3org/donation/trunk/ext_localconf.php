<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$PATH_donation = t3lib_extMgm::extPath('donation');

t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_donation_donation = 1
');

t3lib_extMgm::addPItoST43(
	$_EXTKEY,
	'pi_form/class.tx_donation_pi_form.php',
	'_pi_form',
	'list_type',
	false
);

t3lib_extMgm::addPItoST43(
	$_EXTKEY,
	'pi_donorlist/class.tx_donation_pi_donorlist.php',
	'_pi_donorlist',
	'list_type',
	true
);


?>