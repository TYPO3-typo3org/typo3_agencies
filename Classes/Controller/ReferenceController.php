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

/**
 * The reference controller for the Reference package
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Typo3Agencies_Controller_ReferenceController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * @var Tx_Typo3Agencies_Domain_Model_ReferenceRepository
	 */
	protected $referenceRepository;
	
	/**
	 * @var Tx_Typo3Agencies_Domain_Model_AgencyRepository
	 */
	protected $agencyRepository;
	
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
	protected $localization;
	
	/**
	 * @var boolean Show deactivated references
	 */
	protected $showDeactivated;

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->referenceRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_ReferenceRepository');
		$this->agencyRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_AgencyRepository');
		$this->localization = t3lib_div::makeInstance('Tx_Extbase_Utility_Localization');
		$this->showDeactivated = false;
		if($GLOBALS['TSFE']->loginUser){
			$uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
			$result = $this->agencyRepository->findByAdministrator($uid);
			if(count($result) > 0){
				$this->administrator = $uid;
				$this->agency = current($result);
				if($this->agency->getAdministrator() == $this->administrator){
					$this->showDeactivated = true;
				}
			}
		}
	}

	/**
	 * Index action for this controller. Displays a list of references.
	 *
	 * @var Tx_Typo3Agencies_Domain_Model_Filter $filter The filter to filter
	 * @return string The rendered view
	 * @dontvalidate $filter
	 */
	public function indexAction(Tx_Typo3Agencies_Domain_Model_Filter $filter = null) {
		if($this->settings['showAgencyIfLoggedIn']==1 && $this->administrator>0){
			 $this->redirect('show','Agency');
		} else {
			
			$allowedCategories = $this->getCategories();
			$allowedIndustries = $this->getIndustries();
			$allowedCompanySizes = $this->getCompanySizes();
			
			$this->removeNotSet($allowedCategories, $allowedIndustries, $allowedCompanySizes);
			
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
	
			$this->pager->setItemsPerPage($this->settings['pageBrowser']['itemsPerPage']);
			$offset = ($this->pager->getPage() - 1) * $this->pager->getItemsPerPage();
			$count = 0;
			if($filter == null){
				$this->filter = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Filter');
				$references = $this->referenceRepository->findAllByFilter($this->filter,null,null,false,$this->showDeactivated);
				$count = count($references);
				$this->view->assign('references', $this->referenceRepository->findAllByRange($offset, $this->pager->getItemsPerPage(), $this->showDeactivated));
			} else {
				$this->filter = $filter;
				$references = $this->referenceRepository->findAllByFilter($this->filter,null,null,false,$this->showDeactivated);
				$count = count($references);
				$this->filter->setResultCount($count);
				$this->view->assign('references', $this->referenceRepository->findAllByFilter($this->filter, $offset, $this->pager->getItemsPerPage(), false, $this->showDeactivated));
			}
			
			$this->pager->setCount($count);
			$this->view->assign('pager', $this->pager);
			$this->view->assign('filter', $this->filter);
			$request = t3lib_div::makeInstance('Tx_Extbase_MVC_Web_Request');
			$request->setBaseUri($_SERVER['HTTP_HOST']);
			$request->setFormat('json');

//			$builder=t3lib_div::makeInstance('Tx_Extbase_MVC_Web_Routing_UriBuilder');
//			$builder->setRequest($request);
//			$uri = $builder->setTargetPageType(124)->buildFrontendUri();
			$uri = '';

			$this->view->assign('ajaxUrl', $uri);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('agency', $this->agency);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
		}
	}
	
	public function categoriesAction(){
		$this->request->setFormat('json');
		
		$allowedCategories = $this->getCategories();
		$allowedIndustries = $this->getIndustries();
		$allowedCompanySizes = $this->getCompanySizes();
		
		$this->removeNotSet($allowedCategories, $allowedIndustries, $allowedCompanySizes);
		
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
	
	private function removeNotSet(&$allowedCategories, &$allowedIndustries, &$allowedCompanySizes){
		$category = 0;
		if($this->request->hasArgument('category')){
			$category = intval($this->request->getArgument('category')); // 5
		}
		$industry = 0;
		if($this->request->hasArgument('industry')){
			$industry = intval($this->request->getArgument('industry')); // 4
		}
		$companySize = 0;
		if($this->request->hasArgument('companySize')){
			$companySize = intval($this->request->getArgument('companySize')); // 4
		}
		
		$remove = Array();
		for($i=1;$i<count($allowedCategories); $i++){
			$count = $this->referenceRepository->countByOption($i,$industry,$companySize,$this->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCategories = array_diff_key($allowedCategories,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedIndustries); $i++){
			$count = $this->referenceRepository->countByOption($category,$i,$companySize,$this->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedIndustries = array_diff_key($allowedIndustries,$remove);
		$remove = Array();
		for($i=1;$i<count($allowedCompanySizes); $i++){
			$count = $this->referenceRepository->countByOption($category,$industry,$i,$this->showDeactivated);
			if($count == 0){
				$remove[$i] = 'remove';
			}
		}
		$allowedCompanySizes = array_diff_key($allowedCompanySizes,$remove);
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
			$this->view->assign('categories', $this->getCategories(false));
			$this->view->assign('industries', $this->getIndustries(false));
			$this->view->assign('companySizes', $this->getCompanySizes(false));
			$this->view->assign('pagesList', $this->getPages(false));
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
			$count = $this->referenceRepository->countByAgency($this->agency);
			if($this->agency->getMember() == 1){
				$this->view->assign('referenceMaxReached', $count >= $this->settings['premiumMax']);
			} else if($this->agency->getMember() == 2){
				$this->view->assign('referenceMaxReached', $count >= $this->settings['platinMax']);
			} else {
				$this->view->assign('referenceMaxReached', $count >= $this->settings['normalMax']);
			}
		}
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
			if($this->agency->getMember() == 1){
				$referenceMaxReached = $this->settings['premiumMax'];
			} else if($this->agency->getMember() == 2){
				$referenceMaxReached = $this->settings['platinMax'];
			} else {
				$referenceMaxReached = $this->settings['normalMax'];
			}
			if($count < $referenceMaxReached){
				$this->handleFiles($newReference);
				$newReference->setAgency($this->agency);
				if($preview == 'Preview'){
					$this->view->assign('agency', $this->agency);
					$this->view->assign('reference', $newReference);
					$this->view->assign('administrator', $this->administrator);
					$this->view->assign('uploadPath', $this->settings['uploadPath']);
					$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$newReference->getScreenshotGallery(),1));
				} else {
					$this->referenceRepository->add($newReference);
					$this->flashMessages->add(str_replace('%NAME%', $newReference->getTitle(), $this->localization->translate('referenceCreated',$this->extensionName)));
				}
			} else {
				$this->flashMessages->add($this->localization->translate('notAllowed',$this->extensionName));
				$this->flashMessages->add($this->localization->translate('referenceMaxReached',$this->extensionName));
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
	 * @return string Form for editing the existing reference
	 * @dontvalidate $reference
	 * @dontvalidate $screenshot
	 */
	public function editAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $screenshot = false) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			if($screenshot == 1){
				$reference->setScreenshot('');
				$this->referenceRepository->update($reference);
			} else if(is_string($screenshot)){
				$newGallery = str_replace($screenshot, '', $reference->getScreenshotGallery());
				$reference->setScreenshotGallery(implode(',',t3lib_div::trimExplode(',',$newGallery,1)));
			}
			$this->handleFiles($reference);
			$this->view->assign('reference', $reference);
			$this->view->assign('categories', $this->getCategories(false));
			$this->view->assign('industries', $this->getIndustries(false));
			$this->view->assign('companySizes', $this->getCompanySizes(false));
			$this->view->assign('pagesList', $this->getPages(false));
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1));
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Updates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference A not yet persisted clone of the original reference containing the modifications
	 * @return void
	 */
	public function updateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$this->handleFiles($reference);
			$this->referenceRepository->update($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceUpdated',$this->extensionName)));
		}
		$this->redirect('index');
	}
	
	/**
	 * Deactivates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be deactivated
	 * @return void
	 */
	public function deactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$reference->setDeactivated(true);
			$this->referenceRepository->update($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceDeactivated',$this->extensionName)));
		}
		$this->redirect('list');
	}
	
	/**
	 * Reactivates an deactivated reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be reactivated
	 * @return void
	 */
	public function reactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$reference->setDeactivated(false);
			$this->referenceRepository->update($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceReactivated',$this->extensionName)));
		}
		$this->redirect('list');
	}
	
	/**
	 * Shows the delete view
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to delete
	 * @return void
	 * @dontvalidate $reference
	 */
	public function deleteAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$this->view->assign('reference', $reference);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
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
	public function removeAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		if($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()){
			$this->referenceRepository->remove($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceRemoved',$this->extensionName)));
		}
		$this->redirect('index');
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
		
	}
	
	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function previewAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference) {
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
				return 'Could not update the reference:';
			case 'createAction' :
				return 'Could not create the new reference:';
			default :
				return parent::getErrorFlashMessage();
		}
	}
	
	private function getCategories($includeDescription = true){
		$values = Array(0 => $this->localization->translate('category',$this->extensionName),
					1 => $this->localization->translate('category1',$this->extensionName),
					2 => $this->localization->translate('category2',$this->extensionName),
					3 => $this->localization->translate('category3',$this->extensionName),
					4 => $this->localization->translate('category4',$this->extensionName),
					5 => $this->localization->translate('category5',$this->extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	private function getIndustries($includeDescription = true){
		$values = Array(0 => $this->localization->translate('industry',$this->extensionName),
					1 => $this->localization->translate('industry1',$this->extensionName),
					2 => $this->localization->translate('industry2',$this->extensionName),
					3 => $this->localization->translate('industry3',$this->extensionName),
					4 => $this->localization->translate('industry4',$this->extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;	
	}
	
	private function getCompanySizes($includeDescription = true){
		$values = Array(0 => $this->localization->translate('size',$this->extensionName),
					1 => $this->localization->translate('size1',$this->extensionName),
					2 => $this->localization->translate('size2',$this->extensionName),
					3 => $this->localization->translate('size3',$this->extensionName),
					4 => $this->localization->translate('size4',$this->extensionName));
		if(!$includeDescription){
			unset($values[0]);
		}
		return $values;
	}
	
	private function getPages($includeDescription = true){
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
	
	private function handleFiles(&$reference){
		if(is_array($_FILES['tx_typo3agencies_pi1'])){
			
			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$all_files = Array();
			$all_files['webspace']['allow'] = '*';
			$all_files['webspace']['deny'] = '';
			$fileFunc->init('', $all_files);
			$galleryImages = Array();
			$formName = array_shift(array_keys($_FILES['tx_typo3agencies_pi1']['error']));
			foreach($_FILES['tx_typo3agencies_pi1']['error'][$formName] as $key => $error){
				if($error == 0 && strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key],'image') === 0){
					if($key == 'screenshot'){
						$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
						$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
						t3lib_div::upload_copy_move($theFile,$theDestFile);
						$reference->setScreenshot(basename($theDestFile));
					} else {
						// must be an image for the gallery
						$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
						$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
						t3lib_div::upload_copy_move($theFile,$theDestFile);
						$galleryImages[] = basename($theDestFile);
					}
				} else if ($error == 0 && $_FILES['tx_typo3agencies_pi1']['type'][$formName][$key] == 'application/pdf'){
					if($key == 'casestudy'){
						$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
						$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
						t3lib_div::upload_copy_move($theFile,$theDestFile);
						$reference->setCasestudy(basename($theDestFile));
					}
				}
			}
			$exisitingScreenshots = t3lib_div::trimExplode(',',$reference->getScreenshotGallery(),1);
			$reference->setScreenshotGallery(implode(',',array_merge($galleryImages,$exisitingScreenshots)));
		}
	}

}

?>