<?php
/**
 * Logicoder Web Application Framework - Models library components
 *
 * @package     Logicoder
 * @copyright   Copyright (c) 1999-2007, Marco Del Tongo
 * @license     http://logicoder.com/documentation/license.html
 * @version     $Id$
 */

// -----------------------------------------------------------------------------

/**
 * Password Field
 *
 * Password string.
 * HTML input: password
 *
 * @package     Logicoder
 * @subpackage  Model
 * @link        http://www.logicoder.com/documentation/models.html
 * @author      Marco Del Tongo <info@marcodeltongo.com>
 */
class Logicoder_Model_Field_Password extends Logicoder_Model_Field_Char
{
    /**
     * Override default constructor.
     */
    public function __construct ( $oModel, $sField, array $aOptions = array() )
    {
        /*
            Override default values.
        */
        $aOptions['index'] = true;
        $aOptions['maxlength'] = 16;
        /*
            Call parent constructor.
        */
        parent::__construct($oModel, $sField, $aOptions);
    }
}
// END Logicoder_Model_Field_Password class