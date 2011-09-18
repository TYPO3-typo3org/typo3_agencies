<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Mario Matzulla <mario@matzullas.de>
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
***************************************************************/

require_once('ReferenceController.php');
/**
 * The reference controller for the Reference package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Controller_ReferenceController extends Tx_Typo3Agencies_Controller_BaseController {

	/**
	 * Index action for this controller. Displays a list of references.
	 *
	 * @param array $filter The filter to filter
	 * @param string $filterString A passed on filterString to be tokenized
	 * @return string The rendered view
	 * @dontvalidate $filter
	 */
	public function indexAction(array $filter = null, $filterString = null) {

		$filterObject = $this->createFilterObject($filter);
		
		if($this->settings['showAgencyIfLoggedIn']==1 && $this->administrator>0){
			 $this->redirect('show','Agency');
		} else {
			$allowedCategories = Tx_Typo3Agencies_Controller_BaseController::getCategories($this, $this->extensionName);
			$allowedIndustries = Tx_Typo3Agencies_Controller_BaseController::getIndustries($this, $this->extensionName);
			$allowedCompanySizes = Tx_Typo3Agencies_Controller_BaseController::getCompanySizes($this, $this->extensionName);
			
			Tx_Typo3Agencies_Controller_BaseController::removeNotSet($this, $this->request, $allowedCategories, $allowedIndustries, $allowedCompanySizes);
			
			$this->view->assign('categories', $allowedCategories);
			$this->view->assign('industries', $allowedIndustries);
			$this->view->assign('companySizes', $allowedCompanySizes);
			
			$this->pager = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Pager');
			
			if($this->request->hasArgument('page')){
				$this->pager->setPage($this->request->getArgument('page'));
				if($this->pager->getPage() < 1){
					$this->pager->setPage(1);
				}
			}

			if($filterObject == null && isset($filterString)){
				$filterObject = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter');
				if(strpos($filterString,'industry#')!==false){
					$filterObject->setIndustry(substr($filterString,9));
				}
				if(strpos($filterString,'category#')!==false){
					$filterObject->setCategory(substr($filterString,9));
				}
				if(strpos($filterString,'tag#')!==false){
					$filterObject->setSearchTerm(substr($filterString,4));
					$this->view->assign('filterString', true);
				}
			}
			
			$tagArray = Array();
	
			$this->pager->setItemsPerPage($this->settings['pageBrowser']['itemsPerPage']);
			$offset = ($this->pager->getPage() - 1) * $this->pager->getItemsPerPage();
			$count = 0;
			if($filterObject == null){
				$ignore = 0;
				if($this->settings['topReferences'] != ''){
					$topReferences = $this->referenceRepository->findValidTopReferences($this->settings['topReferences']);
					$rand = rand(0,count($topReferences)-1);
					$ignore = $topReferences[$rand];
					$topReference = $this->referenceRepository->findByUid($ignore);
					$tags = t3lib_div::trimExplode(',',$topReference->getTags(),1);
					foreach ($tags as $tag){
						$tagArray[$tag][] = 1;
					}
					$this->view->assign('topReference', $topReference);
				}
				$this->filter = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter');
				$since = time() - $this->settings['recentlyPastDays'] * 86400;
				$count = $this->referenceRepository->countRecentlyAdded($this->showDeactivated, $this->agency, $since);
				$references = $this->referenceRepository->findRecentlyAdded($offset, $this->pager->getItemsPerPage(), $this->showDeactivated, $this->agency, $since, $ignore);
			} else {
				$this->filter = $filterObject;
				$references = $this->referenceRepository->findAllByFilter($this->filter,null,null,false,$this->showDeactivated);
				$count = count($references);
				$this->filter->setResultCount($count);
				$references = $this->referenceRepository->findAllByFilter($this->filter, $offset, $this->pager->getItemsPerPage(), false, $this->showDeactivated);
			}
			
			$this->pager->setCount($count);
			$this->view->assign('pager', $this->pager);
			$this->view->assign('filter', $this->filter);
			$this->view->assign('references', $references);
			
			
			
			foreach ($references->toArray() as $reference){
				$tags = t3lib_div::trimExplode(',',$reference->getTags(),1);
				foreach ($tags as $tag){
					$tagArray[$tag][] = 1;
				}
			}
			
			$tagCloudArray = Array();
			foreach($tagArray as $tag => $values){
				$tagCloudArray[] = Array('tag'=>$tag.' ', 'occurrences' => count($values), 'href'=>$this->uriBuilder->uriFor('index',Array('filterString'=>'tag#'.$tag),'Reference'), 'title'=>null, 'style'=>null);
			}
			$this->view->assign('tagCloud', $tagCloudArray);
			
			
			$request = t3lib_div::makeInstance('Tx_Extbase_MVC_Web_Request');
			$request->setBaseUri($_SERVER['HTTP_HOST']);
			$request->setFormat('json');

			$this->view->assign('ajaxUrl', '');
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('agency', $this->agency);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('redirect','index');
			$this->view->assign('redirectController','Reference');
		}
	}
	
	public function categoriesAction(){
		$this->request->setFormat('json');
		
		$allowedCategories = Tx_Typo3Agencies_Controller_BaseController::getCategories($this);
		$allowedIndustries = Tx_Typo3Agencies_Controller_BaseController::getIndustries($this);
		$allowedCompanySizes = Tx_Typo3Agencies_Controller_BaseController::getCompanySizes($this);
		
		Tx_Typo3Agencies_Controller_BaseController::removeNotSet($this, $this->request, $allowedCategories, $allowedIndustries, $allowedCompanySizes);
		
		$categoryString = Array();
		foreach($allowedCategories as $key => $value){
			$categoryString[] = '{"optionValue":'.$key.',"optionDisplay":"'.$value.'"}';
		}
		$industryString = Array();
		foreach($allowedIndustries as $key => $value){
			$industryString[] = '{"optionValue":'.$key.',"optionDisplay":"'.$value.'"}';
		}
		$companySizeString = Array();
		foreach($allowedCompanySizes as $key => $value){
			$companySizeString[] = '{"optionValue":'.$key.',"optionDisplay":"'.$value.'"}';
		}
		$this->view->assign('jsonString', '[['.implode(',',$categoryString).'],['.implode(',',$industryString).'],['.implode(',',$companySizeString).']]');
	}
	
	/**
	 * Displays a form for creating a new blog
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $newReference A fresh reference object taken as a basis for the rendering
	 * @return string An HTML form for creating a new reference
	 * @dontvalidate $newReference
	 */
	public function newAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference = null) {
		if($newReference == null){
			$newReference = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Reference');
		}
		if(!$this->agency){
			$this->redirect('index');
		} else {
			$this->view->assign('newReference', $newReference);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('categories', Tx_Typo3Agencies_Controller_BaseController::getCategories($this, $this->extensionName, false));
			$this->view->assign('industries', Tx_Typo3Agencies_Controller_BaseController::getIndustries($this, $this->extensionName, false));
			$this->view->assign('companySizes', Tx_Typo3Agencies_Controller_BaseController::getCompanySizes($this, $this->extensionName, false));
			$this->view->assign('pagesList', Tx_Typo3Agencies_Controller_BaseController::getPages($this, $this->extensionName, false));
			$this->view->assign('languagesList', Tx_Typo3Agencies_Controller_BaseController::getLanguages($this, $this->extensionName, false));
			$countries = $this->countryRepository->findAll();
			$availableCountries = Array();
			foreach($countries as $country){
				$availableCountries[$country->getCnShortEn()] = $country->getCnShortEn();
			}

			$this->view->assign('countries', $availableCountries);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
			$this->view->assign('referenceMaxReached', $this->validateMaximumReferences());
		}
	}
	
	/**
	 * Updates the sorting of the references
	 *
	 * @param array $sort The new sorting index and the uid of a Tx_Typo3Agencies_Domain_Model_Reference to change the sorting for
	 */
	public function sortAction(array $sort) {
		$reference = $this->referenceRepository->findByUid($sort['uid']);
		if($reference->getAgency()->getAdministrator() == $this->administrator){
			if($reference->getSorting() != $sort['sort']){
				$references = $this->referenceRepository->findAllByAgency($reference->getAgency(), true);
				if($sort['sort'] < $reference->getSorting()){
					// moving up
					foreach($references as $referenceRecord){
						if($referenceRecord->getSorting() >= $sort['sort'] && $referenceRecord->getSorting() < $reference->getSorting()){
							$referenceRecord->setSorting($referenceRecord->getSorting() + 1);
							$this->referenceRepository->update($referenceRecord);
						}
					}
				} else {
					// moving down
					foreach($references as $referenceRecord){
						if($referenceRecord->getSorting() <= $sort['sort'] && $referenceRecord->getSorting() > $reference->getSorting()){
							$referenceRecord->setSorting($referenceRecord->getSorting() - 1);
							$this->referenceRepository->update($referenceRecord);
						}
					}
				}
				$reference->setSorting($sort['sort']);
				$this->referenceRepository->update($reference);
			}
		}
		$this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
       	$this->objectManager->get('Tx_Extbase_Reflection_Service')->shutdown();
       	$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
       	die();
	}
	
	private function validateMaximumReferences(){
		
		$count = $this->referenceRepository->countByAgency($this->agency);
		//1 = Bronze member
		//2 = Silver member
		//3 = Gold member
		//4 = Platinum member
		$maxReached = false;

		if($count > $this->agency->getCasestudies()){
			$maxReached = true;
			$this->flashMessages->add($this->localization->translate('referenceMoreThanMax', $this->extensionName),'',t3lib_message_AbstractMessage::WARNING);
		} else if($count == $this->agency->getCasestudies()){
			$maxReached = true;
			$this->flashMessages->add($this->localization->translate('referenceMaxReached', $this->extensionName),'',t3lib_message_AbstractMessage::INFO);
		}

		return $maxReached;
	}

	/**
	 * Creates a new reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $newReference A fresh reference object which has not yet been added to the repository
	 * @param string $preview
	 * @param boolean $screenshot
	 * @dontvalidate $preview
	 * @dontvalidate $screenshot
	 * @return void
	 */
	public function createAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference, $preview = null, $screenshot = false) {
		if($this->agency){
			$count = $this->referenceRepository->countByAgency($this->agency);
			$referenceMaxReached = $this->agency->getCasestudies();
			if($count < $referenceMaxReached){
				if($this->handleFiles($newReference)){
					$newReference->setAgency($this->agency);
					if($preview == 'Preview'){
						$this->view->assign('agency', $this->agency);
						$this->view->assign('reference', $newReference);
						$this->view->assign('administrator', $this->administrator);
						$this->view->assign('uploadPath', $this->settings['uploadPath']);
						$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
					} else {
						$newReference->setSorting($count);
						$this->referenceRepository->add($newReference);
						$this->flashMessages->add(str_replace('%NAME%', $newReference->getTitle(), $this->localization->translate('referenceCreated',$this->extensionName)),'',t3lib_message_AbstractMessage::OK);
						$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
					}
				} else {
					$this->view->assign('agency', $this->agency);
					$this->view->assign('reference', $newReference);
					$this->view->assign('administrator', $this->administrator);
					$this->view->assign('uploadPath', $this->settings['uploadPath']);
					$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
					$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
					$this->redirect('create','Reference');
				}
			} else {
				$this->flashMessages->add($this->localization->translate('notAllowed',$this->extensionName),'',t3lib_message_AbstractMessage::ERROR);
				$this->flashMessages->add($this->localization->translate('referenceMaxReached',$this->extensionName),'',t3lib_message_AbstractMessage::WARNING);
			}
		}
		if($preview != 'Preview'){
			$this->redirect('index');
		}
	}
	
	/**
	 * Edits an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be edited. This might also be a clone of the original reference already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @param boolean $screenshot Delete the screenshot
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @return string Form for editing the existing reference
	 * @dontvalidate $reference
	 * @dontvalidate $screenshot
	 */
	public function editAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $screenshot = false, $redirectController = null, $redirect = null) {
		$this->view->assign('redirectController', $redirectController);
		$this->view->assign('redirect', $redirect);
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			if($screenshot == 1){
				$reference->setScreenshot('');
				$this->referenceRepository->update($reference);
				$this->flashMessages->add($this->localization->translate('screenshotRemoved', $this->extensionName),'',t3lib_message_AbstractMessage::OK);
			} else if(is_string($screenshot)){
				$newGallery = str_replace($screenshot, '', $reference->getScreenshotGallery());
				$reference->setScreenshotGallery(implode(',',t3lib_div::trimExplode(',',$newGallery,1)));
				$this->flashMessages->add($this->localization->translate('screenshotRemoved', $this->extensionName),'',t3lib_message_AbstractMessage::OK);
			}
			$this->handleFiles($reference);
			$this->view->assign('reference', $reference);
			$this->view->assign('maxFiles', 3 - count(t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1)));
			$this->view->assign('categories', Tx_Typo3Agencies_Controller_BaseController::getCategories($this, $this->extensionName, false));
			$this->view->assign('industries', Tx_Typo3Agencies_Controller_BaseController::getIndustries($this, $this->extensionName, false));
			$this->view->assign('companySizes', Tx_Typo3Agencies_Controller_BaseController::getCompanySizes($this, $this->extensionName, false));
			$this->view->assign('pagesList', Tx_Typo3Agencies_Controller_BaseController::getPages($this, $this->extensionName, false));
			$this->view->assign('languagesList', Tx_Typo3Agencies_Controller_BaseController::getLanguages($this, $this->extensionName, false));
			$countries = $this->countryRepository->findAll();
			$availableCountries = Array();
			foreach($countries as $country){
				$availableCountries[$country->getCnShortEn()] = $country->getCnShortEn();
			}

			$this->view->assign('countries', $availableCountries);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1));
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Updates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference A not yet persisted clone of the original reference containing the modifications
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @param boolean $submit
	 * @return void
	 */
	public function updateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = null, $redirect = null, $submit = null) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			if($this->handleFiles($reference)){
				//if($submit){
					$this->referenceRepository->update($reference);
					$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceUpdated',$this->extensionName)),'',t3lib_message_AbstractMessage::OK);
					$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				//} else {
				//	$this->redirect('preview','Reference',$this->extensionName,Array('newReference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
				//}
			} else {
				$this->referenceRepository->update($reference);
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				$this->redirect('edit','Reference',$this->extensionName,Array('reference'=>$reference,'screenhot'=>null,'redirectController'=>$redirectController,'redirect'=>$redirect));
			}
		}
		if(isset($redirect) && isset($redirectController)){
			$this->redirect($redirect,$redirectController,$this->extensionName,Array('reference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
		} else {
			$this->redirect('list');
		}
	}
	
	/**
	 * Deactivates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be deactivated
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @return void
	 */
	public function deactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = null, $redirect = null) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$reference->setDeactivated(true);
			$this->referenceRepository->update($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceDeactivated',$this->extensionName)),'',t3lib_message_AbstractMessage::OK);

			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if(isset($redirect) && isset($redirectController)){
			$this->redirect($redirect,$redirectController,$this->extensionName,Array('reference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
		} else {
			$this->redirect('list');
		}
	}
	
	/**
	 * Reactivates an deactivated reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be reactivated
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @return void
	 */
	public function reactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = null, $redirect = null) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			if($this->validateMaximumReferences()){
				//
			} else {
				$reference->setDeactivated(false);
				$this->referenceRepository->update($reference);
				$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceReactivated',$this->extensionName)),'',t3lib_message_AbstractMessage::OK);
			}
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if(isset($redirect) && isset($redirectController)){
			$this->redirect($redirect,$redirectController,$this->extensionName,Array('reference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
		} else {
			$this->redirect('list');
		}
	}
	
	/**
	 * Shows the delete view
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to delete
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @return void
	 * @dontvalidate $reference
	 */
	public function deleteAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = null, $redirect = null) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$this->referenceRepository->remove($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceRemoved',$this->extensionName)),'',t3lib_message_AbstractMessage::OK);
			
			$this->view->assign('redirectController', $redirectController);
			$this->view->assign('redirect', $redirect);
			$this->view->assign('reference', $reference);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if(isset($redirect) && isset($redirectController)){
			$this->redirect($redirect,$redirectController,$this->extensionName,Array('reference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Deletes an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to delete
	 * @return void
	 * @dontvalidate $reference
	 */
	public function removeAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = null, $redirect = null) {
		$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		if(isset($redirect) && isset($redirectController)){
			$this->redirect($redirect,$redirectController,$this->extensionName,Array('reference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
		} else {
			$this->redirect('index');
		}
	}
	
	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function showAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		$this->view->assign('reference', $reference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1));
		$this->view->assign('redirect','show');
		$this->view->assign('redirectController','Reference');
	}
	
	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function pdfAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		$this->view->assign('reference', $reference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1));
		$this->view->assign('redirect','show');
		$this->view->assign('redirectController','Reference');
	}
	
	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 * @param string $redirectController	The controller to redirect to
	 * @param string $redirect	The agency action to redirect to
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function previewAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference, $redirectController = null, $redirect = null) {
		$this->view->assign('newReference', $newReference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
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
				return $this->localization->translate('referenceUpdateFailed', $this->extensionName);
			case 'createAction' :
				return $this->localization->translate('referenceCreateFailed', $this->extensionName);
			default :
				return parent::getErrorFlashMessage();
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference
	 * @return boolean True, if there were no errors
	 */
	private function handleFiles(&$reference){

		$ok = true;
		
		if(is_array($_FILES['tx_typo3agencies_pi1'])){
			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$all_files = Array();
			$all_files['webspace']['allow'] = '*';
			$all_files['webspace']['deny'] = '';
			$fileFunc->init('', $all_files);
			$galleryImages = Array();
			$formName = array_shift(array_keys($_FILES['tx_typo3agencies_pi1']['error']));
			foreach($_FILES['tx_typo3agencies_pi1']['error'][$formName] as $key => $error){
				if(is_array($error)){
					// must be an image for the gallery
					foreach ($error as $error_key => $error_error){
						if($error_error == 0){
							if( strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key][$error_key],'image/png') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key][$error_key],'image/jpg') === 0){
								if($_FILES['tx_typo3agencies_pi1']['size'][$formName][$key][$error_key] < 500000){
									$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key][$error_key];
									$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key]), $this->settings['uploadPath']);
									t3lib_div::upload_copy_move($theFile,$theDestFile);
									$galleryImages[] = basename($theDestFile);
								} else {
									$ok = false;
									$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key], $this->localization->translate('wrongFileSize',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
								}
							} else {
								$ok = false;
								$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key], $this->localization->translate('wrongFileType',$this->extensionName)),'',t3lib_message_AbstractMessage::ERROR);
							}
						}
					}
				} else {
					if($error == 0){
						if($key == 'screenshot'){
							if( strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key],'image/png') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key],'image/jpg') === 0){
								if($_FILES['tx_typo3agencies_pi1']['size'][$formName][$key] < 500000){
									$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
									$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
									t3lib_div::upload_copy_move($theFile,$theDestFile);
									$reference->setScreenshot(basename($theDestFile));
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
			$exisitingScreenshots = t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1);
			$reference->setScreenshotGallery(implode(',',array_merge($galleryImages,$exisitingScreenshots)));
		}
		return $ok;
	}

}

?>