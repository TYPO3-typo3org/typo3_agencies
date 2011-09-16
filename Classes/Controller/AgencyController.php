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

require_once('GeneralFunctions.php');

/**
 * The agency controller for the Reference package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Controller_AgencyController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Typo3Agencies_Domain_Model_AgencyRepository
	 */
	protected $agencyRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Model_ReferenceRepository
	 */
	public $referenceRepository;
	/**
	 * @var Tx_Typo3Agencies_Domain_Model_CountryRepository
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

	/**
	 * Index action for this controller. Displays a list of agencies.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		
		// Add Google API
		$this->response->addAdditionalHeaderData('<script src="http://maps.google.com/maps/api/js?sensor=true"></script>');

		$this->view->assign('countries', $this->agencyRepository->findAllCountries());
		$this->view->assign('imagePath', t3lib_extMgm::extRelPath('typo3_agencies') . 'Resources/Public/Media/Images/');
		$this->view->assign('redirect','index');
		$this->view->assign('filter',t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter'));
	}

	/**
	 * Geo code the address. Utility action
	 *
	 * @return string The rendered view
	 */
	public function geocodeAction() {
		$agencies = $this->agencyRepository->findAll();

		// Assign values
		$this->view->assign('agencies', $agencies);
	}

	/**
	 * Displays a agency and its references
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The agency to display
	 * @dontvalidate $agency
	 * @return string The rendered view
	 */
	public function showAction(Tx_Typo3Agencies_Domain_Model_Agency $agency = null) {
		if ($agency == null) {
			$agency = $this->agency;
		}

		$showDeactivated = false;
		if ($agency->getAdministrator() == $this->administrator) {
			$showDeactivated = true;
		}
		$references = $this->referenceRepository->findAllByAgency($agency, $showDeactivated);
		$agency->setReferences($references);
		$this->view->assign('agency', $agency);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('redirect','show');
		$this->view->assign('redirectController','Agency');
		$this->addFilterOptions();
	}

	private function addFilterOptions(){
		$allowedCategories = GeneralFunctions::getCategories($this, $this->extensionName);
		$allowedIndustries = GeneralFunctions::getIndustries($this, $this->extensionName);
		$allowedCompanySizes = GeneralFunctions::getCompanySizes($this, $this->extensionName);
		
		GeneralFunctions::removeNotSet($this, $this->request, $allowedCategories, $allowedIndustries, $allowedCompanySizes);
		
		$this->view->assign('categories', $allowedCategories);
		$this->view->assign('industries', $allowedIndustries);
		$this->view->assign('companySizes', $allowedCompanySizes);
	}
	
	private function addCountries(){
		$countries = $this->countryRepository->findAll();
		$availableCountries = Array();
		foreach($countries as $country){
			$availableCountries[$country->getCnIso2()] = $country->getCnShortEn();
		}

		$this->view->assign('countries', $availableCountries);
	}

	/**
	 * Edits an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The agency to be edited. This might also be a clone of the original agency already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @param boolean $logo Delete the logo
	 * @param boolean $submit Form got submitted
	 * @return string Form for editing the existing agency
	 * @dontvalidate $agency
	 * @dontvalidate $logo
	 */
	public function editAction(Tx_Typo3Agencies_Domain_Model_Agency $agency, $logo = false, $submit = false) {
		if ($agency->getAdministrator() == $this->administrator) {
			if ($logo == 1) {
				$agency->setLogo('');
				$this->agencyRepository->update($agency);
				$this->flashMessages->add($this->localization->translate('logoRemoved', $this->extensionName),'',t3lib_message_AbstractMessage::OK);
			}
			$this->handleFiles($agency);
			if($submit){
				$this->agencyRepository->update($agency);
				$this->flashMessages->add(str_replace('%NAME%', $agency->getName(), $this->localization->translate('agencyUpdated', $this->extensionName)),'',t3lib_message_AbstractMessage::OK);
			}
			$this->view->assign('agency', $agency);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('administrator', $this->administrator);
			$this->addCountries();
			
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		} else {
			$this->redirect('show', 'Agency',$this->extensionName,Array('agency'=>$agency));
		}
	}
	
	/**
	 * Edits an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The agency to be edited. This might also be a clone of the original agency already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @param boolean $logo Delete the logo
	 * @param boolean $submit Form got submitted
	 * @return string Form for editing the existing agency
	 * @dontvalidate $logo
	 */
	public function updateAction(Tx_Typo3Agencies_Domain_Model_Agency $agency, $logo = false, $submit = false) {
		if ($agency->getAdministrator() == $this->administrator) {
			if($this->handleFiles($agency)){
				$this->agencyRepository->update($agency);
				$this->flashMessages->add(str_replace('%NAME%', $agency->getName(), $this->localization->translate('agencyUpdated', $this->extensionName)),'',t3lib_message_AbstractMessage::OK);
				
				$references = $this->referenceRepository->findAllByAgency($agency, $showDeactivated);
				$agency->setReferences($references);
				$this->view->assign('agency', $agency);
				$this->view->assign('uploadPath', $this->settings['uploadPath']);
				$this->view->assign('administrator', $this->administrator);
				$this->addFilterOptions();
				
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
			} else {
				$this->agencyRepository->update($agency);
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				$this->redirect('edit','Agency',$this->extensionName,Array('agency'=>$agency));
			}
		} else {
			$this->redirect('show', 'Agency',null,Array('agency'=>$agency));
		}
	}

	/**
	 * List action for this controller. Displays a list of agencies
	 *
	 * @var array $filter The filter to filter
	 * @return string The rendered view
	 * @dontvalidate $filter
	 * @dontvalidate $order
	 */
	public function listAction(array $filter = null) {
		// Process the filter
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

		// Initialize the order
		$order = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Order');
		$order->addOrdering('member', Tx_Extbase_Persistence_QueryInterface::ORDER_DESCENDING);
		$order->addOrdering('name', Tx_Extbase_Persistence_QueryInterface::ORDER_ASCENDING);
		
		// Initialize the pager
		$pager = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Pager');

		if ($this->request->hasArgument('page')) {
			$pager->setPage($this->request->getArgument('page'));
			if ($pager->getPage() < 1) {
				$pager->setPage(1);
			}
		}

		$pager->setItemsPerPage($this->settings['pageBrowser']['itemsPerPage']);
		$offset = ($pager->getPage() - 1) * $pager->getItemsPerPage();
		$count = 0;
		
		if($filterObject->getLocation() != ''){
			//geocode the location
			$url = 'http://maps.google.com/maps/geo?'.
			$this->buildURL('q', $filterObject->getLocation()).
			$this->buildURL('output', 'csv').
			$this->buildURL('key', $this->settings['googleMapsKey']);

			$csv = t3lib_div::getURL($url);
			$csv = explode(',', $csv);
			
			switch($csv[0]) {
				case 200:
					/*
					 * Geocoding worked!
					 * 200:  OK
					 */
					if (TYPO3_DLOG) t3lib_div::devLog('Google: '.$filterObject->getLocation(), 'typo3_agencies', -1, $filterObject->getLocation());
					if (TYPO3_DLOG) t3lib_div::devLog('Google Answer', 'typo3_agencies', -1, $csv);
					$latlong['lat'] = $csv[2];
					$latlong['long'] = $csv[3];
					break;
				case 500:
				case 610:
					/*
					 * Geocoder can't run at all, so disable this service and
					 * try the other geocoders instead.
					 * 500: Undefined error.  Geocoding may be blocked.
					 * 610: Bad API Key.
					 */
					if (TYPO3_DLOG) t3lib_div::devLog('Google: '.$csv[0].': '.$filterObject->getLocation().'. Disabling.', 'typo3_agencies', 3, $filterObject->getLocation());
					$latlong = null;
					break;
				default:
					/*
					 * Something is wrong with this address. Might work for other
					 * addresses though.
					 * 601: No address to geocode.
					 * 602: Unknown address.
					 * 603: Can't geocode for contractual reasons.
					 */
					if (TYPO3_DLOG) t3lib_div::devLog('Google: '.$csv[0].': '.$filterObject->getLocation().'. Disabling.', 'typo3_agencies', 2, $filterObject->getLocation());
					$latlong = null;
					break;
			}
		}

		// Query the repository
		$agencies = $this->agencyRepository->findAllByFilter($filterObject, $order, $offset, $pager->getItemsPerPage(), $latlong, $this->settings['nearbyAdditionalWhere']);
		$allAgencies = $this->agencyRepository->findAllByFilter($filterObject, null, null, null, $latlong, $this->settings['nearbyAdditionalWhere']);
		$count = $this->agencyRepository->countAllByFilter($filterObject, $latlong, $this->settings['nearbyAdditionalWhere']);
		$pager->setCount($count);

		// Assign values
		$this->view->assign('agencies', $agencies);
		$this->view->assign('allAgencies', $allAgencies);
		$this->view->assign('pager', $pager);
		$this->view->assign('filter', $filterObject);
	}
	
	/**
	 * Override getErrorFlashMessage to present
	 * nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		switch ($this->actionMethodName) {
			case 'updateAction' :
				return $this->localization->translate('agencyUpdateFailed', $this->extensionName);
			case 'createAction' :
				return $this->localization->translate('agencyCreateFailed', $this->extensionName);
			default :
				return parent::getErrorFlashMessage();
		}
	}
	
	private function buildURL($name, $value){
		if($value) {
			return $name.'='.urlencode($value).'&';
		}
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $agency
	 */
	private function handleFiles(&$agency) {

		$ok = true;
		
		if (is_array($_FILES['tx_typo3agencies_pi1'])) {

			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$all_files = Array();
			$all_files['webspace']['allow'] = '*';
			$all_files['webspace']['deny'] = '';
			$fileFunc->init('', $all_files);
			$formName = array_shift(array_keys($_FILES['tx_typo3agencies_pi1']['error']));
			foreach ($_FILES['tx_typo3agencies_pi1']['error'][$formName] as $key => $error) {
				if($error == 0){
					if($key == 'logo'){
						if( strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key],'image/png') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key],'image/jpg') === 0){
							if($_FILES['tx_typo3agencies_pi1']['size'][$formName][$key] < 500000){
								$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
								$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
								t3lib_div::upload_copy_move($theFile,$theDestFile);
								$agency->setLogo(basename($theDestFile));
							} else {
								$ok = false;
								$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key], $this->localization->translate('wrongFileSize',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
							}
						} else {
							$ok = false;
							$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key], $this->localization->translate('wrongFileType',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
						}
					}
				}
			}
		}
		return $ok;
	}

}

?>