<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_typo3agencies_domain_model_reference'] = array(
	'ctrl' => $TCA['tx_typo3agencies_domain_model_reference']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'deactivated,title,description,link,pages,languages,category,category_other,tags,industry,industry_other,screenshot,screeshot_gallery,casestudy,conclusion,about,size,country,listed'
	),
	'columns' => array(
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_typo3agencies_domain_model_reference',
				'foreign_table_where' => 'AND tx_typo3agencies_domain_model_reference.uid=###REC_FIELD_l18n_parent### AND tx_typo3agencies_domain_model_reference.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array(
			'config'=>array(
				'type'=>'passthrough'
			)
		),
		't3ver_label' => Array (
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => Array (
				'type'=>'none',
				'cols' => 27
			)
		),
		'hidden' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type' => 'check'
			)
		),
		'deactivated' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.deactivated',
			'config'  => array(
				'type' => 'check',
			)
		),
		'title' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.title',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'description' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.description',
			'config'  => array(
				'type' => 'text',
				'eval' => 'required',
				'rows' => 30,
				'cols' => 80,
			)
		),
		'link' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.link',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'pages' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.pages',
			'config'  => array(
				'type' => 'select',
				'size' => '1',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => Array(
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.select',0),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.pages0',1),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.pages1',2),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.pages2',3),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.pages3',4)
				)
			)
		),
		'languages' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.languages',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'category' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category',
			'config'  => array(
				'type' => 'select',
				'size' => '1',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => Array(
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.select',0),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category0',1),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category1',2),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category2',3),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category3',4),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category4',5)
				)
			)
		),
		'category_other' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.category_other',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 256
			)
		),
		'tags' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.tags',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 256
			)
		),
		'industry' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry',
			'config'  => array(
				'type' => 'select',
				'size' => '1',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => Array(
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.select',0),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry0',1),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry1',2),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry2',3),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry3',4)
				)
			)
		),
		'industry_other' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.industry_other',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 256
			)
		),
		'screenshot' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.screenshot',
			'config'  => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => 3000,
				'uploadfolder'  => 'uploads/tx_typo3_agencies/pics',
				'show_thumbs'   => 1,
				'size'          => 1,
				'maxitems'      => 1,
				'minitems'      => 0
			)
		),
		'screenshot_gallery' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.screenshot_gallery',
			'config'  => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => 3000,
				'uploadfolder'  => 'uploads/tx_typo3_agencies/pics',
				'show_thumbs'   => 1,
				'size'          => 3,
				'maxitems'      => 3,
				'minitems'      => 0
			)
		),
		'casestudy' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.casestudy',
			'config'  => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => 'pdf',
				'max_size'      => 3000,
				'uploadfolder'  => 'uploads/tx_typo3_agencies/pics',
				'show_thumbs'   => 1,
				'size'          => 1,
				'maxitems'      => 1,
				'minitems'      => 0
			)
		),
		'agency' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.agency',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'allowed' => 'tx_typo3agencies_domain_model_agency',
				'wizards' => Array(
					'suggest' => array(
						'type' => 'suggest'
					)
				),
			)
		),
		'conclusion' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.conclusion',
			'config'  => array(
				'type' => 'text',
				'rows' => 30,
				'cols' => 80,
			)
		),
		'about' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.about',
			'config'  => array(
				'type' => 'text',
				'eval' => 'required',
				'rows' => 30,
				'cols' => 80,
			)
		),
		'size' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.size',
			'config'  => array(
				'type' => 'select',
				'size' => '1',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => Array(
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.size0',1),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.size1',2),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.size2',3),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.size3',4)
				)
			)
		),
		'country' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.country',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 256
			)
		),
		'listed' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_reference.listed',
			'config'  => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid, hidden, deactivated, agency, title,description,link,pages,languages,category,category_other,tags,industry,industry_other,screenshot,screenshot_gallery,casestudy,conclusion,about,size,country,listed')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



?>