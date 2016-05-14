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
 * This class contains hooks for the TYPO3 Backend forms.
 *
 * @author Alexander Stehlik <alexander.stehlik.deleteme@gmail.com>
 */
class Tx_Typo3Agencies_Hooks_BackendForms
{
    /**
     * @var array
     */
    protected $agencyColumns = array(
        'name',
        'address',
        'zip',
        'city',
        'email',
        'link',
        'first_name',
        'last_name',
        'country',
        'contact',
        'member',
        'endtime'
    );

    /**
     * Sets all fields that are related to a member record to read-only mode,
     * if a related_member is configured in the current $row.
     *
     * @param string $table
     * @param array $row
     * @param t3lib_TCEforms $formEngine
     */
    public function getMainFields_preProcess($table, array $row, t3lib_TCEforms $formEngine)
    {
        if ($table !== 'tx_typo3agencies_domain_model_agency') {
            return;
        }
        if (empty($row['related_member'])) {
            return;
        }
        t3lib_div::loadTCA('tx_typo3agencies_domain_model_agency');
        foreach ($this->agencyColumns as $columnName) {
            $GLOBALS['TCA']['tx_typo3agencies_domain_model_agency']['columns'][$columnName]['config']['readOnly'] = true;
        }
    }
}