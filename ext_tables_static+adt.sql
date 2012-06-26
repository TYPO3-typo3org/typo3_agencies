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
INSERT INTO tx_typo3agencies_domain_model_industry (uid,pid,title) VALUES (99,329,'Other');


INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (1,329,'Intranet');
INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (2,329,'Internet');
INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (3,329,'Extranet');
INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (4,329,'Microsite');
INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (5,329,'Application');
INSERT INTO tx_typo3agencies_domain_model_category (uid,pid,title) VALUES (99,329,'Other');

INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (1,329,'unknown',0);
INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (2,329,'1-49 million',1);
INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (3,329,'50-199 million',50);
INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (4,329,'200-499 million',200);
INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (5,329,'500-999 million',500);
INSERT INTO tx_typo3agencies_domain_model_revenue (uid,pid,title,sorting) VALUES (6,329,'over 1 billion',999);

UPDATE tx_typo3agencies_domain_model_reference SET  industry=9, revenue=4 WHERE uid=1;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=9, revenue=5 WHERE uid=3;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=9, revenue=4 WHERE uid=6;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=7, revenue=2 WHERE uid=7;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=8, revenue=2 WHERE uid=9;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=3, revenue=2 WHERE uid=10;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=8, revenue=2 WHERE uid=11;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=9, revenue=5 WHERE uid=12;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=12, revenue = 6 WHERE uid=13;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=12, revenue = 6 WHERE uid=14;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=1, revenue = 5 WHERE uid=15;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=3, revenue = 2 WHERE uid=16;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=3, revenue = 2 WHERE uid=17;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=2, revenue = 2 WHERE uid=18;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=9, revenue = 5 WHERE uid=19;
UPDATE tx_typo3agencies_domain_model_reference SET  industry=8, revenue = 2 WHERE uid=20;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=21;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=22;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 5 WHERE uid=23;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=24;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=25;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=26;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=27;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=28;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=29;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=30;
UPDATE tx_typo3agencies_domain_model_reference SET  revenue = 2 WHERE uid=31;