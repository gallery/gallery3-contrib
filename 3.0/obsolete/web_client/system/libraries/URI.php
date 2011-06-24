<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * URI library.
 *
 * $Id: URI.php 4679 2009-11-10 01:45:52Z isaiah $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class URI_Core extends Router {

	/**
	 * Returns a singleton instance of URI.
	 *
	 * @return  object
	 */
	public static function instance()
	{
		static $instance;

		if ($instance == NULL)
		{
			// Initialize the URI instance
			$instance = new URI;
		}

		return $instance;
	}

	/**
	 * Retrieve a specific URI segment.
	 *
	 * @param   integer|string  segment number or label
	 * @param   mixed           default value returned if segment does not exist
	 * @return  string
	 */
	public function segment($index = 1, $default = FALSE)
	{
		if (is_string($index))
		{
			if (($key = array_search($index, URI::$segments)) === FALSE)
				return $default;

			$index = $key + 2;
		}

		$index = (int) $index - 1;

		return isset(URI::$segments[$index]) ? URI::$segments[$index] : $default;
	}

	/**
	 * Retrieve a specific routed URI segment.
	 *
	 * @param   integer|string  rsegment number or label
	 * @param   mixed           default value returned if segment does not exist
	 * @return  string
	 */
	public function rsegment($index = 1, $default = FALSE)
	{
		if (is_string($index))
		{
			if (($key = array_search($index, URI::$rsegments)) === FALSE)
				return $default;

			$index = $key + 2;
		}

		$index = (int) $index - 1;

		return isset(URI::$rsegments[$index]) ? URI::$rsegments[$index] : $default;
	}

	/**
	 * Retrieve a specific URI argument.
	 * This is the part of the segments that does not indicate controller or method
	 *
	 * @param   integer|string  argument number or label
	 * @param   mixed           default value returned if segment does not exist
	 * @return  string
	 */
	public function argument($index = 1, $default = FALSE)
	{
		if (is_string($index))
		{
			if (($key = array_search($index, URI::$arguments)) === FALSE)
				return $default;

			$index = $key + 2;
		}

		$index = (int) $index - 1;

		return isset(URI::$arguments[$index]) ? URI::$arguments[$index] : $default;
	}

	/**
	 * Returns an array containing all the URI segments.
	 *
	 * @param   integer  segment offset
	 * @param   boolean  return an associative array
	 * @return  array
	 */
	public function segment_array($offset = 0, $associative = FALSE)
	{
		return $this->build_array(URI::$segments, $offset, $associative);
	}

	/**
	 * Returns an array containing all the re-routed URI segments.
	 *
	 * @param   integer  rsegment offset
	 * @param   boolean  return an associative array
	 * @return  array
	 */
	public function rsegment_array($offset = 0, $associative = FALSE)
	{
		return $this->build_array(URI::$rsegments, $offset, $associative);
	}

	/**
	 * Returns an array containing all the URI arguments.
	 *
	 * @param   integer  segment offset
	 * @param   boolean  return an associative array
	 * @return  array
	 */
	public function argument_array($offset = 0, $associative = FALSE)
	{
		return $this->build_array(URI::$arguments, $offset, $associative);
	}

	/**
	 * Creates a simple or associative array from an array and an offset.
	 * Used as a helper for (r)segment_array and argument_array.
	 *
	 * @param   array    array to rebuild
	 * @param   integer  offset to start from
	 * @param   boolean  create an associative array
	 * @return  array
	 */
	public function build_array($array, $offset = 0, $associative = FALSE)
	{
		// Prevent the keys from being improperly indexed
		array_unshift($array, 0);

		// Slice the array, preserving the keys
		$array = array_slice($array, $offset + 1, count($array) - 1, TRUE);

		if ($associative === FALSE)
			return $array;

		$associative = array();
		$pairs       = array_chunk($array, 2);

		foreach ($pairs as $pair)
		{
			// Add the key/value pair to the associative array
			$associative[$pair[0]] = isset($pair[1]) ? $pair[1] : '';
		}

		return $associative;
	}

	/**
	 * Returns the complete URI as a string.
	 *
	 * @return  string
	 */
	public function string()
	{
		return URI::$current_uri;
	}

	/**
	 * Magic method for converting an object to a string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return URI::$current_uri;
	}

	/**
	 * Returns the total number of URI segments.
	 *
	 * @return  integer
	 */
	public function total_segments()
	{
		return count(URI::$segments);
	}

	/**
	 * Returns the total number of re-routed URI segments.
	 *
	 * @return  integer
	 */
	public function total_rsegments()
	{
		return count(URI::$rsegments);
	}

	/**
	 * Returns the total number of URI arguments.
	 *
	 * @return  integer
	 */
	public function total_arguments()
	{
		return count(URI::$arguments);
	}

	/**
	 * Returns the last URI segment.
	 *
	 * @param   mixed   default value returned if segment does not exist
	 * @return  string
	 */
	public function last_segment($default = FALSE)
	{
		if (($end = $this->total_segments()) < 1)
			return $default;

		return URI::$segments[$end - 1];
	}

	/**
	 * Returns the last re-routed URI segment.
	 *
	 * @param   mixed   default value returned if segment does not exist
	 * @return  string
	 */
	public function last_rsegment($default = FALSE)
	{
		if (($end = $this->total_segments()) < 1)
			return $default;

		return URI::$rsegments[$end - 1];
	}

	/**
	 * Returns the path to the current controller (not including the actual
	 * controller), as a web path.
	 *
	 * @param   boolean  return a full url, or only the path specifically
	 * @return  string
	 */
	public function controller_path($full = TRUE)
	{
		return ($full) ? url::site(URI::$controller_path) : URI::$controller_path;
	}

	/**
	 * Returns the current controller, as a web path.
	 *
	 * @param   boolean  return a full url, or only the controller specifically
	 * @return  string
	 */
	public function controller($full = TRUE)
	{
		return ($full) ? url::site(URI::$controller_path.URI::$controller) : URI::$controller;
	}

	/**
	 * Returns the current method, as a web path.
	 *
	 * @param   boolean  return a full url, or only the method specifically
	 * @return  string
	 */
	public function method($full = TRUE)
	{
		return ($full) ? url::site(URI::$controller_path.URI::$controller.'/'.URI::$method) : URI::$method;
	}

} // End URI Class
