<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Contains hooks for the TYPO3 data handler (t3lib_TCEmain) and the ImportMembersTask of the
 * t3o_membership Extension. Updates agency data with related member data.
 *
 * @author Alexander Stehlik <alexander.stehlik.deleteme@gmail.com>
 */
class Tx_Typo3Agencies_Hooks_DataHandler
{
    /**
     * Mapping of membership UID to agency membership type.
     *
     * @var array
     */
    protected $memberPslMapping = array(
        1 => 4, // Platinum
        2 => 3, // Gold
        3 => 2, // Silver
        4 => 1, // Bronze
    );

    /**
     * Is called in the ImportMemberTask of the t3o_membership Extension after the
     * data of a member was updated.
     *
     * @param int $memberUid
     * @param array $memberData
     */
    public function postUpdateMemberData($memberUid, array $memberData)
    {
        $db = $this->getDatabaseConnection();
        $agencyUidData = $db->exec_SELECTgetSingleRow(
            'uid',
            'tx_typo3agencies_domain_model_agency',
            'related_member=' . (int)$memberUid
        );
        if (empty($agencyUidData)) {
            return;
        }
        $this->updateRelatedAgencyData($agencyUidData['uid'], $memberData);
    }

    /**
     * After updates in the tx_typo3agencies_domain_model_agency table
     * the agency data is updated with related member data, if a related member is configured.
     *
     * @param string $status
     * @param string $table
     * @param int|string $id
     * @param array $fieldArray
     * @param t3lib_TCEmain $dataHandler
     */
    public function processDatamap_afterDatabaseOperations(
        $status,
        $table,
        $id,
        /** @noinspection PhpUnusedParameterInspection */
        array $fieldArray,
        t3lib_TCEmain $dataHandler
    ) {
        if ($table !== 'tx_typo3agencies_domain_model_agency') {
            return;
        }
        if ($status === 'new') {
            $uid = (int)$dataHandler->substNEWwithIDs[$id];
        } else {
            $uid = (int)$id;
        }
        $this->synchronizeAgencyWithMemberData($uid);
    }

    /**
     * @return t3lib_db
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Checks if the agency with the given UID has a related member.
     * If a related member is found the agency data will be updated with the data of the related member.
     *
     * @param int $agencyUid
     */
    protected function synchronizeAgencyWithMemberData($agencyUid)
    {
        $db = $this->getDatabaseConnection();
        $agencyData = $db->exec_SELECTgetSingleRow(
            'related_member',
            'tx_typo3agencies_domain_model_agency',
            'uid=' . (int)$agencyUid
        );
        if (empty($agencyData['related_member'])) {
            return;
        }
        $relatedMemberData = $db->exec_SELECTgetSingleRow(
            '*',
            'tx_t3omembership_domain_model_member',
            'uid=' . (int)$agencyData['related_member']
        );
        if (empty($relatedMemberData)) {
            return;
        }
        $this->updateRelatedAgencyData($agencyUid, $relatedMemberData);
    }

    /**
     * Maps the given member data to agency fields and updates the agency data in the database.
     *
     * @param int $agencyUid
     * @param array $memberData
     */
    protected function updateRelatedAgencyData($agencyUid, array $memberData)
    {
        $agencyData = array(
            'name' => $memberData['name'],
            'address' => $memberData['address'],
            'zip' => $memberData['zip'],
            'city' => $memberData['city'],
            'country' => $memberData['country'],
            'link' => $memberData['url'],
            'first_name' => $memberData['firstname'],
            'last_name' => $memberData['lastname'],
            'endtime' => $memberData['endtime'],
            'contact' => $memberData['firstname'] . ' ' . $memberData['lastname'],
            'member' => $this->memberPslMapping[$memberData['membership']],
        );
        $this->getDatabaseConnection()->exec_UPDATEquery(
            'tx_typo3agencies_domain_model_agency',
            'uid=' . (int)$agencyUid,
            $agencyData
        );
    }
}
