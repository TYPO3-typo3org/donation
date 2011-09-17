#
# Table structure for table 'tx_donation_donation'
#
CREATE TABLE tx_donation_donation (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	company varchar(255) DEFAULT '' NOT NULL,
	address text,
	address_street varchar(255) DEFAULT '' NOT NULL,
	address_zip varchar(10) DEFAULT '' NOT NULL,
	address_city varchar(255) DEFAULT '' NOT NULL,
	address_state varchar(100) DEFAULT '' NOT NULL,
	address_country varchar(255) DEFAULT '' NOT NULL,
	address_country_code varchar(5) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	amount double(11,2) DEFAULT '0.00' NOT NULL,
	fee double(11,2) DEFAULT '0.00' NOT NULL,
	currency varchar(3) DEFAULT '' NOT NULL,
	url varchar(255) DEFAULT '' NOT NULL,
	comment text,
	feuser text,
	paypal_txn_id varchar(255) DEFAULT '' NOT NULL,
	account text,

	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_donation_account'
#
CREATE TABLE tx_donation_account (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	image text,
	bank_account text,
	description text,
	email_notification varchar(255) DEFAULT '' NOT NULL,
	email_paypal varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);