<?php
$extensionClassesPath = t3lib_extMgm::extPath('typo3_agencies') . 'Classes/';
return array(
	'tx_typo3agencies_domain_model_reference' => $extensionClassesPath . 'Domain/Model/Reference.php',
	'tx_typo3agencies_domain_model_agency' => $extensionClassesPath . 'Domain/Model/Agency.php',
	'tx_typo3agencies_domain_model_filter' => $extensionClassesPath . 'Domain/Model/Filter.php',
	'tx_typo3agencies_domain_model_pager' => $extensionClassesPath . 'Domain/Model/Pager.php',
	'tx_typo3agencies_domain_model_country' => $extensionClassesPath . 'Domain/Model/Country.php',
	'tx_typo3agencies_domain_model_industry' => $extensionClassesPath . 'Domain/Model/Industry.php',
	'tx_typo3agencies_domain_repository_referencerepository' => $extensionClassesPath . 'Domain/Repository/ReferenceRepository.php',
	'tx_typo3agencies_domain_repository_agencyrepository' => $extensionClassesPath . 'Domain/Repository/AgencyRepository.php',
	'tx_typo3agencies_domain_repository_countryrepository' => $extensionClassesPath . 'Domain/Repository/CountryRepository.php',
	'tx_typo3agencies_domain_repository_industryrepository' => $extensionClassesPath . 'Domain/Repository/IndustryRepository.php',
	'tx_typo3agencies_controller_referencecontroller' => $extensionClassesPath . 'Controller/ReferenceController.php',
	'tx_typo3agencies_controller_agencycontroller' => $extensionClassesPath . 'Controller/AgencyController.php',
	'tx_typo3agencies_viewhelpers_haserrorviewhelper' => $extensionClassesPath . 'ViewHelpers/HasErrorViewHelper.php',
	'tx_typo3agencies_viewhelpers_imageviewhelper' => $extensionClassesPath . 'ViewHelpers/ImageViewHelper.php',

	'tx_typo3agencies_scheduler_updatememberships' => $extensionClassesPath . 'Scheduler/UpdateMemberships.php',
);
?>