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
use NS\Exception\IllegalArgumentException;

/**
 *Create pager / paging user interface
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 */
class Paginator extends UI {
	function __construct($id, $total, $offset = 1, $limit = 10, $uri_base = '', $max_offset = 10, $show_next_prev = true) {
		if(!is_numeric($total)) throw new IllegalArgumentException($total);
		if(!is_numeric($offset)) throw new IllegalArgumentException($offset);
		if(!is_numeric($limit)) throw new IllegalArgumentException($limit);
		if(!is_numeric($max_offset)) throw new IllegalArgumentException($max_offset);
		if($limit == 0) { parent::__construct('<div' . ($id != null ? ' id="' . $id . '" name="' . $id . '" ' : ' ') . 'class="NS-Paginator NS-Paginator-Empty"></div>'); return; };

		$end_offset = ceil($total / $limit);
		if($end_offset == 1 || $offset > $end_offset || $offset < 1) { parent::__construct('<div' . ($id != null ? ' id="' . $id . '" name="' . $id . '" ' : ' ') . 'class="NS-Paginator NS-Paginator-Empty"></div>'); return; }

		if($end_offset <= $max_offset) {
			$l = 1;
			$r = $end_offset;
		} else {
			$l = round($max_offset / 2);
			$r = $max_offset - $l;    
		}

		if(($offset - $l) <= 1) {
			$r += ($l - 1);
			$l = 1;
		} else if(($offset + $r) >= $end_offset) {
			$l = $end_offset - ($l + $r) + 2;
			$r = $end_offset;
		} else {
			$l = $offset - $l;
			$r = $offset + $r;

			while(($r) - $l > $max_offset - 3) if($offset - $l <= $r - $offset) --$r; else ++$l;
		}

		$widget = '<div' . ($id != null ? ' id="' . $id . '" name="' . $id . '" ' : ' ') . 'class="NS-Paginator">';
		if($show_next_prev && $offset != 1) $widget .= '<span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Prev"><a href="' . $uri_base . ($offset - 1) . '">&#171;</a></span>&nbsp;';

		if($l != 1) $widget .= '<a href="' . $uri_base . 1 . '"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Offset-1">' . 1 . '</span></a>&nbsp;<span class="NS-Paginator NS-Paginator-Skip">...</span>&nbsp;';

		for($l; $l < $offset; ++$l) {
			$widget .= '<a href="' . $uri_base . $l . '"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Offset-' . $l . '">' . $l . '</span></a>' . '&nbsp;';
		}

		$widget .= '<a href="#" class="NS-Paginator-Hyperlink-Selected"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Offset-' . $l . ' NS-Paginator NS-Paginator-Selected">' . $l . '</span></a>' . '&nbsp;';
		++$l;

		for($l; $l <= $r; ++$l) {
			$widget .= '<a href="' . $uri_base . $l . '"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Offset-' . $l . '">' . $l . '</span></a>' . '&nbsp;';
		}

		if($r < $end_offset) $widget .= '<span class="NS-Paginator NS-Paginator-Skip">...</span>&nbsp;<a href="' . $uri_base . $end_offset . '"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Offset-' . $end_offset . '">' . $end_offset . '</span></a>';
		if($show_next_prev && $offset != $end_offset) $widget .= '&nbsp;<a href="' . $uri_base . ($offset + 1) . '"><span class="NS-Paginator NS-Paginator-Offset NS-Paginator-Next">&#187;</span></a>';
		$widget .= '</div>';

		parent::__construct($widget);
	}
}
?>
