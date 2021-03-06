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
	 * @deprecated Seems not necessary anymore, but might be linked in a template
	 *
	 * @return void
	 */
	public function verifyCodeAction($agencyCode = NULL) {

		/** @var $memberDataUtility Tx_Typo3Agencies_Utility_MemberData */
		//$memberDataUtility = $this->objectManager->get('Tx_Typo3Agencies_Utility_MemberData');

		// Disabled because shop data do not exists anymore. Codesprint May 2nd-4th
		$memberData = NULL;

		if($agencyCode) {
			//$memberData = $memberDataUtility->getMemberDataByCode($agencyCode);

			// fake $memberData because API is not working anymore
			$memberData = array(
				'caseStudies' => 0,
				'isApproved' => 0,
				'membershipLevel' => '',
			);


			if($memberData !== NULL) {
				if((int) $this->agencyRepository->countByCode($agencyCode) == 0) {

					$this->redirect('enterInformation', 'Agency', $this->extensionName, array('newAgency' => NULL));

				} else {
						/* @var $newAgency Tx_Typo3Agencies_Domain_Model_Agency */
					$newAgency = $this->agencyRepository->findOneByCode($agencyCode);
					if (!$newAgency->getAdministrator()) {
						$newAgency->setAdministrator((int) $GLOBALS['TSFE']->fe_user->user['uid']);
					}
					if ($newAgency->getAdministrator() == (int) $GLOBALS['TSFE']->fe_user->user['uid']) {
						$this->redirect('enterInformation', 'Agency', $this->extensionName, array('newAgency' => $newAgency));
					} else {
						$this->flashMessageContainer->add('The entered key is already used', 'Key is already used', t3lib_message_AbstractMessage::ERROR);
					}
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
	 * @todo This is more like a list action now
	 * @return void
	 */
	public function enterCodeAction() {
		$agencies = $this->agencyRepository->findAllForUser((int) $GLOBALS['TSFE']->fe_user->user['uid']);
		$this->view->assign('agencies', $agencies);
	}

	 /**
	 * Enter agency information, action
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 * @dontvalidate $newAgency
	 * @param boolean $logo Delete the logo
	 *
	 * @return void
	 */
	public function enterInformationAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency = NULL, $logo = FALSE) {
		$this->flashMessageContainer->getAllAndFlush();

		if($newAgency === NULL) {
			$newAgency = $this->createNewAgency();
		}

		$this->redirectOnMissingAccessRights($newAgency);

		if ($logo) {
			$newAgency->setLogo('');
			$this->agencyRepository->update($newAgency);
			$this->flashMessages->add($this->localization->translate('logoRemoved', $this->extensionName),'',t3lib_message_AbstractMessage::OK);
		}
		$this->handleFiles($newAgency);
		$this->addCountries();
		$this->view->assign('newAgency', $newAgency);
	}



	/**
	 * Update new agency information and go to step 3
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 */
	public function updateNewAgencyAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency) {
		if(!$newAgency->getAdministrator()) {
			$newAgency->setAdministrator((int) $GLOBALS['TSFE']->fe_user->user['uid']);
		}

		$this->redirectOnMissingAccessRights($newAgency);

		if($newAgency->getUid() !== NULL) {
			$this->agencyRepository->update($newAgency);
		} else {
			$this->agencyRepository->add($newAgency);
		}
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();

		$this->geoCodeAgency($newAgency);
		$this->handleFiles($newAgency);

		$this->forward('enterApprovalData', 'Agency', $this->extensionName, array('newAgency' => $newAgency));
	}

	/**
	 * Enter approval data
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 * @dontvalidate $newAgency
	 * @param array $errors
	 * @param array $referringArguments
	 */
	public function enterApprovalDataAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency, $errors = null, $referringArguments = null) {
		$this->redirectOnMissingAccessRights($newAgency);

		$this->view->assign('newAgency', $newAgency);
		$this->view->assign('errors', $errors);
		$this->view->assign('referringArguments', $referringArguments);
	}

	/**
	 * Send approval data
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $newAgency
	 */
	public function sendApprovalDataAction(Tx_Typo3Agencies_Domain_Model_Agency $newAgency) {
		$this->redirectOnMissingAccessRights($newAgency);

		if (!$this->request->getArgument('typo3version')) {
			$this->forward(
					'enterApprovalData',
					null,
					null,
					array(
							'newAgency' => $newAgency,
							'errors' => array ('typo3version' => '12312376'),
							'referringArguments' => $this->request->getArguments()
					)
			);
		} else {
			$this->view->assign('agency', $newAgency);
			$this->view->assign('typo3Version', $this->request->getArgument('typo3version'));
			$this->view->assign('certifiedEmployee', $this->request->getArgument('certifiedintegrator'));
			$this->view->assign('developmentKnowledge', $this->request->getArgument('knowledge'));
			$this->view->assign('financialContribution', $this->request->getArgument('financialContributions'));
			$this->view->assign('activeContribution', $this->request->getArgument('activeContributions'));
			$this->view->assign('caseStudies', $this->request->getArgument('casestudies'));

			$bodyContent = $this->view->render();

			$mail = t3lib_div::makeInstance('t3lib_mail_Message');
			$mail->setFrom(array($newAgency->getEmail() => $newAgency->getName()));
			$mail->setTo(array($this->settings['applicationEmailAddress'] => 'Agency Listing'));
			$mail->setSubject('PSL approval request from ' . $newAgency->getName());
			$mail->setBody($bodyContent);
			$mail->send();

			$this->forward('confirmAgencySubmission');
		}
	}

	/**
	 * Confirm agency submission
	 */
	public function confirmAgencySubmissionAction() {
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
		$this->response->addAdditionalHeaderData('<script src="https://maps.google.com/maps/api/js?sensor=true&language=en"></script>');

		$this->view->assign('countries', $this->agencyRepository->findAllCountries());
		$this->view->assign('imagePath', t3lib_extMgm::extRelPath('typo3_agencies') . 'Resources/Public/Media/Images/');
		$this->view->assign('redirect','index');
		$this->view->assign('filter',t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter'));
	}

	/**
	 * Geo code an agency
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The agency to geo code
	 */
	public function geoCodeAgency(Tx_Typo3Agencies_Domain_Model_Agency $agency) {

		// The Geocoding API v2 has been turned down on September 9th, 2013.
		// The Geocoding API v3 should be used now. Learn more at https://developers.google.com/maps/documentation/geocoding/
		return;

		// Initialize delay in geocode speed
		$delay = 0;
		$base_url = 'https://maps.google.com/maps/geo?output=xml&key=' . $this->settings['googleMapsKey'];

		$geocode_pending = true;

		while ($geocode_pending) {

			$countries = $this->countryRepository->findByCnIso2($agency->getCountry());
			if(count($countries) == 1){
				$address = $agency->getAddress().', '.$agency->getZip().' '.$agency->getCity().', '.$countries->getFirst()->getCnShortEn();
				$request_url = $base_url . '&q=' . urlencode($address);
				$xml = simplexml_load_file($request_url);

				$status = $xml->Response->Status->code;
				if (strcmp($status, '200') == 0) {
					// Successful geocode
					$geocode_pending = false;
					$coordinates = $xml->Response->Placemark->Point->coordinates;
					$coordinatesSplit = explode(',', $coordinates);

					$agency->setLatitude($coordinatesSplit[1]);
					$agency->setLongitude($coordinatesSplit[0]);
					$this->agencyRepository->update($agency);
				} else if (strcmp($status, "620") == 0) {
					// sent geocodes too fast
					$delay += 100000;
				} else {
					// failure to geocode
					$geocode_pending = false;
					// maybe next time
				}
				usleep($delay);
			}
		}
	}

	/**
	 * Displays a agency and its references
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency The agency to display
	 * @dontvalidate $agency
	 * @return string The rendered view
	 */
	public function showAction(Tx_Typo3Agencies_Domain_Model_Agency $agency = null) {
		if ($agency === null) {
			$agency = $this->agency;
		}

		$isAdministrator = false;
		if ($agency instanceof Tx_Typo3Agencies_Domain_Model_Agency) {
			$GLOBALS['TSFE']->page['title'] = $GLOBALS['TSFE']->indexedDocTitle = $agency->getName();
			if ($agency->getAdministrator() == $this->administrator) {
				$isAdministrator = true;
			}
			$references = $this->referenceRepository->findAllByAgency($agency, $isAdministrator);
			$usedCredits = $this->referenceRepository->countByAgency($agency);
			$agency->setReferences($references);
			$this->view->assign('agency', $agency);
			$this->view->assign('isAdministrator', $isAdministrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('redirect','show');
			$this->view->assign('redirectController','Agency');
			$this->view->assign('availableCredits',$agency->getCasestudies() - $usedCredits);
			$this->view->assign('usedCredits',$usedCredits);
			$this->addFilterOptions();
		}
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
	 * @dontverifyrequesthash
	 */
	public function editAction(Tx_Typo3Agencies_Domain_Model_Agency $agency, $logo = false, $submit = false) {
		if ($agency->getAdministrator() == $this->administrator) {
			if ($logo == 1) {
				$agency->setLogo('');
				$this->agencyRepository->update($agency);
				$this->geoCodeAgency($agency);
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
	 * @dontvalidate $submit
	 * @dontverifyrequesthash
	 */
	public function updateAction(Tx_Typo3Agencies_Domain_Model_Agency $agency, $logo = false, $submit = false) {
		if ($agency->getAdministrator() == $this->administrator) {
			$this->agencyRepository->update($agency);
			$this->geoCodeAgency($agency);
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
			if(!$this->handleFiles($agency)){
				$this->redirect('edit', 'Agency', $this->extensionName, array('agency' => $agency));
			}
		}
		$this->redirect('show', 'Agency', null, array('agency' => $agency));
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

		if ($filterObject instanceof Tx_Typo3Agencies_Domain_Model_Filter &&  $filterObject->getLocation() != ''){
			//geocode the location
			$url = 'https://maps.googleapis.com/maps/api/geocode/json?'.
			$this->buildURL('address', $filterObject->getLocation());
			#$this->buildURL('output', 'csv').
			#$this->buildURL('key', $this->settings['googleMapsKey']);

			$result = json_decode(t3lib_div::getURL($url));
			$latLong = array();

			switch($result->status) {
				case 'OK':
					/*
					 * Geocoding worked!
					 * 200:  OK
					 */
					$latLong['lat'] = $result->results[0]->geometry->location->lat;
					$latLong['long'] = $result->results[0]->geometry->location->lng;
					break;
				default:
			}
		}

		// Query the repository
		if (!empty($latLong) || $filterObject->getLocation() === '') {
			$agencies = $this->agencyRepository->findAllByFilter(
				$filterObject, $order, $offset, $pager->getItemsPerPage(), $latLong, $this->settings['nearbyAdditionalWhere']
			);
			$allAgencies = $this->agencyRepository->findAllByFilter(
				$filterObject, NULL, 0, 0, $latLong, $this->settings['nearbyAdditionalWhere']
			);
		}
		if ($agencies !== NULL) {
			$agenciesArray = $agencies->toArray();
		}
		if ($filterObject->getLocation() !== '' && (empty($latLong) || empty($agenciesArray))) {
			// search for the name or city
			$agencies = $this->agencyRepository->findByNameOrCity($filterObject, $order);
			$allAgencies = $agencies;
		}
		$count = count($allAgencies->toArray());
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

		$namespace = $this->getNamespace();
		if (is_array($_FILES[$namespace])) {
			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$all_files = Array();
			$all_files['webspace']['allow'] = '*';
			$all_files['webspace']['deny'] = '';
			$fileFunc->init('', $all_files);
			$formName = array_shift(array_keys($_FILES[$namespace]['error']));
			foreach ($_FILES[$namespace]['error'][$formName] as $key => $error) {
				if($error == 0){
					if($key == 'logo'){
						if( strpos($_FILES[$namespace]['type'][$formName][$key],'image/png') === 0 || strpos($_FILES[$namespace]['type'][$formName][$key],'image/jpg') === 0){
							if($_FILES[$namespace]['size'][$formName][$key] < 5 * 1024 * 1024){
								$theFile = $_FILES[$namespace]['tmp_name'][$formName][$key];
								$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES[$namespace]['name'][$formName][$key]), $this->settings['uploadPath']);
								t3lib_div::upload_copy_move($theFile,$theDestFile);
								$agency->setLogo(basename($theDestFile));
							} else {
								$ok = false;
								$this->flashMessages->add(str_replace('%FILE%', $_FILES[$namespace]['name'][$formName][$key], $this->localization->translate('wrongFileSize',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
							}
						} else {
							$ok = false;
							$this->flashMessages->add(str_replace('%FILE%', $_FILES[$namespace]['name'][$formName][$key], $this->localization->translate('wrongFileType',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
						}
					}
				}
			}
		}
		return $ok;
	}

	/**
	 * @param Tx_Typo3Agencies_Domain_Model_Agency $agency
	 * @return null
	 */
	protected function redirectOnMissingAccessRights($agency) {
		if(!$agency->getAdministrator()) {
			return;
		}
		if ($agency->getAdministrator() !== intval($GLOBALS['TSFE']->fe_user->user['uid'])) {
			$this->flashMessageContainer->add('You are not allowed to edit this agency', 'Access not allowed', t3lib_message_AbstractMessage::ERROR);
			$this->redirect('enterCode');
		}
	}

	/**
	 * a work-around to avoid a major refactoring of the code
	 *
	 * Previously the agency code was given out by the association and information
	 * on the membership of an agency was fetched from the shop via an API.
	 * Right now the shop is closed down and so this code does not have any use.
	 *
	 * @return string
	 */
	protected function getRandomAgencyCode() {
		return 'c0de' . t3lib_div::getRandomHexString(12);
	}

	/**
	 * returns a new (non-persisted) Agency Domain Model that is hidden by default
	 *
	 * @param null|string $agencyCode
	 * @return Tx_Typo3Agencies_Domain_Model_Agency
	 */
	protected function createNewAgency($agencyCode = NULL) {
		if($agencyCode === NULL) {
			$agencyCode = $this->getRandomAgencyCode();
		}
		/* @var $newAgency Tx_Typo3Agencies_Domain_Model_Agency */
		$newAgency = $this->objectManager->create('Tx_Typo3Agencies_Domain_Model_Agency');
		$newAgency->setCode($agencyCode);
		$newAgency->setAdministrator((int) $GLOBALS['TSFE']->fe_user->user['uid']);

		// do not list agency by default
		$newAgency->setApproved(FALSE);
		$newAgency->setCaseStudies(0);
		$newAgency->setMember('');

		return $newAgency;
	}

}

?>