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
 * View helper which renders the flash messages (if there are any) as an unsorted list.
 *
 * In case you need custom Flash Message HTML output, please write your own ViewHelper for the moment.
 *
 *
 * = Examples =
 *
 * <code title="Simple">
 * <f:flashMessages />
 * </code>
 * <output>
 * An ul-list of flash messages.
 * </output>
 *
 * <code title="Output with custom css class">
 * <f:flashMessages class="specialClass" />
 * </code>
 * <output>
 * <ul class="specialClass">
 *  ...
 * </ul>
 * </output>
 *
 * <code title="TYPO3 core style">
 * <f:flashMessages renderMode="div" />
 * </code>
 * <output>
 * <div class="typo3-messages">
 *   <div class="typo3-message message-ok">
 *     <div class="message-header">Some Message Header</div>
 *     <div class="message-body">Some message body</div>
 *   </div>
 *   <div class="typo3-message message-notice">
 *     <div class="message-body">Some notice message without header</div>
 *   </div>
 * </div>
 * </output>
 *
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 * @api
 */
class Tx_Typo3Agencies_ViewHelpers_FlashMessagesViewHelper extends Tx_Fluid_ViewHelpers_FlashMessagesViewHelper {

	/*
	 * Renders the flash messages as nested divs
	 *
	 * @param array $flashMessages array<t3lib_FlashMessage>
	 * @return string
	 */
	protected function renderDiv(array $flashMessages) {
		$this->tag->setTagName('div');
		if ($this->arguments->hasArgument('class')) {
			$this->tag->addAttribute('class', $this->arguments['class']);
		} else {
			$this->tag->addAttribute('class', 'typo3-messages');
		}
		$tagContent = '';
		$severity = Array(
				t3lib_message_AbstractMessage::NOTICE	=> Array('class'=>'','title'=>''),
				t3lib_message_AbstractMessage::INFO 	=> Array('class'=>'','title'=>''),
				t3lib_message_AbstractMessage::OK 		=> Array('class'=>'congratulations','title'=>'Congratulations!'),
				t3lib_message_AbstractMessage::WARNING 	=> Array('class'=>'warning','title'=>'Warning!'),
				t3lib_message_AbstractMessage::ERROR	=> Array('class'=>'error','title'=>'Error notification'),
			);
		foreach ($flashMessages as $singleFlashMessage) {
			$s = $singleFlashMessage->getSeverity();
			$tagContent .= '<div class="b-message '.strtolower($severity[$s]['class']).'">';
			
			if($s == t3lib_message_AbstractMessage::OK || $s == t3lib_message_AbstractMessage::WARNING || $s == t3lib_message_AbstractMessage::ERROR){
				$tagContent .= '<p>'.$severity[$s]['title'].'</p>';
			}
			$tagContent .= '<p>'.$singleFlashMessage->getMessage().'</p></div>';
		}
		$this->tag->setContent($tagContent);
		return $this->tag->render();
	}
}

?>