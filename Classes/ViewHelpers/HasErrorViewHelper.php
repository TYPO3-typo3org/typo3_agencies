<?php
/*                                                                        *
 * This script belongs to the FLOW3 package "Fluid".                      *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This ViewHelper enables conditional output in case of validation errors.
 * In case there are validation errors, the THEN part is displayed.
 * Otherwise, the ELSE part is displayd.
 *
 * This ViewHelper only works if it is used INSIDE an <f:form>-Tag which has an
 * object bound to it.
 *
 * <code>
 * <f:form name="myForm" object="{blog}">
 *   <!-- be sure to include the namespace of the ViewHelper -->
 *   <span class="{my:hasErrors(property: 'title', then: 'error', else: 'noError')">
 *     This span will have a CSS class "error" / "noError" attached to it, in case
 *     the property "title" of the blog object has errors / no errors.
 *   </span>
 * </f:form>
 * </code>
 *
 * @version $Id$
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @author Sebastian Kurfürst <sebastian@typo3.org>
 */
class Tx_Typo3Agencies_ViewHelpers_HasErrorViewHelper extends Tx_Fluid_ViewHelpers_Form_AbstractFormFieldViewHelper {

	/**
	 * Remove all other arguments, but only leave the "property" argument; as we do not want to build a tag.
	 */
	public function initializeArguments() {
		$this->registerArgument('property', 'string', 'Name of Object Property. If used in conjunction with <f:form object="...">, "name" and "value" properties will be ignored.', TRUE);
	}

	/**
	 * Render the ViewHelper.
	 *
	 * @param string $then The THEN part, displayed if there are validation errors for the given property.
	 * @param string $else The ELSE part, displayed if there are NO validation errors for the given property.
	 */
	public function render($then = '', $else = '') {
		$errors = $this->getErrorsForProperty();
		if (count($errors) > 0) {
			return $then;
		} else {
			return $else;
		}

	}
}
?>
