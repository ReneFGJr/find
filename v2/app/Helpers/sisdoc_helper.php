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

function ascii($d)
{    //$d = strtoupper($d);

    $gr = array();
    $gr['-'] = '–';
    $gr['.'] = 'º';
    $gr['A'] = 'Â;À;Á;Ä;Ã;Å';
    $gr['a'] = 'â;ã;à;á;ä';
    $gr['E'] = 'Ê;È;É;Ë';
    $gr['e'] = 'ê;è;é;ë';
    $gr['I'] = 'Î;Í;Ì;Ï';
    $gr['i'] = 'î;í;ì;ï';
    $gr['O'] = 'Ô;Õ;Ò;Ó;Ö';
    $gr['o'] = 'ô;õ;ò;ó;ö';
    $gr['U'] = 'Û;Ù;Ú;Ü';
    $gr['u'] = 'û;ú;ù;ü';
    $gr['c'] = 'ç';
    $gr['C'] = 'Ç';

    foreach ($gr as $lt => $gpa) {
        $grp = explode(';', $gpa);
        for ($r = 0; $r < count($grp); $r++) {
            $d = troca($d, $grp[$r], $lt);
        }
    }
    $name = '';
    for ($r = 0; $r < strlen($d); $r++) {
        $c = substr($d, $r, 1);
        $o = ord($c);
        if ($o <= 127) {
            $name .= $c;
        }
    }
    return $name;
}

/* Funcao troca */
function troca($qutf, $qc, $qt)
{
    if (!is_array($qc)) {
        $qc = array($qc);
    }
    if (!is_array($qt)) {
        $qt = array($qt);
    }
    return (str_replace($qc, $qt, $qutf));
}