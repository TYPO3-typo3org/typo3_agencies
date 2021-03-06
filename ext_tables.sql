#
# Table structure for table 'tx_typo3agencies_domain_model_reference'
#
CREATE TABLE tx_typo3agencies_domain_model_reference (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	link varchar(255) DEFAULT '' NOT NULL,
	pages tinyint(2) unsigned DEFAULT '0' NOT NULL,
	languages tinyint(3) unsigned DEFAULT '1' NOT NULL,
	category tinyint(2) unsigned DEFAULT '0' NOT NULL,
	category_other varchar(50) DEFAULT '' NOT NULL,
	tags varchar(100) DEFAULT '' NOT NULL,
	industry tinyint(2) unsigned DEFAULT '0' NOT NULL,
	industry_other varchar(50) DEFAULT '' NOT NULL,
	screenshot varchar(50) DEFAULT '' NOT NULL,
	screenshot_gallery varchar(300) DEFAULT '' NOT NULL,
	casestudy varchar(50) DEFAULT '' NOT NULL,
	agency int(11) DEFAULT '0' NOT NULL,
	conclusion text NOT NULL,
	about text NOT NULL,
	size tinyint(2) unsigned DEFAULT '0' NOT NULL,
	revenue tinyint(2) unsigned DEFAULT '0' NOT NULL,
	country varchar(50) DEFAULT '' NOT NULL,
	listed tinyint(1) unsigned DEFAULT '0' NOT NULL,
	deactivated tinyint(1) unsigned DEFAULT '0' NOT NULL,


	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sorting int(3) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_typo3agencies_domain_model_agency'
#
CREATE TABLE tx_typo3agencies_domain_model_agency (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	name varchar(255) DEFAULT '' NOT NULL,
	about text NOT NULL,
	link varchar(255) DEFAULT '' NOT NULL,
	logo varchar(50) DEFAULT '' NOT NULL,

	related_member int(11) DEFAULT '0' NOT NULL,
	email varchar(100) DEFAULT '' NOT NULL,
	address varchar(255) DEFAULT '' NOT NULL,
	zip varchar(50) DEFAULT '' NOT NULL,
	city varchar(100) DEFAULT '' NOT NULL,
	country varchar(100) DEFAULT '' NOT NULL,
	contact varchar(100) DEFAULT '' NOT NULL,
	salutation varchar(20) DEFAULT '' NOT NULL,
	first_name varchar(100) DEFAULT '' NOT NULL,
	last_name varchar(100) DEFAULT '' NOT NULL,

	member tinyint(4) unsigned DEFAULT '0' NOT NULL,
	approved tinyint(4) unsigned DEFAULT '0' NOT NULL,
	casestudies tinyint(4) unsigned DEFAULT '0' NOT NULL,
	code varchar(100) DEFAULT '' NOT NULL,
	training_service tinyint(1) unsigned DEFAULT '0' NOT NULL,
	hosting_service tinyint(1) unsigned DEFAULT '0' NOT NULL,
	development_service tinyint(1) unsigned DEFAULT '0' NOT NULL,
	latitude decimal(24,14) DEFAULT '0.00000000000000' NOT NULL,
	longitude decimal(24,14) DEFAULT '0.00000000000000' NOT NULL,

	administrator int(11) unsigned DEFAULT '0' NOT NULL,
	payed_until_date int(11) unsigned DEFAULT '0' NOT NULL,
	next_review_date int(11) unsigned DEFAULT '0' NOT NULL,

	internal_comment text NOT NULL,

	referenceses int(11) unsigned DEFAULT '0' NOT NULL,

	own_page_t3ver varchar(255) DEFAULT '' NOT NULL,
	certified_integrators int(11) unsigned DEFAULT '0' NOT NULL,
	published_extkeys varchar(255) DEFAULT '' NOT NULL,
	financial_involvement text NOT NULL,
	active_involvement text NOT NULL,
	last_t3pages text NOT NULL,
	online_references varchar(255) DEFAULT '' NOT NULL,

	proof_own_page_t3ver tinyint(4) unsigned DEFAULT '0' NOT NULL,
	proof_certified_integrators tinyint(4) unsigned DEFAULT '0' NOT NULL,
	proof_published_extkeys tinyint(4) unsigned DEFAULT '0' NOT NULL,
	proof_financial_involvement tinyint(4) unsigned DEFAULT '0' NOT NULL,
	proof_active_involvement tinyint(4) unsigned DEFAULT '0' NOT NULL,
	proof_online_references tinyint(4) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_typo3agencies_domain_model_industry (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid),
	UNIQUE uid (uid)
);

CREATE TABLE tx_typo3agencies_domain_model_revenue (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	sorting int(3) unsigned DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	UNIQUE uid (uid)
);

CREATE TABLE tx_typo3agencies_domain_model_category (
	uid int(11) unsigned NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid),
	UNIQUE uid (uid)
);


