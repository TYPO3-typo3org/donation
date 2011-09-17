<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_donation_donation'] = array (
	'ctrl' => $TCA['tx_donation_donation']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,company,address,address_street,address_zip,address_city,address_state,address_country,address_country_code,email,amount,fee,currency,url,comment,paypal_txn_id,feuser,account'
	),
	'feInterface' => $TCA['tx_donation_donation']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'company' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.company',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'address' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address',
			'config' => array (
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '30',
				'rows' => '3',
			)
		),
		'address_street' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_street',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'address_zip' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_zip',
			'config' => array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'trim',
			)
		),
		'address_city' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_city',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'address_state' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_state',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'address_country' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_country',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'address_country_code' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.address_country_code',
			'config' => array (
				'type' => 'input',
				'size' => '5',
				'eval' => 'trim',
			)
		),
		'email' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.email',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'amount' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.amount',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,double2',
			)
		),
		'fee' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.fee',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2',
			)
		),
		'currency' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.currency',
			'config' => array (
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => 'required,trim',
			)
		),
		'url' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.url',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'comment' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.comment',
			'config' => array (
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '30',
				'rows' => '3',
			)
		),
		'paypal_txn_id' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.paypal_txn_id',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'feuser' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.feuser',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'fe_users',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'account' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_donation.account',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'tx_donation_account',
				'size' => 1,
				'minitems' => 1,
				'maxitems' => 1,
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden, name;;;;1-1-1, company, address, address_street, address_zip, address_city, address_state, address_country, address_country_code, url;;;;1-1-1, email, --div--;Donation, amount, fee, currency, --div--;Misc, comment, paypal_txn_id, feuser, account')
	),
	'palettes' => array ()
);



$TCA['tx_donation_account'] = array (
	'ctrl' => $TCA['tx_donation_account']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,name,image,description,bank_account,email_notification,email_paypal'
	),
	'feInterface' => $TCA['tx_donation_account']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.name',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'image' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.image',
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,jpg,jpeg,tif,bmp,pcx,tga,png,pdf,ai',
				'max_size' => 10240,
				'uploadfolder' => 'uploads/pics',
				'show_thumbs' => 1,
				'size' => 1,
				'maxitems' => 1,
				'minitems' => 0
			)
		),
		'bank_account' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.bank_account',
			'config' => array (
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'description' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.description',
			'config' => array (
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'email_notification' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.email_notification',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'email_paypal' => array (
			'exclude' => 0,
			'label' => 'LLL:EXT:donation/locallang_db.xml:tx_donation_account.email_paypal',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'trim',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;;;1-1-1, name, image,description, bank_account, email_notification, email_paypal')
	),
	'palettes' => array ()
);

?>