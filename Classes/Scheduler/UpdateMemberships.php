<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Daniel Lienert <daniel@lienert.cc>
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
 * Scheduler job to update memberships
 *
 * = Examples =
 */
class Tx_Typo3Agencies_Scheduler_UpdateMemberships extends tx_scheduler_Task {

	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_AgencyRepository
	 */
	protected $agencyRepository;


	/**
	 * @var Tx_Typo3Agencies_Domain_Repository_ReferenceRepository
	 */
	protected $referenceRepository;


	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * This is the main method that is called when a task is executed
	 * It MUST be implemented by all classes inheriting from this one
	 * Note that there is no error handling, errors and failures are expected
	 * to be handled and logged by the client implementations.
	 * Should return true on successful execution, false on error.
	 *
	 * @return boolean	Returns true on successful execution, false on error
	 */
	public function execute() {

		$this->setupFramework();
		$this->setupRepositories();

		$this->updateAgenciesByMemberDataAction();

		$this->tearDownFramework();

		return true;
	}


	/**
	 * Gets the memberData from association.typo3.org and uses this data to
	 * - update membership level and amount of caseStudies allowed in agency
	 * - deactivates caseStudies if allowed amount is lower than activated caseStudies
	 *
	 * @return void
	 */
	protected function updateAgenciesByMemberDataAction() {

		$memberDataUtility = $this->objectManager->get('Tx_Typo3Agencies_Utility_MemberData');
		$memberDataArray = $memberDataUtility->getAllMemberData();
		foreach($memberDataArray as $memberData) {

			$agency = $this->agencyRepository->findOneByCode($memberData['code']);

			if($agency) { /** @var $agency Tx_Typo3Agencies_Domain_Model_Agency */

				$approved = $memberData['isApproved'] == 1 ? true : false;
				$allowedCaseStudies = $approved ? (int) $memberData['caseStudies'] : 0;

				$agency->setApproved($approved);
				$agency->setCaseStudies($allowedCaseStudies);
				$agency->setMember($memberData['membershipLevel']);


				$references = $this->referenceRepository->findAllByAgency($agency);

				foreach($references as $reference) { /** @var $reference Tx_Typo3Agencies_Domain_Model_Reference */

					if($allowedCaseStudies <= 0) {
						$reference->setDeactivated(true);
					}

					$allowedCaseStudies--;
					$this->referenceRepository->update($reference);
				}

				$this->agencyRepository->update($agency);
			}
		}
		$GLOBALS['TSFE']->clearPageCacheContent_pidList($this->clearCachePids);
	}



	/**
	 * @return void
	 */
	protected function setupFramework() {


		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['typo3_agencies']);
		$storagePid = (int) $extConf['storagePid'];
		$this->clearCachePids = $extConf['clearCachePids'];

		define('TYPO3_MODE','FE');

		require_once(PATH_t3lib.'class.t3lib_div.php');
		require_once(PATH_t3lib.'class.t3lib_extmgm.php');
		require_once(PATH_t3lib.'config_default.php');
		require_once(PATH_typo3conf.'localconf.php');
		require_once(PATH_tslib.'class.tslib_fe.php');
		require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib.'class.t3lib_page.php');
		require_once(PATH_tslib.'class.tslib_content.php');

		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');

		require_once(PATH_t3lib.'class.t3lib_cs.php');

		if (!defined ('TYPO3_db'))  die ('The configuration file was not included.');

		require_once(PATH_t3lib.'class.t3lib_db.php');

		$TYPO3_DB = t3lib_div::makeInstance('t3lib_DB');
		$GLOBALS['TYPO3_DB'] = $TYPO3_DB;

		require_once(PATH_t3lib.'class.t3lib_timetrack.php');
		$GLOBALS['TT'] = new t3lib_timeTrack;

		// ***********************************
		// Creating a fake $TSFE object
		// ***********************************
		$id = isset($HTTP_GET_VARS['id'])?$HTTP_GET_VARS['id']:0;
		$GLOBALS['TSFE'] = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $id, '0', 1, '', '','','');
		$GLOBALS['TSFE']->connectToMySQL();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->fe_user->dontSetCookie = true;
		$GLOBALS['TSFE']->fetch_the_id();
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->tmpl->getFileName_backPath = PATH_site;
		$GLOBALS['TSFE']->forceTemplateParsing = 1;
		$GLOBALS['TSFE']->getConfigArray();
		$GLOBALS['TSFE']->newCObj();

		$configuration = array(
			'extensionName' => 'Typo3Agencies',
			'pluginName' => 'tx_typo3agencies_pi2',
			'settings' => '< plugin.tx_typo3agencies.settings',
			'persistence' => '< plugin.tx_typo3agencies.persistence',
			'view' => '< plugin.tx_typo3agencies.view',
			'persistence.' => array(
				'storagePid' => $storagePid
			),
			'_LOCAL_LANG' => '< plugin.tx_typo3agencies._LOCAL_LANG'
		);

		$bootstrap = t3lib_div::makeInstance('Tx_Extbase_Core_Bootstrap');
		$bootstrap->initialize($configuration);

		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
	}


	/**
	 * Setup the repositories
	 *
	 * @return void
	 */
	protected function setupRepositories() {
		$this->agencyRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_AgencyRepository');
		$this->referenceRepository = t3lib_div::makeInstance('Tx_Typo3Agencies_Domain_Repository_ReferenceRepository');
	}


	/**
	 * @return void
	 */
	protected function tearDownFramework() {
		 $this->objectManager->get('Tx_Extbase_Persistence_Manager')->persistAll();
       $this->objectManager->get('Tx_Extbase_Reflection_Service')->shutdown();
	}
}

?>