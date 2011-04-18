<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_donation_donation'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate DESC",
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_donation_donation.gif',
		'dividers2tabs'     => true,
	),
);

$TCA['tx_donation_account'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account',
		'label'     => 'name',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY name",
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_donation_account.gif',
	),
);

t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi_form'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi_form'] = 'layout,select_key,pages,recusive';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi_form', 'FILE:EXT:donation/pi_form/flexform.xml');

t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:donation/locallang_db.xml:tt_content.list_type_pi_form',
		$_EXTKEY.'_pi_form'
	),
	'list_type'
);


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi_donorlist'] = 'pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi_donorlist'] = 'layout,select_key,pages,recusive';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi_donorlist', 'FILE:EXT:donation/pi_donorlist/flexform.xml');

t3lib_extMgm::addPlugin(
	array(
		'LLL:EXT:donation/locallang_db.xml:tt_content.list_type_pi_donorlist',
		$_EXTKEY.'_pi_donorlist'
	),
	'list_type'
);


t3lib_extMgm::addStaticFile($_EXTKEY,'static/donation/', 'Donation');

?>