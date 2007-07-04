<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/exceptions/SwatException.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Maps class names to overridden class names
 *
 * This is a static class and should not be instantiated.
 *
 * This class also attempts to require class-definition files for mapped
 * class names. You can modify the behaviour of automatic class-definition
 * file requiring using the {@link SwatDBClassMap::addPath()} and
 * {@link SwatDBClassMap::removePath()} methods.
 *
 * @package   SwatDB
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBClassMap extends SwatObject
{
	// {{{ private properties

	/**
	 * An associative array of class-mappings
	 *
	 * The array has keys of the 'from class name' and values of the
	 * 'to class name'.
	 *
	 * @var array
	 */
	private static $map = array();

	/**
	 * Paths to search for class-definition files
	 *
	 * @var array
	 */
	private static $search_paths = array('.');

	// }}}
	// {{{ public static function add()

	/**
	 * Maps a class name to another class name
	 *
	 * Subsequent calls to {@link SwatDBClassMap::get()} using the
	 * <i>$from_class_name</i> will return the <i>$to_class_name</i>. Class
	 * names may be mapped to already mapped class names. For example:
	 *
	 * <code>
	 * SwatDBClassMap::add('Object', 'MyObject');
	 * SwatDBClassMap::add('MyObject', 'MyOtherObject');
	 * echo SwatDBClassMap::resolve('Object'); // MyOtherObject
	 * </code>
	 *
	 * Because it is only possible to map from a parent class to a subclass,
	 * it is impossible to create a dependency loop. If the
	 * <i>$from_class_name</i> is already mapped to another class the old
	 * mapping is overwritten.
	 *
	 * @param string $from_class_name the class name to map from.
	 * @param string $to_class_name the class name to map to. The mapped class
	 *                               must be a subclass of the
	 *                               <i>$from_class_name</i>.
	 *
	 * @throws SwatInvalidClassException if the <i>$to_class_name</i>
	 *                                    paramater is not a subclass of the
	 *                                    <i>$from_class_name</i>.
	 */
	public static function add($from_class_name, $to_class_name)
	{
		if (!is_subclass_of($to_class_name, $from_class_name))
			throw new SwatInvalidClassException(
				'$to_class_name must be a subclass of $from_class_name');

		self::$map[$from_class_name] = $to_class_name;
	}

	// }}}
	// {{{ public static function get()

	/**
	 * Resolves a class name from the class map
	 *
	 * @param string $class_name the name of the class to resolve.
	 *
	 * @return string the resolved class name. If no class mapping exists for
	 *                 for the given class name, the same class name is
	 *                 returned.
	 */
	public static function get($class_name)
	{
		$include_paths = explode(':', get_include_path());

		while (array_key_exists($class_name, self::$map)) {
			$class_name = self::$map[$class_name];

			if (!class_exists($class_name) && count(self::$search_paths) > 0) {
				foreach ($include_paths as $include_path) {
					foreach (self::$search_paths as $search_path) {
						$filename = sprintf('%s/%s/%s.php',
							$include_path, $search_path, $class_name);

						if (file_exists($filename)) {
							require_once $filename;
							break 2;
						}
					}
				}
			}
		}

		return $class_name;
	}

	// }}}
	// {{{ public static function addPath()

	/**
	 * Adds a search path for class-definition files
	 *
	 * When an undefined class is resolved, the class map attempts to find
	 * and require a class-definition file for the class.
	 *
	 * All search paths are relative to the PHP include path. The empty search
	 * path ('.') is included by default.
	 *
	 * @param string $search_path the path to search for class-definition files.
	 *
	 * @see SwatDBClassMap::removePath()
	 */
	public static function addPath($search_path)
	{
		if (!in_array($search_path, self::$search_paths, true)) {
			// add path to front of array since it is more likely we will find
			// class-definitions in manually added search paths
			array_unshift(self::$search_paths, $search_path);
		}
	}

	// }}}
	// {{{ public static function removePath()

	/**
	 * Removes a search path for class-definition files
	 *
	 * @param string $search_path the path to remove.
	 *
	 * @see SwatDBClassMap::addPath()
	 */
	public static function removePath($search_path)
	{
		$index = array_search($search_path, self::$search_paths);
		if ($index !== false)
			array_splice(self::$search_paths, $index, 1);
	}

	// }}}
	// {{{ private function __construct()

	/**
	 * The class map is a static object and should not be instantiated
	 */
	private function __construct()
	{
	}

	// }}}

	// deprecated API
	// {{{ private properties

	/**
	 * Singleton instance
	 *
	 * @var SwatDBClassMap
	 *
	 * @deprecated Use static methods instead of instantiating this class.
	 */
	private static $instance = null;

	/**
	 * An associative array of class-mappings
	 *
	 * The array is of the form 'PackageClassName' => 'SiteSpecificOverrideClassName'.
	 *
	 * @var array
	 *
	 * @deprecated Use static methods instead of instantiating this class.
	 */
	private $mappings = array();

	/**
	 * The path to search for site-specific class files
	 *
	 * @var string
	 *
	 * @deprecated Use static methods instead of instantiating this class.
	 */
	private $path = null;

	// }}}
	// {{{ public static function instance()

	/**
	 * Gets the singleton instance of the class-mapping object
	 *
	 * @return SwatDBClassMap the singleton instance of the class-
	 *                                  mapping object.
	 *
	 * @deprecated Use static methods instead of instantiating this class.
	 */
	public static function instance()
	{
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	// }}}
	// {{{ public function addMapping()

	/**
	 * Adds a class-mapping to the class-mapping object
	 *
	 * @param string $store_class_name the name of the package 
	 *                                  class to override.
	 * @param string $class_nam the name of the site-specific class.
	 *
	 * @deprecated Use the static method {@link SwatDBClassMap::add()}.
	 */
	public function addMapping($package_class_name, $class_name)
	{
		$this->mappings[$package_class_name] = $class_name;
		SwatDBClassMap::add($package_class_name, $class_name);
	}

	// }}}
	// {{{ public function resolveClass()

	/**
	 * Gets the appropriate class name for a given package class name
	 *
	 * @param string $name the name of the package class to get the
	 *                      mapped name of.
	 *
	 * @return string the appropriate class name for site-specific code. If
	 *                 the site-specific code has overridden a package
	 *                 class, the site-specific overridden value is
	 *                 returned. Otherwise, the package default class
	 *                 name is returned.
	 *
	 * @deprecated Use the static method {@link SwatDBClassMap::get()}.
	 */
	public function resolveClass($name)
	{
		$class_name = $name;

		if (array_key_exists($name, $this->mappings)) {
			$class_name = $this->mappings[$name];

			if (!class_exists($class_name) && $this->path !== null) {
				$class_file = sprintf('%s/%s.php', $this->path, $class_name);
				require_once $class_file;
			}
		}

		return $class_name;
	}

	// }}}
	// {{{ public function setPath()

	/**
	 * Sets the path to search for site-specific class files
	 *
	 * @param string $path the path to search for site-specific class files.
	 *
	 * @deprecated Use the static methods {@link SwatDBClassMap::addPath()} and
	 *             {@link SwatDBClassMap::removePath()}.
	 */
	public function setPath($path)
	{
		$this->path = $path;
		SwatDBClassMap::addPath($path);
	}

	// }}}
}

?>
