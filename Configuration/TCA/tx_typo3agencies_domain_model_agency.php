<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_typo3agencies_domain_model_agency'] = array(
	'ctrl' => $TCA['tx_typo3agencies_domain_model_agency']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'name,logo,about'
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
				'foreign_table' => 'tx_typo3agencies_domain_model_agency',
				'foreign_table_where' => 'AND tx_typo3agencies_domain_model_agency.uid=###REC_FIELD_l18n_parent### AND tx_typo3agencies_domain_model_agency.sys_language_uid IN (-1,0)',
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
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'related_member' => Array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.related_member',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'allowed' => 'tx_t3omembership_domain_model_member',
				'wizards' => Array(
					'suggest' => array(
						'type' => 'suggest'
					)
				),
			)
		),
		'name' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.name',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 255
			)
		),
		'about' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.about',
			'config'  => array(
				'type' => 'text',
				'eval' => 'required',
				'rows' => 30,
				'cols' => 80,
			)
		),
		'salutation' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.salutation',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 20
			)
		),
		'first_name' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.first_name',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 100
			)
		),
		'last_name' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.last_name',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 100
			)
		),
		'email' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.email',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 100
			)
		),
		'address' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.address',
			'config'  => array(
				'type' => 'text',
				'eval' => '',
				'rows' => 30,
				'cols' => 80,
				'max'  => 255
			)
		),
		'zip' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.zip',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 50
			)
		),
		'city' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.city',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 100
			)
		),
		'latitude' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.latitude',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			)
		),
		'longitude' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.longitude',
			'config' => Array (
				'type' => 'input',
				'size' => '20',
				'eval' => 'trim',
				'max' => '30',
				'default' => '0.00000000000000'
			)
		),
		'country' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.country',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 100
			)
		),
		'contact' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.contact',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 100
			)
		),
		'link' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.link',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim,required',
				'max'  => 255
			)
		),
		'training_service' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.training_service',
			'config'  => array(
				'type' => 'check',
			)
		),
		'hosting_service' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.hosting_service',
			'config'  => array(
				'type' => 'check',
			)
		),
		'development_service' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.development_service',
			'config'  => array(
				'type' => 'check',
			)
		),
		'logo' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.logo',
			'config'  => array(
				'type'          => 'group',
				'internal_type' => 'file',
				'allowed'       => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size'      => 500,
				'uploadfolder'  => 'uploads/tx_typo3_agencies/pics',
				'show_thumbs'   => 1,
				'size'          => 1,
				'maxitems'      => 1,
				'minitems'      => 0
			)
		),
		'administrator' => Array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.administrator',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'db',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
				'eval' => 'required',
				'allowed' => 'fe_users',
				'wizards' => Array(
					'suggest' => array(
						'type' => 'suggest'
					)
				),
			)
		),
		'member' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member',
			'config'  => array(
				'type' => 'select',
				'size' => '1',
				'minitems' => 1,
				'maxitems' => 1,
				'items' => Array(
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member',''),
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member.4', '4'), #Platenium
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member.3', '3'), #Gold
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member.2', '2'), #Silver
					Array('LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.member.1', '1'), #Bronze
				)
			)
		),
		'approved' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.approved',
			'config'  => array(
				'type' => 'check',
			)
		),
		'casestudies' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.casestudies',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 3
			)
		),
		'code' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.code',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 100
			)
		),
		'internal_comment' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.internal_comment',
			'config'  => array(
				'type' => 'text',
				'rows' => 10,
				'cols' => 80,
			)
		),
		'payed_until_date' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.payed_until_date',
			'config'  => array(
				'type' => 'input',
				'eval' => 'date',
				'size' => 10,
			)
		),
		'next_review_date' => array(
			'exclude' => 1,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.next_review_date',
			'config'  => array(
				'type' => 'input',
				'eval' => 'date',
				'size' => 10,
			)
		),
		'referenceses' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.references',
			'config' => array(
				'type' => 'inline',
				'allowed' => 'tx_typo3agencies_domain_model_reference',
				'foreign_table' => 'tx_typo3agencies_domain_model_reference',
				'foreign_field' => 'agency',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 20,
				'appearance' => array(
					'collapseAll' => 1,
					'expandSingle' => 1,
					'levelLinksPosition' => 'bottom',
				)
			)
		),
		'own_page_t3ver' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.own_page_t3ver',
			'config' => array(
				'type' => 'input',
				'size' => 10,
			)
		),
		'certified_integrators' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.certified_integrators',
			'config' => array(
				'type' => 'input',
				'size' => 10,
			)
		),
		'published_extkeys' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.published_extkeys',
			'config' => array(
				'type' => 'input',
				'size' => 10,
			)
		),
		'financial_involvement' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.financial_involvement',
			'config' => array(
				'type' => 'text',
				'size' => 10,
			)
		),
		'active_involvement' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.active_involvement',
			'config' => array(
				'type' => 'text',
				'size' => 10,
			)
		),
		'last_t3pages' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.last_t3pages',
			'config' => array(
				'type' => 'text',
				'size' => 10,
			)
		),
		'online_references' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.online_references',
			'config' => array(
				'type' => 'text',
				'size' => 10,
			)
		),
		'proof_own_page_t3ver' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_own_page_t3ver',
			'config' => array(
				'type' => 'check',
			)
		),
		'proof_certified_integrators' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_certified_integrators',
			'config' => array(
				'type' => 'check',
			)
		),
		'proof_published_extkeys' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_published_extkeys',
			'config' => array(
				'type' => 'check',
			)
		),
		'proof_financial_involvement' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_financial_involvement',
			'config' => array(
				'type' => 'check',
			)
		),
		'proof_active_involvement' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_active_involvement',
			'config' => array(
				'type' => 'check',
			)
		),
		'proof_online_references' => array(
			'label' => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.proof_online_references',
			'config' => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => '
				--div--;LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.generaltab,
				sys_language_uid, hidden, related_member, logo, about, size,
				training_service, hosting_service, development_service, latitude, longitude,
				--div--;LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.internaltab,
				approved, administrator, casestudies, payed_until_date, next_review_date, internal_comment, code, --palette--;Proof;proof,
				--div--;LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.assoctab,
				member, name, address, zip, city, country, link, email, contact, endtime,
				--div--;LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_agency.reftab,
				referenceses
			'
		)
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
		'proof' => array('showitem' => 'proof_own_page_t3ver, --linebreak--, proof_certified_integrators, --linebreak--, proof_published_extkeys, --linebreak--, proof_financial_involvement, --linebreak--, proof_active_involvement, --linebreak--, proof_last_t3pages, --linebreak--, proof_online_references', 'canNotCollapse' => 1)
	)
);
