<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

/**
 * The agency controller for the Reference package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Controller_BaseController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_AgencyRepository
	 */
	protected $agencyRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_ReferenceRepository
	 */
	public $referenceRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_CountryRepository
	 */
	protected $countryRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Model_Administrator
	 */
	protected $administrator;
	/**
	 * @var Tx_Typo3Agencies_Domain_Model_Agency
	 */
	protected $agency;

	/**
	 * @var Tx_Typo3Agencies_Domain_Model_Filter
	 */
	protected $filter;

	/**
	 * @var Tx_Extbase_Utility_Localization
	 */
	public $localization;
	
	/**
	 * @var boolean Show deactivated references
	 */
	public $showDeactivated;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->agencyRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_AgencyRepository');
		$this->referenceRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_ReferenceRepository');
		$this->localization = t3lib_div::makeInstance('Tx_Extbase_Utility_Localization');
		$this->countryRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_CountryRepository');
		$this->showDeactivated = false;
		if($GLOBALS['TSFE']->loginUser){
			$uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
			$result = $this->agencyRepository->findByAdministrator($uid);
			if(count($result) > 0){
				$this->administrator = $uid;
				$this->agency = $result->getFirst();
				if($this->agency->getAdministrator() == $this->administrator){
					$this->showDeactivated = true;
				}
			}
		}
	}
	
	protected function createFilterObject(array $filter = null) {
		if($filter == null){
			return null;
		}
		$filterObject = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter');
		if(!empty($filter)){
			if($filter['location'] != ''){
				$filterObject->setLocation($filter['location']);
			}
			if($filter['country']){
				$filterObject->setCountry($filter['country']);
			}
			if($filter['member']){
				$filterObject->setMember($filter['member']);
			}
			if($filter['category']){
				$filterObject->setCategory($filter['category']);
			}
			if($filter['industry']){
				$filterObject->setIndustry($filter['industry']);
			}
			if($filter['companySize']){
				$filterObject->setCompanySize($filter['companySize']);
			}
			$filterObject->setTrainingService($filter['trainingService']);
			$filterObject->setHostingService($filter['hostingService']);
			$filterObject->setDevelopmentService($filter['developmentService']);
		}

		if($this->administrator > 0) {
			$filterObject->setFeUser($this->administrator);
		}
		
		// Process member value
		$members = t3lib_div::trimExplode(',', $filterObject->getMember(),1);
		foreach ($members as $member) {
			$filterObject->addMember($member);
		}
		return $filterObject;
	}
	
	protected function addCountries(){
		$countries = $this->countryRepository->findAll();
		$availableCountries = Array();
		foreach($countries as $country){
			$availableCountries[$country->getCnIso2()] = $country->getCnShortEn();
		}
		uasort($availableCountries, create_function('$a,$b', 'return strcasecmp($a, $b);'));
		$this->view->assign('countries', $availableCountries);
	}

	static function getCategories(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('category',$extensionName),
					1 => $ref->localization->translate('category1',$extensionName),
					2 => $ref->localization->translate('category2',$extensionName),
					3 => $ref->localization->translate('category3',$extensionName),
					4 => $ref->localization->translate('category4',$extensionName),
					5 => $ref->localization->translate('category5',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	static function getIndustries(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('industry',$extensionName),
					1 => $ref->localization->translate('industry1',$extensionName),
					2 => $ref->localization->translate('industry2',$extensionName),
					3 => $ref->localization->translate('industry3',$extensionName),
					4 => $ref->localization->translate('industry4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	static function getCompanySizes(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('size',$extensionName),
					1 => $ref->localization->translate('size1',$extensionName),
					2 => $ref->localization->translate('size2',$extensionName),
					3 => $ref->localization->translate('size3',$extensionName),
					4 => $ref->localization->translate('size4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	static function getPages(&$ref, $extensionName, $includeDescription = true){
		$values = Array(0 => $ref->localization->translate('page',$extensionName),
					1 => $ref->localization->translate('page1',$extensionName),
					2 => $ref->localization->translate('page2',$extensionName),
					3 => $ref->localization->translate('page3',$extensionName),
					4 => $ref->localization->translate('page4',$extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	static function getLanguages(&$ref, $extensionName, $includeDescription = true){
		$values = Array();
		for($i=1;$i<10;$i++){
			$values[$i] = $i;
		}
		return $values;
	}
	
	static function removeNotSet(&$ref, &$request, &$allowedCategories, &$allowedIndustries, &$allowedCompanySizes){
		$category = 0;
		if($request->hasArgument('category')){
			$category = intval($request->getArgument('category')); // 5
		}
		$industry = 0;
		if($request->hasArgument('industry')){
			$industry = intval($request->getArgument('industry')); // 4
		}
		$companySize = 0;
		if($request->hasArgument('companySize')){
			$companySize = intval($request->getArgument('companySize')); // 4
		}
		
		$remove = Array();
		for($i=1;$i<count($allowedCategories); $i++){
			$count = $ref->referenceRepository->countByOption($i,$industry,$companySize,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCategories = array_diff_key($allowedCategories,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedIndustries); $i++){
			$count = $ref->referenceRepository->countByOption($category,$i,$companySize,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedIndustries = array_diff_key($allowedIndustries,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedCompanySizes); $i++){
			$count = $ref->referenceRepository->countByOption($category,$industry,$i,$ref->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCompanySizes = array_diff_key($allowedCompanySizes,$remove);
	}
}

?>