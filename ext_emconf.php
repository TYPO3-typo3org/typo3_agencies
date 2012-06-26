<?php

########################################################################
# Extension Manager/Repository config file for ext "typo3_agencies".
#
# Auto generated 17-04-2011 22:52
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Agency extension for typo3.org',
	'description' => 'Displayes TYPO3 agencies and references',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.1.0',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_typo3_agencies/pics',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Mario Matzulla, Fabien Udriot',
	'author_email' => 'mario@matzullas.de, fabien.udriot@typo3.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.4.0-4.5.99',
			'extbase' => '1.3.0-0.0.0',
			'fluid' => '1.3.0-0.0.0',
			'static_info_tables' => '2.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:37:{s:16:"ext_autoload.php";s:4:"38d0";s:12:"ext_icon.gif";s:4:"e922";s:17:"ext_localconf.php";s:4:"5d18";s:14:"ext_tables.php";s:4:"336f";s:14:"ext_tables.sql";s:4:"3f7e";s:39:"Classes/Controller/AgencyController.php";s:4:"0b5d";s:42:"Classes/Controller/ReferenceController.php";s:4:"f7da";s:31:"Classes/Domain/Model/Agency.php";s:4:"27ae";s:31:"Classes/Domain/Model/Filter.php";s:4:"5353";s:30:"Classes/Domain/Model/Pager.php";s:4:"5601";s:34:"Classes/Domain/Model/Reference.php";s:4:"1daf";s:46:"Classes/Domain/Repository/AgencyRepository.php";s:4:"2045";s:49:"Classes/Domain/Repository/ReferenceRepository.php";s:4:"52a1";s:25:"Configuration/TCA/tca.php";s:4:"d166";s:38:"Configuration/TypoScript/constants.txt";s:4:"6b7e";s:34:"Configuration/TypoScript/setup.txt";s:4:"a2d5";s:40:"Resources/Private/Language/locallang.xml";s:4:"8e68";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"06c4";s:38:"Resources/Private/Layouts/default.html";s:4:"04c5";s:42:"Resources/Private/Partials/formErrors.html";s:4:"cc71";s:41:"Resources/Private/Partials/reference.html";s:4:"c897";s:48:"Resources/Private/Partials/referenceActions.html";s:4:"dafd";s:44:"Resources/Private/Templates/Agency/edit.html";s:4:"a319";s:44:"Resources/Private/Templates/Agency/list.html";s:4:"5c4f";s:44:"Resources/Private/Templates/Agency/show.html";s:4:"b6d6";s:53:"Resources/Private/Templates/Reference/categories.html";s:4:"e91b";s:53:"Resources/Private/Templates/Reference/categories.json";s:4:"b923";s:49:"Resources/Private/Templates/Reference/create.html";s:4:"c64e";s:49:"Resources/Private/Templates/Reference/delete.html";s:4:"a83b";s:47:"Resources/Private/Templates/Reference/edit.html";s:4:"917c";s:48:"Resources/Private/Templates/Reference/index.html";s:4:"e7a4";s:46:"Resources/Private/Templates/Reference/new.html";s:4:"c86d";s:47:"Resources/Private/Templates/Reference/show.html";s:4:"767d";s:28:"Resources/Public/premium.png";s:4:"6cdb";s:68:"Resources/Public/Icons/icon_tx_typo3agencies_domain_model_agency.gif";s:4:"e922";s:71:"Resources/Public/Icons/icon_tx_typo3agencies_domain_model_reference.gif";s:4:"d050";s:14:"doc/manual.sxw";s:4:"5f9b";}',
	'suggests' => array(
	),
);

?>