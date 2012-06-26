<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

/**
 * Configure the Plugin to call the
 * right combination of Controller and Action according to
 * the user input (default settings, FlexForm, URL etc.)
 */
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,																		// The extension name (in UpperCamelCase) or the extension key (in lower_underscore)
	'Pi1',																// A unique name of the plugin in UpperCamelCase
	array(																			// An array holding the controller-action-combinations that are accessible
		'Agency' => 'index,show,edit,list,update',
		'Reference' => 'index,filter,show,new,search,create,preview,remove,delete,edit,update,deactivate,reactivate,categories,sort',	// The first controller and its first action will be the default
		),
	array(																			// An array of non-cachable controller-action-combinations (they must already be enabled)
		'Agency' => '',
		'Reference' => 'create,filter',
		)
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Pi2',
	array (
		'Agency' => 'enterCode,verifyCode,new,create,enterInformation,updateNewAgency,enterApprovalData,sendApprovalData'
	),
	array(
		'Agency' => 'verifyCode,create,new,enterInformation,updateNewAgency,enterApprovalData,sendApprovalData'
	)
);


/**
 * Scheduler Cron Job
 */
$TYPO3_CONF_VARS['SC_OPTIONS']['scheduler']['tasks']['Tx_Typo3Agencies_Scheduler_UpdateMemberships'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang.xml:membershipUpdater.name',
	'description' => 'LLL:EXT:'.$_EXTKEY.'/Resources/Private/Language/locallang.xml:membershipUpdater.description',
);

?>