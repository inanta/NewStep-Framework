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
use NS\UI\Widget\Hyperlink;
use NS\UI\ScriptManager;

/**
 *Manage multiple number for single form element
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class DynamicWidgetElement extends UI {
	function __construct($widget, $attrs, $max = 0, $add_text = 'Add More', $remove_text = 'Remove') {
		$name = $widget->name;
		$widget->name = $widget->name . '[]';
		$widget->UI = $widget->constructUI();
		$count = $this->getUICount(__CLASS__);

		$this->UI =
		'<div class="NS-DynamicWidgetElement-Container NS-DynamicWidgetElement-Container-' . $count . '">
			<div id="NS-DynamicWidgetElement-Element-Template-' . $count . '" class="NS-DynamicWidgetElement-Element NS-DynamicWidgetElement-Element-' . $count . '">' .
				$widget->UI . '&nbsp;
				<a class="NS-DynamicWidgetElement-Add NS-DynamicWidgetElement-Add-' . $count . '" href="#">' . $add_text . '</a>&nbsp;
				<span class="NS-DynamicWidgetElement-Remove-Container NS-DynamicWidgetElement-Remove-Container-' . $count . '" style="display:none;">
					<span class="NS-DynamicWidgetElement-Splitter NS-DynamicWidgetElement-Splitter-' . $count . '">|</span>&nbsp;
					<a class="NS-DynamicWidgetElement-Remove NS-DynamicWidgetElement-Remove-' . $count . '" href="#">' . $remove_text . '</a>
				</span>
			</div>
		</div>';

		$sm = ScriptManager::getInstance();
		$sm->addSource(NS_JQUERY_PATH);
		$sm->addScript(
			"jQuery(document).ready(function() {
				document.NS_DynamicWidgetElement_Max_$count  =  $max ;
				document.NS_DynamicWidgetElement_Counter_$count = 1;

				jQuery('.NS-DynamicWidgetElement-Add-$count').click(function(e) {
					e.preventDefault();

					if(document.NS_DynamicWidgetElement_Max_$count != 0) {
						if(document.NS_DynamicWidgetElement_Counter_$count >= document.NS_DynamicWidgetElement_Max_$count) {
							return false;
						}
					}

					document.NS_DynamicWidgetElement_Counter_$count++;

					var buffer = jQuery('#NS-DynamicWidgetElement-Element-Template-$count').clone(true).removeAttr('id').val('');
					buffer.find('.NS-DynamicWidgetElement-Remove-Container').css('display', '');
					buffer.hide();

					jQuery('.NS-DynamicWidgetElement-Container-$count').append(buffer);
					buffer.show('medium');

					return false;
				});

				jQuery('.NS-DynamicWidgetElement-Remove-$count').click(function(e) {
					e.preventDefault();

					document.NS_DynamicWidgetElement_Counter_$count--;

					jQuery(this).parent().parent().hide('medium', function() {
						jQuery(this).remove();
					});

					return false;
				});
			});"
		);
	}
}
?>