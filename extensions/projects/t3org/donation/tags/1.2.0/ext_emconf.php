<?php

########################################################################
# Extension Manager/Repository config file for ext "donation".
#
# Auto generated 18-04-2011 11:46
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Donation',
	'description' => 'An extension  to make donations. Used on typo3.org',
	'category' => 'plugin',
	'author' => 'Ingo Renner',
	'author_email' => 'ingo@typo3.org',
	'shy' => '',
	'dependencies' => 'cms,pagebrowse',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.2.0',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'pagebrowse' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:64:{s:9:"ChangeLog";s:4:"25f3";s:12:"ext_icon.gif";s:4:"0620";s:17:"ext_localconf.php";s:4:"2d5f";s:14:"ext_tables.php";s:4:"ac10";s:14:"ext_tables.sql";s:4:"66ff";s:28:"icon_tx_donation_account.gif";s:4:"8af7";s:29:"icon_tx_donation_donation.gif";s:4:"0620";s:16:"locallang_db.xml";s:4:"5605";s:7:"tca.php";s:4:"f2ed";s:37:"classes/class.tx_donation_Account.php";s:4:"8fe7";s:44:"classes/class.tx_donation_AccountGateway.php";s:4:"301c";s:54:"classes/class.tx_donation_CobjUnavailableException.php";s:4:"e4da";s:38:"classes/class.tx_donation_Donation.php";s:4:"c8c7";s:46:"classes/class.tx_donation_HtmlTemplateView.php";s:4:"0e5e";s:62:"classes/class.tx_donation_LanguageFileUnavailableException.php";s:4:"5d25";s:48:"classes/class.tx_donation_MapCommandResolver.php";s:4:"63de";s:38:"classes/class.tx_donation_Registry.php";s:4:"2b23";s:43:"classes/class.tx_donation_SortableTable.php";s:4:"b8d8";s:45:"classes/class.tx_donation_donationFactory.php";s:4:"074a";s:56:"classes/viewHelpers/class.tx_donation_LinkViewHelper.php";s:4:"577b";s:55:"classes/viewHelpers/class.tx_donation_LllViewHelper.php";s:4:"9af6";s:57:"classes/viewHelpers/class.tx_donation_MoneyViewHelper.php";s:4:"e2c0";s:57:"classes/viewHelpers/class.tx_donation_Nl2brViewHelper.php";s:4:"f212";s:59:"classes/viewHelpers/class.tx_donation_TimeagoViewHelper.php";s:4:"6140";s:56:"classes/viewHelpers/class.tx_donation_WrapViewHelper.php";s:4:"178b";s:44:"interfaces/interface.tx_donation_Command.php";s:4:"311c";s:52:"interfaces/interface.tx_donation_CommandResolver.php";s:4:"e20f";s:47:"interfaces/interface.tx_donation_ViewHelper.php";s:4:"d985";s:47:"pi_donorlist/class.tx_donation_pi_donorlist.php";s:4:"05c6";s:25:"pi_donorlist/flexform.xml";s:4:"d894";s:26:"pi_donorlist/locallang.xml";s:4:"b345";s:29:"pi_donorlist/locallang_ff.xml";s:4:"a7d2";s:43:"pi_form/class.tx_donation_LogIpnCommand.php";s:4:"9af7";s:59:"pi_form/class.tx_donation_ShowBankAccountDetailsCommand.php";s:4:"ee4f";s:53:"pi_form/class.tx_donation_ShowBankwireFormCommand.php";s:4:"6655";s:48:"pi_form/class.tx_donation_ShowBucketsCommand.php";s:4:"dfa0";s:51:"pi_form/class.tx_donation_ShowPaypalFormCommand.php";s:4:"ae0a";s:37:"pi_form/class.tx_donation_pi_form.php";s:4:"b8a7";s:20:"pi_form/flexform.xml";s:4:"3258";s:21:"pi_form/locallang.xml";s:4:"2d67";s:24:"pi_form/locallang_ff.xml";s:4:"c0d4";s:33:"resources/donorlist/arrow-asc.png";s:4:"25bf";s:34:"resources/donorlist/arrow-desc.png";s:4:"957d";s:39:"resources/donorlist/style_donorlist.css";s:4:"ec32";s:42:"resources/donorlist/template_donorlist.htm";s:4:"cc22";s:25:"resources/form/pi_form.js";s:4:"9bca";s:29:"resources/form/style_form.css";s:4:"3e96";s:32:"resources/form/template_form.htm";s:4:"fcdc";s:37:"resources/form/images/btn_up_down.png";s:4:"23d2";s:42:"resources/form/images/donate-bucket-bg.png";s:4:"f691";s:35:"resources/form/images/donate-bw.png";s:4:"2612";s:38:"resources/form/images/donate-input.png";s:4:"6e19";s:35:"resources/form/images/donate-pp.png";s:4:"9806";s:39:"resources/form/images/donate-submit.png";s:4:"0d7c";s:32:"resources/prototype/prototype.js";s:4:"b568";s:34:"resources/scriptaculous/builder.js";s:4:"1174";s:35:"resources/scriptaculous/controls.js";s:4:"612b";s:35:"resources/scriptaculous/dragdrop.js";s:4:"87c1";s:34:"resources/scriptaculous/effects.js";s:4:"d795";s:40:"resources/scriptaculous/scriptaculous.js";s:4:"d59e";s:33:"resources/scriptaculous/slider.js";s:4:"4b10";s:32:"resources/scriptaculous/sound.js";s:4:"0f0f";s:35:"resources/scriptaculous/unittest.js";s:4:"9996";s:25:"static/donation/setup.txt";s:4:"cffb";}',
	'suggests' => array(
	),
);

?>