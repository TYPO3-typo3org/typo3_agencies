<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_typo3agencies_domain_model_revenue'] = array(
	'ctrl' => $TCA['tx_typo3agencies_domain_model_revenue']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'title'
	),
	'columns' => array(
		'title' => array(
			'exclude' => 0,
			'label'   => 'LLL:EXT:typo3_agencies/Resources/Private/Language/locallang_db.xml:tx_typo3agencies_domain_model_revenue.title',
			'config'  => array(
				'type' => 'input',
				'size' => 20,
				'eval' => 'trim',
				'max'  => 255
			)
		),
	),
	'types' => array(
		'1' => array('showitem' => 'title')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);

?>