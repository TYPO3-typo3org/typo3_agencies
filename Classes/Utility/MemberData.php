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
 * Utility for fetching memberData
 *
 * = Examples =
 */
class Tx_Typo3Agencies_Utility_MemberData {


	protected $baseApiUrl = 'http://shopadmin.typo3.org/?type=31337';



	/**
	 * Get data array from association.TYPO3.org
	 *
	 * @param string $agencyCode
	 *
	 * @return array
	 */
	public function getMemberDataByCode($authCode = NULLL) {
		if($authCode === NULL) {
			return NULL;
		}

		$agencyData = t3lib_div::getURL($this->baseApiUrl . '&tx_ptassoc_admin%5Baction%5D=checkCode&tx_ptassoc_admin%5Bcode%5D=' . $authCode);
		$decodedData = $this->decodeMemberData($agencyData);
		if($decodedData[0] === NULL) {
			return NULL;
		} else {
			return $decodedData[0];
		}
	}


	/**
	 * @return void
	 */
	public function getAllMemberData() {
		$agencyData = t3lib_div::getURL($this->baseApiUrl . '&tx_ptassoc_admin%5Baction%5D=getAll');
		$agencyData = '[{"code":"4e7518fdb1588","isApproved":true,"caseStudies":5,"membership":"gold"},{"code":"4e7518fdbc43d","isApproved":true,"caseStudies":5,"membership":"gold"},{"code":"4e7518fdbcd63","isApproved":false,"caseStudies":100,"membership":"active"},{"code":"4e7518fdbd5ff","isApproved":false,"caseStudies":0,"membership":"bronze"}]';
		$decodedData = $this->decodeMemberData($agencyData);

		return $decodedData;
	}


	/**
	 * @param $rawData
	 * @return mixed
	 */
	public function decodeMemberData($rawData) {
		return json_decode($rawData,1);
	}

}

?>