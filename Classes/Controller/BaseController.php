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
	protected  $referenceRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_CountryRepository
	 */
	protected $countryRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_IndustryRepository
	 */
	protected $industryRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_CategoryRepository
	 */
	protected $categoryRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_RevenueRepository
	 */
	protected $revenueRepository;
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
	protected  $localization;
	
	/**
	 * @var boolean Show deactivated references
	 */
	protected  $showDeactivated;

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
		$this->industryRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_IndustryRepository');
		$this->categoryRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_CategoryRepository');
		$this->revenueRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_RevenueRepository');
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
	
	protected function addFilterOptions(){
		$allowedCategories = $this->getCategories();
		$allowedIndustries = $this->getIndustries();
		$allowedRevenues = $this->getRevenues();
	
		$this->removeNotSet($this->request, $allowedCategories, $allowedIndustries, $allowedRevenues);
		
		$all = $this->localization->translate('all',$this->extensionName);
	
		$allCategories = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Category');
		$allCategories->setTitle($all);
		array_unshift($allowedCategories,$allCategories);
	
		$allIndustries = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Industry');
		$allIndustries->setTitle($all);
		array_unshift($allowedIndustries,$allIndustries);
	
		$allRevenues = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Revenue');
		$allRevenues->setTitle($all);
		array_unshift($allowedRevenues,$allRevenues);
	
		$this->view->assign('categories', $allowedCategories);
		$this->view->assign('industries', $allowedIndustries);
		$this->view->assign('revenues', $allowedRevenues);
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
			if($filter['revenue']){
				$filterObject->setRevenue($filter['revenue']);
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

	protected function getCategories($includeDescription = true){
		return $this->categoryRepository->findAll()->toArray();
	}
	
	protected function getIndustries($includeDescription = true){
		return $this->industryRepository->findAll()->toArray();
	}
	
	protected function getRevenues($includeDescription = true){
		return $this->revenueRepository->findAll()->toArray();
	}
		
	protected function getPages($includeDescription = true){
		$values = Array(0 => $this->localization->translate('page',$this->extensionName),
					1 => $this->localization->translate('page1',$this->extensionName),
					2 => $this->localization->translate('page2',$this->extensionName),
					3 => $this->localization->translate('page3',$this->extensionName),
					4 => $this->localization->translate('page4',$this->extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	protected function getLanguages($includeDescription = true){
		$values = Array();
		for($i=1;$i<10;$i++){
			$values[$i] = $i;
		}
		return $values;
	}
	
	protected function removeNotSet(&$request, &$allowedCategories, &$allowedIndustries, &$allowedRevenues){
		$arguments = $request->getArguments();
		$categoryValue = 0;
		if($arguments['filter']['category']){
			$categoryValue = intval($arguments['filter']['category']); // 5
		}
		$industryValue = 0;
		if($arguments['filter']['industry']){
			$industryValue = intval($arguments['filter']['industry']); // 4
		}
		$revenueValue = 0;
		if($arguments['filter']['revenue']){
			$revenueValue = intval($arguments['filter']['revenue']); // 4
		}
		
		$remove = Array();
		foreach($allowedCategories as $uid => $category){
			$count = $this->referenceRepository->countByOption($category->getUid(),$industryValue,$revenueValue,$this->showDeactivated);
			if($count == 0){
				$remove[$uid] = 'remove';
			}
		}
		$allowedCategories = array_diff_key($allowedCategories,$remove);
		
		$remove = Array();
		foreach($allowedIndustries as $uid => $industry){
			$count = $this->referenceRepository->countByOption($categoryValue,$industry->getUid(),$revenueValue,$this->showDeactivated);
			if($count == 0){
				$remove[$uid] = 'remove';
			}
		}
		$allowedIndustries = array_diff_key($allowedIndustries,$remove);
		
		$remove = Array();
		foreach($allowedRevenues as $uid => $revenue){
			$count = $this->referenceRepository->countByOption($categoryValue,$industryValue,$revenue->getUid(),$this->showDeactivated);
			if($count == 0){
				$remove[$uid] = 'remove';
			}
		}
		$allowedRevenues = array_diff_key($allowedRevenues,$remove);
	}
	
	/**
	 * Get the namespace of the uploaded file
	 *
	 * @return string
	 */
	protected function getNamespace() {
		$frameworkSettings = $this->configurationManager->getConfiguration(Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		return strtolower('tx_' . $frameworkSettings['extensionName'] . '_' . $frameworkSettings['pluginName']);
	}	
}

?>