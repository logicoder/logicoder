<?php
/**
 * Logicoder Web Application Framework - Debug Helpers
 *
 * @package     Logicoder
 * @subpackage  Helpers
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @link        http://www.logicoder.com/documentation/helpers/debug.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
define('DEBUG_HELPER', true);

/**#@+
 * Required dependency.
 */
if (!defined('HTML_HELPER'))
{
    if (defined('LOGICODER'))
    {
        Logicoder::instance()->load->helper('HTML');
    }
    else
    {
        require('html.php');
    }
}
/**#@-*/

// -----------------------------------------------------------------------------

define('DUMP_CSS_STYLES', "
    <style type='text/css'>
        table.dump {
            width:          240px;
            font-family:    Consolas, 'Courier New', Courier, monospace;
            color:          #000;
            font-size:      10px;
            margin:         0 auto 4px auto;
            border:         1px solid #edd400;
        }
        table.dump caption {
            background:     #edd400;
            margin:         0 auto;
            font-weight:    bold;
            text-align:     center;
            padding:        2px 4px 0 4px;
        }
        table.dump span {
            font-style:     italic;
        }
        table.dump td {
            background:     #fce94f;
            border:         1px solid #edd400;
            margin:         0px;
            padding:        2px 4px;
            text-align:     center;
        }
        table.dump td.value {
            background:     #fff;
            text-align:     left;
        }
        table.dump table {
            margin:         0 auto;
        }
        table.dump.array {
            border:         1px solid #73d216;
        }
        table.dump.array caption {
            background:     #73d216;
        }
        table.dump.array td {
            background:     #8ae234;
            border:         1px solid #edd400;
        }
        table.dump.array td.value {
            background:     #fff;
        }
    </style>
");

// -----------------------------------------------------------------------------

/**
 * Returns pretty memory usage.
 */
function memory_usage ( /* void */ )
{
    if (function_exists('memory_get_usage'))
    {
        return round(memory_get_usage()/1024/1024, 2).'mb';
    }
    return 'N/A';
}
// END memory_usage function

// -----------------------------------------------------------------------------

/**
 * Returns variable type.
 *
 * @param   mixed   $mVar   Variable to check (by reference)
 *
 * @return  string  The variable type
 */
function get_type ( &$mVar )
{
    if (is_null($mVar))
    {
        /*
            It's null, work done.
        */
        return 'null';
    }
    elseif (is_scalar($mVar))
    {
        /*
            It's scalar, so may be integer, float, boolean or string.
        */
        if (is_integer($mVar))
        {
            return 'integer';
        }
        elseif (is_float($mVar))
        {
            return 'float';
        }
        elseif (is_bool($mVar))
        {
            return 'boolean';
        }
        else
        {
            return 'string';
        }
        return 'scalar';
    }
    else
    {
        /*
            It's not scalar, so may be array, object or resource.
        */
        if (is_array($mVar))
        {
            return 'array';
        }
        elseif (is_object($mVar))
        {
            return 'object';
        }
        elseif (is_resource($mVar))
        {
            return 'resource';
        }
        return 'not_scalar';
    }
}
// END get_type function

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
function _dump_call ( $sFunction = '_dump_call' )
{
    $aBT = array_reverse(debug_backtrace());
    $aIn = array('include', 'include_once', 'require', 'require_once');
    /*
        Look at last included file for line numbers.
    */
    foreach ($aBT as $aTrace)
    {
        if (!(isset($aTrace['function']) and
            (in_array($aTrace['function'], $aIn) or
             (strcmp($aTrace['function'], $sFunction) !== 0))))
        {
            $aFile = $aTrace;
        }
    }
    /*
        Get the name.
    */
    if (isset($aFile))
    {
        $aLines = file($aFile['file']);
        $sCode  = $aLines[--$aFile['line']];
        /*
            Find function call.
        */
        preg_match('|\b'.$sFunction.'\s*\(\s*([^()]+)\s*|', $sCode, $aMatches);
        return (isset($aMatches[1])) ? str_part($aMatches[1] , ',') : $sCode;
    }
    return '';
}
// END var_name function

// -----------------------------------------------------------------------------

/**
 * @ignore
 */
function _dump_scalar ( &$mVar )
{
    /*
        Manage boolean conversion.
    */
    if (is_bool($mVar))
    {
        $sVal = ($mVar) ? 'true' : 'false';
    }
    elseif (is_string($mVar))
    {
        $sVal = "'" . $mVar . "'";
    }
    else
    {
        $sVal = (string)$mVar;
    }
    return $sVal;
}

// -----------------------------------------------------------------------------

/**
 * Dumps with details.
 *
 * @param   mixed   $mVar       Variable to dump (by reference)
 * @param   boolean $bReturn    Whether to return or output
 *
 * @return  string  If $bReturn == true
 */
function dump ( &$mVar, $bReturn = false )
{
    $sCaption = _dump_call('dump') . ' <span>[' . get_type($mVar) . ']</span>';
    if (is_scalar($mVar))
    {
        $sVal = _dump_scalar($mVar);
        if ($bReturn)
        {
            return table(null, null, array(array($sVal)), null, null, 'dump');
        }
        echo table($sCaption, null, array(array($sVal)), null, null, 'dump');
    }
    else
    {
        $aData = array();
        foreach ($mVar as $name => $value)
        {
            if (is_scalar($value))
            {
                $sVal = _dump_scalar($value);
            }
            elseif ($mVar === $value)
            {
                $sVal = '* RECURSION *'; // PHP THROWS "Fatal error: Nesting level too deep - recursive dependency?"
            }
            else
            {
                $sVal = dump($value, true);
            }
            $aData[] = array($name . ' <span>[' . get_type($value) . ']</span>',
                             'value' => $sVal);
        }
        if ($bReturn)
        {
            return table(null, null, $aData, null, null, 'dump ' . get_type($mVar));
        }
        echo table($sCaption, null, $aData, null, null, 'dump ' . get_type($mVar));
    }
}
// END dump function

// -----------------------------------------------------------------------------

echo DUMP_CSS_STYLES;

$test = 0;
dump($test, false);
dump($test = true);
dump($test = 3.14);

$test = 'YEP!';
dump($test);

$variable = array(
    true,
    "first"=> 1,
    "second",
    "third"=>array(
        "inner third 1",
        "inner third 2"=>"yeah",
        array('pippo')),
    "fourth");
#$variable[] = $variable;
dump($variable);
dump($_REQUEST);
dump($_REQUEST['logicoder']);

class Vegetable {

   public $edible;
   protected $color;
   private $test = array(1, 2, 3, array('0'));
   var $t;

   function Vegetable($edible, $color="green") {
       $this->edible = $edible;
       $this->color = $color;
       $this->t = $this;
   }

   function is_edible() {
       return $this->edible;
   }

   function what_color() {
       return $this->color;
   }

}
$variable=new Vegetable("spinach");
dump($variable);
dump(object_to_array($variable));
print_r($variable);
