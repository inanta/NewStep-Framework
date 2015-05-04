<?php
/*
	Copyright (C) 2008 - 2012 Inanta Martsanto
	Inanta Martsanto (inanta@inationsoft.com)

	This file is part of NewStep Framework.

	NewStep Framework is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	NewStep Framework is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with NewStep Framework.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace NS\UI\Widget;

use NS\UI\UI;
use NS\UI\ScriptManager;
use NS\UI\Widget\ListBox;
use NS\IO\Validator\ValidatorManager;

/**
 *Create conected combo box or list box form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class ConnectedListBox extends UI {
	public $ListBox = array();

	function __construct($name, $value = null, $selected = null, $validators = null, $args = array()) {
		$ui_count = $this->getUICount(__CLASS__);
		$this->_attr['class'] = 'NS-ConnectedListBox-Container-' . $ui_count;

		$unmodified_args = $args;

		if(isset($args['class'])) { $this->_attr['class'] .= (' ' . $args['class']); unset($args['class']); }
		if(!empty($args)) $this->_attr = array_merge($this->_attr, $args);

		$dataIndex = 0;
		$connectedData = array();

		$args = $unmodified_args;

		foreach($value as $data) {
			$args['class'] = 'NS-ConnectedListBox-Item NS-ConnectedListBox-Item-' . $ui_count . (isset($args['class']) ? ' ' . $args['class'] : '');

			if($dataIndex != 0) $connectedData[$dataIndex] = $data;
			$this->ListBox[] = new ListBox($name, ($dataIndex == 0 ? $data : null), ($dataIndex == 0 ? $selected[0] : null), $validators, $args);

			++$dataIndex;
		}

		$content = '';
		foreach($this->ListBox as $key => $lb) {
			$this->ListBox[$key]->id .= '-' . $key;
			$this->ListBox[$key]->name .= '[]';
			$this->ListBox[$key]->UI = $this->ListBox[$key]->constructUI();
			//$content .= '<div class="NS-ConnectedListBox-Item">' . $this->ListBox[$key] . '</div>';
			$content .= $this->ListBox[$key];
		}

		$sm = ScriptManager::getInstance();
		$sm->addSource(NS_JQUERY_PATH);
		$sm->addScript(
			"jQuery(document).ready(function() {
				jQuery(function() {
					jQuery('.NS-ConnectedListBox-Item-$ui_count').change(function() {
						var lbData = " . json_encode($connectedData) . ";
						var lbSelected = " . json_encode($selected) . ";
						var currentElementID = jQuery(this).attr('id');
						var currentElementIndex = null;
						var siblingElementIndex = null;
						var elementIndex = 0;

						jQuery('[name=\"" . $name . "[]\"]').each(function() {
							if(jQuery(this).attr('id') == currentElementID) {
								currentElementIndex = elementIndex;
								siblingElementIndex = currentElementIndex + 1;
							}

							elementIndex++;
						});

						if(jQuery('#$name-' + siblingElementIndex)) {
							for(var i = siblingElementIndex, j = elementIndex; i < j; i++) {
								jQuery('#$name-' + i).html('');
								//jQuery('#$name-' + i).parents('.NS-ConnectedListBox-Item').hide();
								jQuery('#$name-' + i).hide();
							}

							if(typeof lbData[siblingElementIndex] != 'undefined' && typeof lbData[siblingElementIndex][jQuery('#$name-' + currentElementIndex).val()] != 'undefined') {
								jQuery.each(lbData[siblingElementIndex][jQuery('#$name-' + currentElementIndex).val()], function(key, value) {
									if(lbSelected != null && typeof lbSelected[siblingElementIndex] != 'undefined' && lbSelected[siblingElementIndex] == key) {
										jQuery('#$name-' + siblingElementIndex).append(jQuery('<option></option>').attr('value', key).attr('selected', 'selected').text(value));
									} else {
										jQuery('#$name-' + siblingElementIndex).append(jQuery('<option></option>').attr('value', key).text(value));
									}
								});

								jQuery('#$name-' + siblingElementIndex).show();
								jQuery('#$name-' + siblingElementIndex).trigger('change');
							}
						}
					});

					jQuery('#$name-0').trigger('change');
				});
			});"
		);

		parent::__construct($this->constructUI('div', true, $content));
	}

	function getListBox($index) {
		return (isset($this->ListBox[$index]) ? $this->ListBox[$index] : false);
	}
}
?>
