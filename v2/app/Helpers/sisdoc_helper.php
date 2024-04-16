<?php

/**
 * CodeIgniter Form Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Forms SisDoc
 * @category    Helpers
 * @author      Rene F. Gabriel Junior <renefgj@gmail.com>
 * @link        http://www.sisdoc.com.br/CodIgniter
 * @version     v0.24.04.16
 */

function get($var)
    {
        $RSP = '';
        if (isset($_POST[$var]))
            {
                $RSP = $_POST[$var];
            }
        if (isset($_GET[$var])) {
            $RSP = $_GET[$var];
        }
        return $RSP;
    }

function pre($dt, $force = true)
{
    echo '<pre>';
    print_r($dt);
    echo '</pre>';
    if ($force) {
        exit;
    }
}