<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Inflector helper class.
 *
 * $Id: inflector.php 4679 2009-11-10 01:45:52Z isaiah $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class inflector_Core {

	// Cached inflections
	protected static $cache = array();

	// Uncountable and irregular words
	protected static $uncountable;
	protected static $irregular;

	/**
	 * Checks if a word is defined as uncountable.
	 *
	 * @param   string   word to check
	 * @return  boolean
	 */
	public static function uncountable($str)
	{
		if (inflector::$uncountable === NULL)
		{
			// Cache uncountables
			inflector::$uncountable = Kohana::config('inflector.uncountable');

			// Make uncountables mirroed
			inflector::$uncountable = array_combine(inflector::$uncountable, inflector::$uncountable);
		}

		return isset(inflector::$uncountable[strtolower($str)]);
	}

	/**
	 * Makes a plural word singular.
	 *
	 * @param   string   word to singularize
	 * @param   integer  number of things
	 * @return  string
	 */
	public static function singular($str, $count = NULL) {
		$parts = explode('_', $str);

		$last = inflector::_singular(array_pop($parts), $count);

		$pre = implode('_', $parts);
		if (strlen($pre))
			$pre .= '_';

		return $pre.$last;
	}


	/**
	 * Makes a plural word singular.
	 *
	 * @param   string   word to singularize
	 * @param   integer  number of things
	 * @return  string
	 */
	public static function _singular($str, $count = NULL)
	{
		// Remove garbage
		$str = strtolower(trim($str));

		if (is_string($count))
		{
			// Convert to integer when using a digit string
			$count = (int) $count;
		}

		// Do nothing with a single count
		if ($count === 0 OR $count > 1)
			return $str;

		// Cache key name
		$key = 'singular_'.$str.$count;

		if (isset(inflector::$cache[$key]))
			return inflector::$cache[$key];

		if (inflector::uncountable($str))
			return inflector::$cache[$key] = $str;

		if (empty(inflector::$irregular))
		{
			// Cache irregular words
			inflector::$irregular = Kohana::config('inflector.irregular');
		}

		if ($irregular = array_search($str, inflector::$irregular))
		{
			$str = $irregular;
		}
		elseif (preg_match('/[sxz]es$/', $str) OR preg_match('/[^aeioudgkprt]hes$/', $str))
		{
			// Remove "es"
			$str = substr($str, 0, -2);
		}
		elseif (preg_match('/[^aeiou]ies$/', $str))
		{
			$str = substr($str, 0, -3).'y';
		}
		elseif (substr($str, -1) === 's' AND substr($str, -2) !== 'ss')
		{
			$str = substr($str, 0, -1);
		}

		return inflector::$cache[$key] = $str;
	}

	/**
	 * Makes a singular word plural.
	 *
	 * @param   string  word to pluralize
	 * @return  string
	 */
	public static function plural($str, $count = NULL)
	{
		if ( ! $str)
			return $str;

		$parts = explode('_', $str);

		$last = inflector::_plural(array_pop($parts), $count);

		$pre = implode('_', $parts);
		if (strlen($pre))
			$pre .= '_';

		return $pre.$last;
	}


	/**
	 * Makes a singular word plural.
	 *
	 * @param   string  word to pluralize
	 * @return  string
	 */
	public static function _plural($str, $count = NULL)
	{
		// Remove garbage
		$str = strtolower(trim($str));

		if (is_string($count))
		{
			// Convert to integer when using a digit string
			$count = (int) $count;
		}

		// Do nothing with singular
		if ($count === 1)
			return $str;

		// Cache key name
		$key = 'plural_'.$str.$count;

		if (isset(inflector::$cache[$key]))
			return inflector::$cache[$key];

		if (inflector::uncountable($str))
			return inflector::$cache[$key] = $str;

		if (empty(inflector::$irregular))
		{
			// Cache irregular words
			inflector::$irregular = Kohana::config('inflector.irregular');
		}

		if (isset(inflector::$irregular[$str]))
		{
			$str = inflector::$irregular[$str];
		}
		elseif (preg_match('/[sxz]$/', $str) OR preg_match('/[^aeioudgkprt]h$/', $str))
		{
			$str .= 'es';
		}
		elseif (preg_match('/[^aeiou]y$/', $str))
		{
			// Change "y" to "ies"
			$str = substr_replace($str, 'ies', -1);
		}
		else
		{
			$str .= 's';
		}

		// Set the cache and return
		return inflector::$cache[$key] = $str;
	}

	/**
	 * Makes a word possessive.
	 *
	 * @param   string  word to to make possessive
	 * @return  string
	 */
	public static function possessive($string)
	{
		$length = strlen($string);

		if (substr($string, $length - 1, $length) == 's')
		{
			return $string.'\'';
		}

		return $string.'\'s';
	}

	/**
	 * Makes a phrase camel case.
	 *
	 * @param   string  phrase to camelize
	 * @return  string
	 */
	public static function camelize($str)
	{
		$str = 'x'.strtolower(trim($str));
		$str = ucwords(preg_replace('/[\s_]+/', ' ', $str));

		return substr(str_replace(' ', '', $str), 1);
	}

	/**
	 * Makes a phrase underscored instead of spaced.
	 *
	 * @param   string  phrase to underscore
	 * @return  string
	 */
	public static function underscore($str)
	{
		return trim(preg_replace('/[\s_]+/', '_', $str), '_');
	}

	/**
	 * Makes an underscored or dashed phrase human-reable.
	 *
	 * @param   string  phrase to make human-reable
	 * @return  string
	 */
	public static function humanize($str)
	{
		return trim(preg_replace('/[_-\s]+/', ' ', $str));
	}

} // End inflector