<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');
/**
 * Registers a Plugin to be listed in the Backend. You also have to configure the Dispatcher in ext_localconf.php.
 */
$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY,
	'Pi1',
	'List of Agencies'
);

Tx_Extbase_Utility_Extension::registerPlugin(
	$_EXTKEY, 
	'Pi2', 
	'Create agency profile'
);

		
$TCA['tt_content']['types']['list']['subtypes_addlist']['typo3agencies_pi2'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue('typo3agencies_pi2', 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/Pi2.xml');  

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'layout,select_key,recursive';
#$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'recursive';


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'TYPO3 References');

/**
 * TCA configuration
 */
t3lib_extMgm::allowTableOnStandardPages('tx_typo3agencies_domain_model_reference');
t3lib_extMgm::allowTableOnStandardPages('tx_typo3agencies_domain_model_agency');

$TCA['tx_typo3agencies_domain_model_reference'] = array (
	'ctrl' => array (
		'title'             => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference',
		'label' 			=> 'title',
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> true,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'dividers2tabs' => TRUE,
		'enablecolumns' 	=> array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tx_typo3agencies_domain_model_reference.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_typo3agencies_domain_model_reference.gif'
	)
);

$TCA['tx_typo3agencies_domain_model_agency'] = Array (
	'ctrl' => Array (
		'title'             => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency',
		'label' 			=> 'name',
		'label_alt'			=> 'first_name',
		'label_alt_force'	=> 1,
		'tstamp' 			=> 'tstamp',
		'crdate' 			=> 'crdate',
		'default_sortby' 	=> 'name',
		'versioningWS' 		=> 2,
		'versioning_followPages'	=> true,
		'origUid' 			=> 't3_origuid',
		'languageField' 	=> 'sys_language_uid',
		'transOrigPointerField' 	=> 'l18n_parent',
		'transOrigDiffSourceField' 	=> 'l18n_diffsource',
		'delete' 			=> 'deleted',
		'dividers2tabs' => TRUE,
		'enablecolumns' 	=> Array(
			'disabled' => 'hidden'
			),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tx_typo3agencies_domain_model_agency.php',
		'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_typo3agencies_domain_model_agency.gif'
	)
);

$TCA['tx_typo3agencies_domain_model_industry'] = Array (
		'ctrl' => Array (
				'title'             => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry',
				'label' 			=> 'title',
				'default_sortby' 	=> 'title',
				'dividers2tabs' => TRUE,
				'enablecolumns' 	=> Array(
				),
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tx_typo3agencies_domain_model_industry.php',
				'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_typo3agencies_domain_model_agency.gif'
		)
);

$TCA['tx_typo3agencies_domain_model_revenue'] = Array (
		'ctrl' => Array (
				'title'             => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.revenue',
				'label' 			=> 'title',
				'default_sortby' 	=> 'sorting',
				'dividers2tabs' => TRUE,
				'enablecolumns' 	=> Array(
				),
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tx_typo3agencies_domain_model_revenue.php',
				'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_typo3agencies_domain_model_agency.gif'
		)
);

$TCA['tx_typo3agencies_domain_model_category'] = Array (
		'ctrl' => Array (
				'title'             => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category',
				'label' 			=> 'title',
				'default_sortby' 	=> 'title',
				'dividers2tabs' => TRUE,
				'enablecolumns' 	=> Array(
				),
				'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'Configuration/TCA/tx_typo3agencies_domain_model_category.php',
				'iconfile' 			=> t3lib_extMgm::extRelPath($_EXTKEY) . 'Resources/Public/Icons/icon_tx_typo3agencies_domain_model_agency.gif'
		)
);

$extensionName = t3lib_div::underscoredToUpperCamelCase($_EXTKEY);
$pluginSignature = strtolower($extensionName) . '_pi1';  
 
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($pluginSignature, 'FILE:EXT:'.$_EXTKEY.'/Configuration/FlexForms/ControllerActions.xml');  

?>