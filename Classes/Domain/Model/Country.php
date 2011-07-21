<?php
/**
 *
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @scope prototype
 * @entity
 */

class Tx_Typo3Agencies_Domain_Model_Country extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * @var int
	 */
	protected $uid;

	/**
	 * @var string
	 */
	protected $cnShortEn;
	/**
	 * Constructor. Initializes all Tx_Extbase_Persistence_ObjectStorage instances.
	 */
	public function __construct() {}

	/**
	 * @param $uid the $uid to set
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @param $cnShortEn the $cnShortEn to set
	 */
	public function setCnShortEn($cnShortEn) {
		$this->cnShortEn = $cnShortEn;
	}

	/**
	 * @return the $cnShortEn
	 */
	public function getCnShortEn() {
		return $this->cnShortEn;
	}
}
?>