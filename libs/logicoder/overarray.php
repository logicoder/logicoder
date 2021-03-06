<?php
/**
 * Logicoder Web Application Framework - OverArray library
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Simply an overloaded array.
 *
 * @package     Logicoder
 * @subpackage  Core
 * @link        http://www.logicoder.com/documentation/overarray.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_OverArray implements ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Internal data stack.
     */
    protected $aData = array();

    /**
     * Constructor.
     *
     * @param   array   $aData      Initial data to import
     */
    public function __construct ( &$aData = null )
    {
        if (!is_null($aData) and (is_array($aData) or is_object($aData)))
        {
            $this->aData = $aData;
        }
    }

    /**
     * Countable interface implementation.
     *
     * @return  integer Total number of values stored
     */
    public function count ( /* void */ )
    {
        return count($this->aData);
    }

    /**
     * Overload magic property setter method.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    protected function __set ( $sKey, $mValue )
    {
        return $this->aData[$sKey] = $mValue;
    }

    /**
     * Overload magic property getter method.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The key value
     */
    protected function __get ( $sKey )
    {
        return (isset($this->aData[$sKey])) ? $this->aData[$sKey] : null;
    }

    /**
     * Overload magic property checker method.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    protected function __isset ( $sKey )
    {
        return isset($this->aData[$sKey]);
    }

    /**
     * Overload magic property unsetter method.
     *
     * @param   string  $sKey       The name/key string
     */
    protected function __unset ( $sKey )
    {
        unset($this->aData[$sKey]);
    }

    /**
     * Implements ArrayAccess element setter.
     *
     * @param   string  $sKey       The name/key string
     * @param   mixed   $mValue     The value
     *
     * @return  mixed   The key value
     */
    public function offsetSet ( $sKey, $mValue )
    {
        return $this->__set($sKey, $mValue);
    }

    /**
     * Implements ArrayAccess element getter.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  mixed   The key value
     */
    public function offsetGet ( $sKey )
    {
        return $this->__get($sKey);
    }

    /**
     * Implements ArrayAccess element unsetter.
     *
     * @param   string  $sKey       The name/key string
     */
    public function offsetUnset ( $sKey )
    {
        $this->__unset($sKey);
    }

    /**
     * Implements ArrayAccess element checker.
     *
     * @param   string  $sKey       The name/key string
     *
     * @return  boolean Whether the key is defined
     */
    public function offsetExists ( $sKey )
    {
        return $this->__isset($sKey);
    }

    /**
     * Implements IteratorAggregate getIterator method.
     *
     * @return  object  This function will return an iterator
     */
    public function getIterator ( /* void */ )
    {
        return new ArrayIterator($this->aData);
    }

    /**
     * Unserialize data from a file.
     *
     * @param   string  $sFile      File name to load data from
     */
    public function load_data ( $sFile )
    {
        $aData = unserialize(file_get_contents($sFile));
        if ($aData === false)
        {
            return false;
        }
        $this->aData = $aData;
        return true;
    }

    /**
     * Serialize data to a file.
     *
     * @param   string  $sFile      File name to save data to
     */
    public function save_data ( $sFile )
    {
        return file_put_contents($sFile, serialize($this->aData));
    }

    /**
     * Clean data before sleeping.
     *
     * @return  array   Returns an array with cleaned data
     */
    function __sleep ( /* void */ )
    {
        foreach ($this->aData as $sKey => $mValue)
        {
            if ($mValue === null)
            {
                unset($this->aData[$sKey]);
            }
        }
        return array('aData');
    }
}
// END LogiCoder_OverArray class
