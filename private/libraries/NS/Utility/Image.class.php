<?php
/*
	Copyright (C) 2008 - 2013 Inanta Martsanto
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

namespace NS\Utility;

use NS\Object;
use NS\Core\Config;
use NS\Exception\Exception;
use NS\Exception\IOException;

/**
 *Image file processing
 *
 *@author Inanta Martsanto <inanta@inationsoft.com>
 *@property int $Width Image width
 *@property int $Height Image height
 */
class Image extends Object {
	const ORIENTATION_PORTRAIT = 1;
	const ORIENTATION_LANDSCAPE = 2;

	const TYPE_JPEG = 1;
	const TYPE_GIF = 2;
	const TYPE_PNG = 3;

	const CROP_Y_TOP = 1;
	const CROP_Y_MIDDLE = 2;
	const CROP_Y_BOTTOM = 3;

	const CROP_X_LEFT = 1;
	const CROP_X_CENTER = 2;
	const CROP_X_RIGHT = 3;

	const COLOR_MODE_GRAYSCALE = 1;
	
	private $_image, $_imageType, $_orientation, $_cf;
 
	function __construct($filename) {
		if(!is_readable($filename)) {
			if(!is_file($filename)) throw new IOException(array('code' => IOException::FILE_NOT_FOUND, 'filename' => $filename));

			throw new IOException(array('code' => IOException::FILE_NOT_READABLE, 'filename' => $filename));
		}

		$image_info = getimagesize($filename);
		if(!in_array($image_info['mime'], array('image/jpeg', 'image/jpg', 'image/gif', 'image/png'))) throw new Exception('Error file type');

		$this->_imageType = $image_info[2];

		if($this->_imageType == IMAGETYPE_JPEG) $this->_image = imagecreatefromjpeg($filename);
		elseif($this->_imageType == IMAGETYPE_GIF) $this->_image = imagecreatefromgif($filename);
		elseif( $this->_imageType == IMAGETYPE_PNG ) $this->_image = imagecreatefrompng($filename);

		$this->createProperties(array(
			'Width' => imagesx($this->_image),
			'Height' => imagesy($this->_image)
		));

		$this->_orientation = ($this->Width > $this->Height ? self::ORIENTATION_LANDSCAPE : self::ORIENTATION_PORTRAIT);
	}

	/**
	 *Save image to new file
	 *@param string $filename Path and filename where image will be saved
	 *@param int $image_type Image type
	 *@param int $compression Image compression from 0 to 100 (only for JPEG image type)
	 *@param int $permissions File permission given when saving image file
	 **/
	function save($filename, $image_type = null, $compression = 100, $permissions = null) {
		$return = false;
		$folder = str_replace('/' . end(explode('/', $filename)), '', $filename);

		if(!is_writeable($folder)) throw new IOException(array('code' => IOException::DIRECTORY_NOT_WRITEABLE, 'directory' => $folder));

		if($image_type == null) {
			$image_type = $this->_imageType;
		}

		if($image_type == IMAGETYPE_JPEG) $return = imagejpeg($this->_image, $filename, $compression);
		elseif($image_type == IMAGETYPE_GIF) $return = imagegif($this->_image, $filename);
		elseif($image_type == IMAGETYPE_PNG) $return = imagepng($this->_image, $filename);

		if($permissions != null) chmod($filename, $permissions);

		return $return;
	}

	function output($image_type = IMAGETYPE_JPEG) {
		if($image_type == IMAGETYPE_JPEG ) imagejpeg($this->_image);
		elseif( $image_type == IMAGETYPE_GIF ) imagegif($this->_image);
		elseif( $image_type == IMAGETYPE_PNG ) imagepng($this->_image);
	}
   
	function resizeToHeight($height) {
		$ratio = $height / $this->Height;
		$width = $this->Width * $ratio;
		$this->resize($width, $height);
	}
 
	function resizeToWidth($width) {
		$ratio = $width / $this->Width;
		$height = $this->Height * $ratio;
		$this->resize($width, $height);
	}
 
	function scale($scale) {
		$width = $this->Width * $scale / 100;
		$height = $this->Height * $scale / 100;
		$this->resize($width, $height);
	}
 
	/**
	 *Resize image to spesific width and height
	 *@param int $width New image width
	 *@param int $height New image height
	 **/
	function resize($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->_image, 0, 0, 0, 0, $width, $height, $this->Width, $this->Height);
		$this->_image = $new_image;

		$this->Width = $width;
		$this->Height = $height;
	}

	function resizeAndCrop($width, $height, $x = null, $y = null) {
		$x = ($x == null ? self::CROP_X_CENTER : $x);
		$y = ($y == null ? self::CROP_Y_MIDDLE : $y);

		$container_width = $width;
		$container_height = $height;

		if($this->_orientation == self::ORIENTATION_PORTRAIT) {
			if($width > $height) {
				$ratio = $width / $this->Width;
				$height = $this->Height * $ratio;
				$x = 0;
				
				echo $container_height, ', ', $height, ', ', $height - $container_height, ', ' , ($height / 2) * 2;
				
				if($y == self::CROP_Y_TOP) $y = 0;
				else if ($y == self::CROP_Y_MIDDLE) $y = ($this->Height - $height) / 2;
				else if ($y == self::CROP_Y_BOTTOM) $y = $height;
			}
		}

		$new_image = imagecreatetruecolor($container_width, $container_height);
		imagecopyresampled($new_image, $this->_image, 0, 0, $x, $y, $width, $height, $this->Width, $this->Height);
		$this->_image = $new_image;
	}

	function addText($text, $x = '50%', $y = '50%', $color = '#000000', $font_size = 20, $font_type = null) {
		if($this->_cf == null) {
			$this->_cf = Config::getInstance();
		}

		if($font_type == null) {
			$font_type = NS_SYSTEM_PATH . '/' . $this->_cf->AssetFolder . '/font/Domine-Regular.ttf';
		}

		$font_color = imagecolorallocate($this->_image, hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));

		$dimensions = imagettfbbox($font_size, 0, $font_type, $text);
		$width_x = $dimensions[2];
		$height_y = $dimensions[5];

		$dimensions = imagettfbbox($font_size, 0, $font_type, 'W');
		$font_width = $dimensions[2];

		if(strpos($x, '%') !== false) {
			$x = floor(min(max($this->Width * (str_replace('%', '', $x) / 100) - 0.5 * $width_x, $font_width), $this->Width - $width_x - 0.5 * $font_width));
		}
		
		if(strpos($y, '%') !== false) {
			$y = floor(min(max($this->Height * (str_replace('%', '', $y) / 100) - 0.5 * $height_y, $height_y), $this->Height - $height_y * 1.5));
		}

		// Old implementation: imagestring($this->_image, $font_size, $text_x, $text_y, $text, $font_color);
		imagettftext($this->_image, $font_size, 0, $x, $y, $font_color, $font_type, $text);
	}

	function grayscale() {
		imagefilter($this->_image, IMG_FILTER_GRAYSCALE);
	}
}
?>