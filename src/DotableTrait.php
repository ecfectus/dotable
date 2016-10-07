<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 07/10/16
 * Time: 14:41
 */

namespace Ecfectus\Dotable;

use ArrayIterator;
use CachingIterator;
use JsonSerializable;


trait DotableTrait
{
    /**
     * @var string
     */
    public static $SEPARATOR = '.';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $this->items = $values;
    }

    /**
     * Get a value from the array using dot notation.
     *
     * @param string $path
     * @param null $default
     * @return mixed
     */
    public function get(string $path = '', $default = null)
    {
        $array = $this->items;
        if (!empty($path)) {
            $keys = $this->explode($path);
            foreach ($keys as $key) {
                if (isset($array[$key])) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
        }
        return $array;
    }

    /**
     * Set a value in the array using dot notation.
     *
     * @param string $path
     * @param $value
     * @return DotableInterface
     */
    public function set(string $path = '', $value = null, $unset = false) : DotableInterface
    {
        if (!empty($path)) {
            $at = & $this->items;
            $keys = $this->explode($path);
            while (count($keys) > 0) {
                if (count($keys) === 1) {
                    if (is_array($at)) {
                        if($value === null && $unset === true){
                            unset($at[array_shift($keys)]);
                        }else{
                            $at[array_shift($keys)] = $value;
                        }
                    } else {
                        throw new \RuntimeException("Can not set value at this path ($path) because is not array.");
                    }
                } else {
                    $key = array_shift($keys);
                    if (!isset($at[$key])) {
                        $at[$key] = [];
                    }
                    $at = & $at[$key];
                }
            }
        } else {
            $this->items = $value;
        }
        return $this;
    }

    /**
     * Check the existance of a key in the array using dot notation.
     *
     * @param string $path
     * @return bool
     */
    public function has(string $path = '') : bool
    {
        $keys = $this->explode($path);
        $array = $this->items;
        foreach ($keys as $key) {
            if (isset($array[$key])) {
                $array = $array[$key];
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * Unset/forget a value from the array using dot notation.
     *
     * @param string $path
     * @return DotableInterface
     */
    public function forget(string $path = '') : DotableInterface
    {
        return $this->set($path, null, true);
    }

    /**
     * Prepend a value onto an array value in the array using dot notation.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return DotableInterface
     */
    public function prepend(string $path = '', $value) : DotableInterface
    {
        $array = $this->get($path);
        array_unshift($array, $value);
        $this->set($path, $array);
        return $this;
    }
    /**
     * Push a value onto an array in the array using dot notation.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return DotableInterface
     */
    public function append(string $path = '', $value) : DotableInterface
    {
        $array = $this->get($path);
        $array[] = $value;
        $this->set($path, $array);
        return $this;
    }

    /**
     * Merge one array into the main array using dot notation. Optionally use array_merge_recursive instead of built in merge stratergy.
     *
     * @param string $path
     * @param array $values
     * @param bool $distinct
     * @return DotableInterface
     */
    public function merge(string $path = '', array $values = [], $distinct = true) : DotableInterface
    {
        $get = (array)$this->get($path);
        $this->set($path, ($distinct) ? $this->arrayMergeRecursiveDistinct($get, $values) : array_merge_recursive($get, $values));
        return $this;
    }

    /**
     * Return the whole array
     *
     * @return array
     */
    public function toArray() : array
    {
        return array_map(function ($value) {
            return (is_object($value) && method_exists($value, 'toArray')) ? $value->toArray() : $value;
        }, $this->items);
    }

    /**
     * Explode the given path into an array of path parts using the given seperator.
     *
     * @param $path
     * @return array
     */
    protected function explode($path) : array
    {
        return explode(self::$SEPARATOR, $path);
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * If key is integer, it will be merged like array_merge do:
     * arrayMergeRecursiveDistinct(array(0 => 'org value'), array(0 => 'new value'));
     *     => array(0 => 'org value', 1 => 'new value');
     *
     * @param array $array1
     * @param array $array2
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    protected function arrayMergeRecursiveDistinct(array &$array1, array &$array2) : array
    {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged[$key]) && is_array($merged[$key])) {
                if (is_int($key)) {
                    $merged[] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                } else {
                    $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                }
            } else {
                if (is_int($key)) {
                    $merged[] = $value;
                } else {
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * Determine if the given array value exists to satisfy the ArrayAccess Interface.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key) : bool
    {
        return $this->has($key);
    }

    /**
     * Get a dot notation value from the array to satisfy the ArrayAccess Interface.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a value into the array using dot notation to satisfy the ArrayAccess Interface.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a value from the array using do notation to satisfy the ArrayAccess Interface.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->forget($key);
    }

    /**
     * Return a representation of the array suitable for json encoding to satisfy the JsonSerializable Interface.
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif (is_object($value) && method_exists($value, 'toArray')) {
                return $value->toArray();
            } else {
                return $value;
            }
        }, $this->items);
    }

    /**
     * Returns an iterator for use in foreach loop to satisfy the IteratorAggregate Interface.
     *
     * @return \ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * Get a CachingIterator instance.
     *
     * @param  int  $flags
     * @return \CachingIterator
     */
    public function getCachingIterator($flags = CachingIterator::CALL_TOSTRING) : CachingIterator
    {
        return new CachingIterator($this->getIterator(), $flags);
    }

    /**
     * returns a count of the items to satisfy the Countable Interface
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->items);
    }

    /**
     * Convert the collection to its string representation.
     *
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this->jsonSerialize());
    }
}