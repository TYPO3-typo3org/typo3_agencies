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
class Tx_Typo3Agencies_Controller_AgencyController extends Tx_Typo3Agencies_Controller_BaseController {

	/**
	 * Verify code action
	 * 
	 * @param string $agencyCode 
	 * @dontvalidate $agencyCode
	 * 
	 * @return void
	 */
	public function verifyCodeAction($agencyCode = NULL) {

		$memberDataUtility = $this->objectManager->get('Tx_Typo3Agencies_Utility_MemberData');

		if($agencyCode !== NULL) {
			if($memberDataUtility->getMemberDataByCode($agencyCode) !== NULL) {
				if((int) $this->agencyRepository->countByCode($agencyCode) == (int) 0) {
					$newAgency = $this->objectManager->create('Tx_Typo3Agencies_Domain_Model_Agency');
					$newAgency->setCode($agencyCode);
					$newAgency->setAdministrator((int) $GLOBALS['TSFE']->fe_user->user['uid']);
					
					$this->agencyRepository->add($newAgency);
					$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
					
					$this->redirect('enterInformation', 'Agency', $this->extensionName, array('newAgency' => $newAgency));
					
				} else {
					$this->flashMessageContainer->add('The entered key is already used', 'Key is already used', t3lib_message_AbstractMessage::ERROR);
				}
			} else {
				$this->flashMessageContainer->add('The entered key is not valid', 'Invalid key', t3lib_message_AbstractMessage::ERROR);
			}
		} else {
			$this->flashMessageContainer->add('No agency code was entered', '', t3lib_message_AbstractMessage::WARNING);
		}
		
		$this->redirect('enterCode');
	}


	/**
	 * Enter code, action
	 * 
	 * @return void
	 */
	public function enterCodeAction() {
		
	}

	 /**
	 * Enter agency information, action
	 * 
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 * 
	 * @dontvalidate $newAgency
	 * 
	 * @return void
	 */
	public function enterInformationAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency = NULL) {
		if ($newAgency->getAdministrator() == $this->administrator) {
			$this->addCountries();
			$this->view->assign('newAgency', $newAgency);
		}
	}
	
	/**
	 * Update new agency information and go to step 3
	 * 
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 * @dontvalidata $newAgency
	 */
	public function updateNewAgencyAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency) {
		//var_dump($newAgency);die();
		$this->agencyRepository->update($newAgency);
		$this->redirect('enterApprovalData', 'Agency', $this->extensionName, array('newAgency' => $newAgency));
	}
	
	/**
	 * Enter approval data
	 * 
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 */
	public function enterApprovalDataAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency) {
		if ($newAgency->getAdministrator() == $this->administrator) {
			$this->view->assign('agency', $newAgency);
		}	
	}
	
	/**
	 * Send approval data
	 */
	public function sendApprovalDataAction() {
		
	}
	
	/**
	 * Create agency
	 * 
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 * 
	 * @return void
	 */
	public function createAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency = NULL) {
		if($newAgency === NULL) {
			$this->redirect('verifyCode');
		}
		//var_dump($newAgency);die();
		$this->agencyRepository->add($newAgency);
		$this->flashMessageContainer->add('Agency created', 'yehaaa!');
		#$this->redirect('verifyCode');
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
		$usedCredits = $this->referenceRepository->countByAgency($agency);
		$agency->setReferences($references);
		$this->view->assign('agency', $agency);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('redirect','show');
		$this->view->assign('redirectController','Agency');
		$this->view->assign('availableCredits',$agency->getCasestudies() - $usedCredits);
		$this->view->assign('usedCredits',$usedCredits);
		$this->addFilterOptions();
	}

	private function addFilterOptions(){
		$allowedCategories = Tx_Typo3Agencies_Controller_BaseController::getCategories($this, $this->extensionName);
		$allowedIndustries = Tx_Typo3Agencies_Controller_BaseController::getIndustries($this, $this->extensionName);
		$allowedCompanySizes = Tx_Typo3Agencies_Controller_BaseController::getCompanySizes($this, $this->extensionName);
		
		Tx_Typo3Agencies_Controller_BaseController::removeNotSet($this, $this->request, $allowedCategories, $allowedIndustries, $allowedCompanySizes);
		
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
		$filterObject = $this->createFilterObject($filter);

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
	 * Gets the memberData from association.typo3.org and uses this data to
	 * - update membership level and amount of caseStudies allowed in agency
	 * - deactivates caseStudies if allowed amount is lower than activated caseStudies
	 *
	 * @return void
	 */
	public function updateAgenciesByMemberDataAction() {

		$memberDataUtility = $this->objectManager->get('Tx_Typo3Agencies_Utility_MemberData');
		$memberDataArray = $memberDataUtility->getAllMemberData();

		print_r($memberDataArray);

		die('test');

		foreach($memberDataArray as $memberData) {

			$agency = $this->agencyRepository->getOneByCode($memberData['code']);
			if($agency) { /** @var $agency Tx_Typo3Agencies_Domain_Model_Agency */

				$allowedCaseStudies = (int) $memberData['caseStudies'];

				$agency->setCaseStudies($allowedCaseStudies);
				$agency->setMember($memberData['memberLevel']);

				$references = $this->referenceRepository->findAllByAgency($agency);

				foreach($references as $reference) { /** @var $reference Tx_Typo3Agencies_Domain_Model_Reference */

					if($allowedCaseStudies <= 0) {
						$reference->isDeactivated(1);
					}

					$allowedCaseStudies--;
				}
			}
		}
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