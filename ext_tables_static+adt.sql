#
# Table structure for table 'tx_typo3agencies_domain_model_industry'
#
DROP TABLE IF EXISTS tx_typo3agencies_domain_model_industry
CREATE TABLE tx_typo3agencies_domain_model_industry (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid),
	UNIQUE uid (uid)
);

INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (0,329,'Industry');
UPDATE tx_typo3agencies_domain_model_industry SET uid = 0 WHERE uid = 1;
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (1,329,'Automotive');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (2,329,'Insurance and Finance');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (3,329,'Services');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (4,329,'NGO');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (5,329,'Financial Services');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (6,329,'Governmental');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (7,329,'Healthcare and Pharma');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (8,329,'Industry and Manufacturing');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (9,329,'IT and Telecomunication');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (10,329,'Media and Communication');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (11,329,'Social');
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (12,329,'Tourism');