<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Allowed non-php view types. Most file extensions are supported.
 * Do not forget to add a valid MIME type in mimes.php
 */
$config['allowed_filetypes'] = array
(
	'gif',
	'jpg', 'jpeg',
	'png',
	'tif', 'tiff',
	'swf',
	'htm', 'html',
	'css',
	'js'
);
