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
	 * @var Tx_Typo3Agencies_Domain_Model_Pager
	 */
	protected $pager;

	/**
	 * Index (cacheable) action for this controller. Displays a list of references.
	 *
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Category $category
	 * @param Tx_Typo3Agencies_Domain_Model_Industry $industry
	 * @param Tx_Typo3Agencies_Domain_Model_Revenue $revenue
	 * @param integer $membershipType
	 * @param integer $listed
	 */
	public function indexAction(Tx_Typo3Agencies_Domain_Model_Category $category = NULL, Tx_Typo3Agencies_Domain_Model_Industry $industry = NULL, Tx_Typo3Agencies_Domain_Model_Revenue $revenue = NULL, $membershipType = -1, $listed = -1) {
		if ($this->settings['showAgencyIfLoggedIn'] == 1 && $this->administrator > 0) {
			$this->redirect('show', 'Agency');
		} else {
			if ($listed == -1) {
				$fortune500 = FALSE;
			} else {
				$fortune500 = ($listed == 1);
			}

			$this->pager = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Pager');
			$this->pager->setItemsPerPage($this->settings['pageBrowser']['itemsPerPage']);

			if ($this->request->hasArgument('page')) {
				$this->pager->setPage($this->request->getArgument('page'));
				if ($this->pager->getPage() < 1) {
					$this->pager->setPage(1);
				}
			}
			$allReferences = $this->referenceRepository->findByFilterSelection($category, $industry, $revenue, $membershipType, $fortune500, NULL, NULL, FALSE);
			$selectedReferences = $this->referenceRepository->findByFilterSelection($category, $industry, $revenue, $membershipType, $fortune500, $this->pager->getOffset(), $this->pager->getItemsPerPage(), FALSE);

			$this->pager->setCount($allReferences->count());

			$this->view->assign('filterOptions', $this->buildDataForFilterModule($category, $industry, $revenue, $membershipType, $fortune500));
			$this->view->assign('pager', $this->pager);
			$this->view->assign('references', $selectedReferences);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('agency', $this->agency);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('redirect', 'index');
			$this->view->assign('redirectController', 'Reference');
		}
	}

	/**
	 * @param Tx_Typo3Agencies_Domain_Model_Category $category
	 * @param Tx_Typo3Agencies_Domain_Model_Industry $industry
	 * @param Tx_Typo3Agencies_Domain_Model_Revenue $revenue
	 * @param $membershipStatus
	 * @param boolean $fortune500
	 */
	protected function buildDataForFilterModule(Tx_Typo3Agencies_Domain_Model_Category $category = NULL, Tx_Typo3Agencies_Domain_Model_Industry $industry = NULL, Tx_Typo3Agencies_Domain_Model_Revenue $revenue = NULL, $membershipStatus = -1, $fortune500 = FALSE) {

		$currentSelection = array();
		$currentSelection['category'] = $category;
		$currentSelection['industry'] = $industry;
		$currentSelection['revenue'] = $revenue;
		$currentSelection['listed'] = $fortune500;

		$filterOptions = array();
		$filteredPropertiesInReferences = array('category' => TRUE, 'industry' => TRUE, 'revenue' => TRUE, 'listed' => FALSE);

		/**
		 * Helper function to retrieve an id
		 * @param $column
		 *
		 * @return int
		 */
		$idFinder = function ($column) {
			if (empty($column)) {
				return NULL;
			} elseif (is_object($column)) {
				$id = $column->getUid();
			} else {
				$id = intval($column);
			}
			return $id;
		};

		/**
		 * Builds an array of URL arguments
		 */
		$buildUrlValue = function($currentSelection, $overrideColumn, $overrideValue) {
			$currentSelection[$overrideColumn] = $overrideValue;
			return $currentSelection;
		};

		foreach ($filteredPropertiesInReferences as $column => $isTable) {
			if ($isTable) {
				$where = 'ref.deactivated = 0';
				$whereParts = array($where);
				foreach (array_keys($filteredPropertiesInReferences) as $checkColumn) {
					if ($checkColumn == $currentSelection) {
						continue;
					}
					$id = $idFinder($currentSelection[$checkColumn]);
					if ($id !== NULL) {
						$whereParts[] = 'ref.' . $checkColumn . '=' . intval($id);
					}
				}
				$where = implode (' AND ', $whereParts);
				$resultSet = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'ref.' . $column . ' as value, COUNT(ref.' . $column . ') as count, content.title as label',
					'tx_typo3agencies_domain_model_reference ref LEFT JOIN tx_typo3agencies_domain_model_' . $column . ' content ON ref.' . $column . ' = content.uid',
					$where,
					'ref.' . $column
				);

				$default = array(
					'label' => $idFinder($currentSelection[$column]) !== NULL ? ' -- remove selection -- ' : ' -- please select --',
					'value' => NULL
				);
				array_unshift($resultSet, $default);
				foreach ($resultSet as $result) {
					$result['link'] = $this->uriBuilder->uriFor('index', $buildUrlValue($currentSelection, $column, $result['value']));
					if (!trim($result['label'])) {
						$result['label'] = 'unknown'; // @Todo Make language dependent
					}
					$filterOptions[$column][$result['value']] = $result;
					$filterOptions[$column][$result['value']]['value'] = intval($filterOptions[$column][$result['value']]['value']);
					$filterOptions[$column][$result['value']]['selected'] = ($idFinder($currentSelection[$column]) === $filterOptions[$column][$result['value']]['value']);
				}
			} else {
				if ($column == 'listed') {
					$filterOptions[$column]['selected'] = $currentSelection[$column];
					$filterOptions[$column]['link'] = $this->uriBuilder->uriFor('index', $buildUrlValue($currentSelection, $column, $currentSelection[$column] === TRUE ? -1 : 1));
				}
			}
		}
		return $filterOptions;
	}

	/**
	 * Displays a form for creating a new blog
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $newReference A fresh reference object taken as a basis for the rendering
	 *
	 * @return string An HTML form for creating a new reference
	 * @dontvalidate $newReference
	 * @dontverifyrequesthash
	 */
	public function newAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference = NULL) {
		if ($newReference == NULL) {
			$newReference = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Model_Reference');
		}
		if (!$this->agency) {
			$this->redirect('index');
		} else {
			$this->view->assign('newReference', $newReference);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('categories', $this->getCategories(FALSE));
			$this->view->assign('industries', $this->getIndustries(FALSE));
			$this->view->assign('revenues', $this->getRevenues(FALSE));
			$this->view->assign('pagesList', $this->getPages(FALSE));
			$this->view->assign('languagesList', $this->getLanguages(FALSE));
			$this->addCountries();
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('maxFiles', 3 - count(t3lib_div::trimExplode(',', $newReference->getScreenshotGallery(), 1)));
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $newReference->getScreenshotGallery(), 1));
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
		if ($reference->getAgency()->getAdministrator() == $this->administrator) {
			if ($reference->getSorting() != $sort['sort']) {
				$references = $this->referenceRepository->findAllByAgency($reference->getAgency(), TRUE);
				if ($sort['sort'] < $reference->getSorting()) {
					// moving up
					foreach ($references as $referenceRecord) {
						if ($referenceRecord->getSorting() >= $sort['sort'] && $referenceRecord->getSorting() < $reference->getSorting()) {
							$referenceRecord->setSorting($referenceRecord->getSorting() + 1);
							$this->referenceRepository->update($referenceRecord);
						}
					}
				} else {
					// moving down
					foreach ($references as $referenceRecord) {
						if ($referenceRecord->getSorting() <= $sort['sort'] && $referenceRecord->getSorting() > $reference->getSorting()) {
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

	private function validateMaximumReferences() {

		$count = $this->referenceRepository->countByAgency($this->agency);
		//1 = Bronze member
		//2 = Silver member
		//3 = Gold member
		//4 = Platinum member
		$maxReached = FALSE;

		if ($count >= $this->agency->getCasestudies()) {
			$maxReached = TRUE;
			$this->flashMessages->add($this->localization->translate('referenceMaxReached', $this->extensionName), '', t3lib_message_AbstractMessage::INFO);
		}

		return $maxReached;
	}

	/**
	 * Creates a new reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $newReference A fresh reference object which has not yet been added to the repository
	 * @param string $preview
	 * @param boolean $screenshot
	 *
	 * @dontvalidate $preview
	 * @dontvalidate $screenshot
	 * @dontverifyrequesthash
	 * @return void
	 */
	public function createAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference, $preview = NULL, $screenshot = FALSE) {
		if ($this->agency) {
			$count = $this->referenceRepository->countByAgency($this->agency);
			$referenceMaxReached = $this->agency->getCasestudies();
			//if($count < $referenceMaxReached){
			if ($this->handleFiles($newReference)) {
				$newReference->setAgency($this->agency);
				if ($preview == 'Preview') {
					$this->view->assign('agency', $this->agency);
					$this->view->assign('reference', $newReference);
					$this->view->assign('administrator', $this->administrator);
					$this->view->assign('uploadPath', $this->settings['uploadPath']);
					$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $newReference->getScreenshotGallery(), 1));
					$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				} else {
					$newReference->setSorting($count);
					$newReference->setDeactivated(TRUE);

					$this->referenceRepository->add($newReference);
					$this->flashMessages->add(str_replace('%NAME%', $newReference->getTitle(), $this->localization->translate('referenceCreated', $this->extensionName)), '', t3lib_message_AbstractMessage::OK);
					$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				}
			} else {
				$this->view->assign('agency', $this->agency);
				$this->view->assign('reference', $newReference);
				$this->view->assign('administrator', $this->administrator);
				$this->view->assign('uploadPath', $this->settings['uploadPath']);
				$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $newReference->getScreenshotGallery(), 1));
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				$this->redirect('create', 'Reference');
			}
			//} else {
			//	$this->flashMessages->add($this->localization->translate('notAllowed',$this->extensionName),'',t3lib_message_AbstractMessage::ERROR);
			//	$this->flashMessages->add($this->localization->translate('referenceMaxReached',$this->extensionName),'',t3lib_message_AbstractMessage::WARNING);
			//}
		}
		if ($preview != 'Preview') {
			$this->redirect('index');
		}
	}

	/**
	 * Edits an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be edited. This might also be a clone of the original reference already containing modifications if the edit form has been submitted, contained errors and therefore ended up in this action again.
	 * @param boolean $screenshot Delete the screenshot
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 *
	 * @return string Form for editing the existing reference
	 * @dontvalidate $reference
	 * @dontvalidate $screenshot
	 */
	public function editAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $screenshot = FALSE, $redirectController = NULL, $redirect = NULL) {
		$this->view->assign('redirectController', $redirectController);
		$this->view->assign('redirect', $redirect);
		if ($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()) {
			if ($screenshot == 1) {
				$reference->setScreenshot('');
				$this->referenceRepository->update($reference);
				$this->flashMessages->add($this->localization->translate('screenshotRemoved', $this->extensionName), '', t3lib_message_AbstractMessage::OK);
			} else if (is_string($screenshot)) {
				$newGallery = str_replace($screenshot, '', $reference->getScreenshotGallery());
				$reference->setScreenshotGallery(implode(',', t3lib_div::trimExplode(',', $newGallery, 1)));
				$this->flashMessages->add($this->localization->translate('screenshotRemoved', $this->extensionName), '', t3lib_message_AbstractMessage::OK);
			}
			$this->handleFiles($reference);
			$this->view->assign('reference', $reference);
			$this->view->assign('maxFiles', 3 - count(t3lib_div::trimExplode(',', $reference->getScreenshotGallery(), 1)));
			$this->view->assign('categories', $this->getCategories(FALSE));
			$this->view->assign('industries', $this->getIndustries(FALSE));
			$this->view->assign('revenues', $this->getRevenues(FALSE));
			$this->view->assign('pagesList', $this->getPages(FALSE));
			$this->view->assign('languagesList', $this->getLanguages(FALSE));
			$this->addCountries();
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $reference->getScreenshotGallery(), 1));
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Updates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference A not yet persisted clone of the original reference containing the modifications
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 * @param boolean $submit
	 *
	 * @return void
	 * @dontverifyrequesthash
	 */
	public function updateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = NULL, $redirect = NULL, $submit = NULL) {
		if ($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()) {
			if ($this->handleFiles($reference)) {
				//if($submit){
				$this->referenceRepository->update($reference);
				$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceUpdated', $this->extensionName)), '', t3lib_message_AbstractMessage::OK);
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				//} else {
				//	$this->redirect('preview','Reference',$this->extensionName,Array('newReference'=>$reference,'redirectController'=>$redirectController,'redirect'=>$redirect));
				//}
			} else {
				$this->referenceRepository->update($reference);
				$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
				$this->redirect('edit', 'Reference', $this->extensionName, Array('reference' => $reference, 'screenhot' => NULL, 'redirectController' => $redirectController, 'redirect' => $redirect));
			}
		}
		if (isset($redirect) && isset($redirectController)) {
			$this->redirect($redirect, $redirectController, $this->extensionName, Array('reference' => $reference, 'redirectController' => $redirectController, 'redirect' => $redirect));
		} else {
			$this->redirect('list');
		}
	}

	/**
	 * Deactivates an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be deactivated
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 *
	 * @return void
	 */
	public function deactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = NULL, $redirect = NULL) {
		if ($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()) {
			$reference->setDeactivated(TRUE);
			$this->referenceRepository->update($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceDeactivated', $this->extensionName)), '', t3lib_message_AbstractMessage::OK);

			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if (isset($redirect) && isset($redirectController)) {
			$this->redirect($redirect, $redirectController, $this->extensionName, Array('reference' => $reference, 'redirectController' => $redirectController, 'redirect' => $redirect));
		} else {
			$this->redirect('list');
		}
	}

	/**
	 * Reactivates an deactivated reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to be reactivated
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 *
	 * @return void
	 */
	public function reactivateAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = NULL, $redirect = NULL) {
		if ($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()) {
			if ($this->validateMaximumReferences()) {
				//
			} else {
				$reference->setDeactivated(FALSE);
				$this->referenceRepository->update($reference);
				$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceReactivated', $this->extensionName)), '', t3lib_message_AbstractMessage::OK);
			}
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if (isset($redirect) && isset($redirectController)) {
			$this->redirect($redirect, $redirectController, $this->extensionName, Array('reference' => $reference, 'redirectController' => $redirectController, 'redirect' => $redirect));
		} else {
			$this->redirect('list');
		}
	}

	/**
	 * Shows the delete view
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to delete
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 *
	 * @return void
	 * @dontvalidate $reference
	 */
	public function deleteAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = NULL, $redirect = NULL) {
		if ($this->agency && $reference->getAgency()->getUid() == $this->agency->getUid()) {
			$this->referenceRepository->remove($reference);
			$this->flashMessages->add(str_replace('%NAME%', $reference->getTitle(), $this->localization->translate('referenceRemoved', $this->extensionName)), '', t3lib_message_AbstractMessage::OK);

			$this->view->assign('redirectController', $redirectController);
			$this->view->assign('redirect', $redirect);
			$this->view->assign('reference', $reference);
			$this->view->assign('administrator', $this->administrator);
			$this->view->assign('uploadPath', $this->settings['uploadPath']);
			$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		}
		if (isset($redirect) && isset($redirectController)) {
			$this->redirect($redirect, $redirectController, $this->extensionName, Array('reference' => $reference, 'redirectController' => $redirectController, 'redirect' => $redirect));
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Deletes an existing reference
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to delete
	 *
	 * @return void
	 * @dontvalidate $reference
	 */
	public function removeAction(Tx_Typo3Agencies_Domain_Model_Reference $reference, $redirectController = NULL, $redirect = NULL) {
		$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->settings['clearCachePids']);
		if (isset($redirect) && isset($redirectController)) {
			$this->redirect($redirect, $redirectController, $this->extensionName, Array('reference' => $reference, 'redirectController' => $redirectController, 'redirect' => $redirect));
		} else {
			$this->redirect('index');
		}
	}

	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 *
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function showAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		$GLOBALS['TSFE']->page['title'] = $GLOBALS['TSFE']->indexedDocTitle =
			'Case Study "' . $reference->getTitle(). '" produced by ' . $reference->getAgency()->getName();
		$this->view->assign('reference', $reference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $reference->getScreenshotGallery(), 1));
		$this->view->assign('redirect', 'show');
		$this->view->assign('redirectController', 'Reference');
	}

	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 *
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function pdfAction(Tx_Typo3Agencies_Domain_Model_Reference $reference) {
		$this->view->assign('reference', $reference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $reference->getScreenshotGallery(), 1));
		$this->view->assign('redirect', 'show');
		$this->view->assign('redirectController', 'Reference');
	}

	/**
	 * Displays a reference and its company
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference The reference to display
	 * @param string $redirectController    The controller to redirect to
	 * @param string $redirect    The agency action to redirect to
	 *
	 * @dontvalidate $reference
	 * @return string The rendered view
	 */
	public function previewAction(Tx_Typo3Agencies_Domain_Model_Reference $newReference, $redirectController = NULL, $redirect = NULL) {
		$this->view->assign('newReference', $newReference);
		$this->view->assign('administrator', $this->administrator);
		$this->view->assign('uploadPath', $this->settings['uploadPath']);
		$this->view->assign('galleryImages', t3lib_div::trimExplode(',', $newReference->getScreenshotGallery(), 1));
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
	 *
	 * @param Tx_Typo3Agencies_Domain_Model_Reference $reference
	 *
	 * @return boolean True, if there were no errors
	 */
	private function handleFiles(&$reference) {

		$ok = TRUE;

		if (is_array($_FILES['tx_typo3agencies_pi1'])) {
			$fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
			$all_files = Array();
			$all_files['webspace']['allow'] = '*';
			$all_files['webspace']['deny'] = '';
			$fileFunc->init('', $all_files);
			$galleryImages = Array();
			$formName = array_shift(array_keys($_FILES['tx_typo3agencies_pi1']['error']));
			foreach ($_FILES['tx_typo3agencies_pi1']['error'][$formName] as $key => $error) {
				if (is_array($error)) {
					// must be an image for the gallery
					foreach ($error as $error_key => $error_error) {
						if ($error_error == 0) {
							if (strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key][$error_key], 'image/png') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key][$error_key], 'image/jpg') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key][$error_key], 'image/jpeg') === 0) {
								if ($_FILES['tx_typo3agencies_pi1']['size'][$formName][$key][$error_key] < 500000) {
									$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key][$error_key];
									$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key]), $this->settings['uploadPath']);
									t3lib_div::upload_copy_move($theFile, $theDestFile);
									$galleryImages[] = basename($theDestFile);
								} else {
									$ok = FALSE;
									$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key], $this->localization->translate('wrongFileSize', $this->extensionName)), '', t3lib_message_AbstractMessage::ERROR);
								}
							} else {
								$ok = FALSE;
								$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key][$error_key], $this->localization->translate('wrongFileType', $this->extensionName)), '', t3lib_message_AbstractMessage::ERROR);
							}
						}
					}
				} else {
					if ($error == 0) {
						if ($key == 'screenshot') {
							if (strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key], 'image/png') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key], 'image/jpg') === 0 || strpos($_FILES['tx_typo3agencies_pi1']['type'][$formName][$key], 'image/jpeg') === 0) {
								if ($_FILES['tx_typo3agencies_pi1']['size'][$formName][$key] < 500000) {
									$theFile = $_FILES['tx_typo3agencies_pi1']['tmp_name'][$formName][$key];
									$theDestFile = $fileFunc->getUniqueName($fileFunc->cleanFileName($_FILES['tx_typo3agencies_pi1']['name'][$formName][$key]), $this->settings['uploadPath']);
									t3lib_div::upload_copy_move($theFile, $theDestFile);
									$reference->setScreenshot(basename($theDestFile));
								} else {
									$ok = FALSE;
									$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key], $this->localization->translate('wrongFileSize', $this->extensionName)), '', t3lib_message_AbstractMessage::ERROR);
								}
							} else {
								$ok = FALSE;
								$this->flashMessages->add(str_replace('%FILE%', $_FILES['tx_typo3agencies_pi1']['name'][$formName][$key], $this->localization->translate('wrongFileType', $this->extensionName)), '', t3lib_message_AbstractMessage::ERROR);
							}
						}
					}
				}
			}
			$exisitingScreenshots = t3lib_div::trimExplode(',', $reference->getScreenshotGallery(), 1);
			$reference->setScreenshotGallery(implode(',', array_merge($galleryImages, $exisitingScreenshots)));
		}
		return $ok;
	}

}

?>