<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Alexander Stehlik <alexander.stehlik.deleteme@gmail.com>
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
 * Agency related CLI commands.
 */
class Tx_Typo3Agencies_Command_AgencyCommandController
{
    /**
     * Tires to find matching association members for all agencies.
     */
    public function connectMembersCommand()
    {
        /** @var Tx_Typo3Agencies_Hooks_DataHandler $dataHandlerHooks */
        $dataHandlerHooks = t3lib_div::makeInstance('Tx_Typo3Agencies_Hooks_DataHandler');
        $db = $this->getDatabaseConnection();
        $agencyResult = $db->exec_SELECTquery(
            'uid,name,member,related_member',
            'tx_typo3agencies_domain_model_agency',
            ''
        );
        $this->checkForDatabaseError($agencyResult);
        while ($agencyData = $db->sql_fetch_assoc($agencyResult)) {
            if ($agencyData['related_member'] > 0) {
                $this->println('Agency ' . $agencyData['name'] . ' already connected.');
                continue;
            }
            $memberData = $this->findRelatedMember($agencyData);
            if (empty($memberData)) {
                $this->println('Could not find a match for agency ' . $agencyData['name']);
                continue;
            }
            $updateResult = $db->exec_UPDATEquery(
                'tx_typo3agencies_domain_model_agency',
                'uid=' . (int)$agencyData['uid'],
                array('related_member' => (int)$memberData['uid'])
            );
            $this->checkForDatabaseError($updateResult);
            $dataHandlerHooks->updateRelatedAgencyData($agencyData['uid'], $memberData);
            $this->println('Connected agency ' . $agencyData['name']);
        }
    }

    /**
     * If the given result is false a RuntimeException with the DB error message is thrown.
     *
     * @param mixed $result
     */
    protected function checkForDatabaseError($result)
    {
        if ($result) {
            return;
        }

        throw new \RuntimeException('Database error: ' . $this->getDatabaseConnection()->sql_error());
    }

    /**
     * Tries to find a related member for the given agency data.
     * Currently the agency name and the membership type are matched.
     *
     * @param array $agencyData
     * @return array
     */
    protected function findRelatedMember(array $agencyData)
    {
        $db = $this->getDatabaseConnection();
        $result = $db->exec_SELECTquery(
            '*',
            'tx_t3omembership_domain_model_member',
            'name=' . $db->fullQuoteStr($agencyData['name'], 'tx_t3omembership_domain_model_member') .
            ' AND membership=' . (int)$this->getMatchingMembershipUid($agencyData['member'])
        );
        $this->checkForDatabaseError($result);
        return $db->sql_fetch_assoc($result);
    }

    /**
     * @return t3lib_db
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Returns the matching membership UID of the given agency membership value.
     *
     * @param int $agencyMembership
     * @return int
     */
    protected function getMatchingMembershipUid($agencyMembership)
    {
        return array_search($agencyMembership, Tx_Typo3Agencies_Hooks_DataHandler::$memberPslMapping);
    }

    /**
     * Echoes the given line and a linebreak.
     *
     * @param string $line
     */
    protected function println($line)
    {
        echo $line . "\n";
    }
}
