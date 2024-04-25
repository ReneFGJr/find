<?php

namespace App\Controllers;

helper(['boostrap', 'url', 'sessions', 'cookie', 'sisdoc']);
$session = \Config\Services::session();
define("PATH", 'https://www.ufrgs.br/find/');

class Api extends BaseController
{
    public function index($verb = '', $d2 = '', $d3 = '')
    {
        /* NAO USADO PARA AS APIS */
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: Content-Type");
        #header("Access-Control-Max-Age': '3600'");

        if (get("test") == '') {
            //header('Content-Type: application/json; charset=utf-8');
            header("Content-Type: application/json");
        }

        if ($verb == '') {
            $d1 = get("verb");
        }


        switch ($verb) {
            case 'getLibrary':
                $Library = new \App\Models\Find\Library\Index();
                $RSP = $Library->where('l_code',$d2)->first();
                break;

            case 'getIndex':
                $lib = get("lib");
                $Index = new \App\Models\Find\Indexes\Index();
                $RSP['index'] = $d2;
                $RSP['terms'] = $Index->getIndex($d2,$lib);
                break;

            case 'search':
                $RSP['term'] = get("q");
                $UI = new \App\Models\UI\Search\Index();
                $RSP['items'] = $UI->searchAPI(get("q"),$d2,$d3);
                $RSP['term'] = array_merge($_POST,$_GET);
                break;

            case 'label':
                $Labels = new \App\Models\Find\Labels\Index();
                $RSP = $Labels->print($d2, $d3);
                break;

            case 'getIsbn':
                $Book = new \App\Models\Find\Items\Index();
                if ($d2 != '') {
                    $isbn = $d2;
                } else {
                    $isbn = get("isbn");
                }
                if ($d3 != '') {
                    $lib = $d3;
                } else {
                    $lib = get("lib");
                }
                $RSP = $Book->getISBN($isbn, $lib);
                $RSP['dados']['isbn'] = $isbn;
                $RSP['dados']['lib'] = $lib;
                break;

            case 'vitrine':
                $lib = trim(get("library") . $d2);
                $Items = new \App\Models\Find\Items\Index();
                $RSP['items'] = $Items->vitrine($lib);
                break;

            case 'library':
                $Library = new \App\Models\Find\Library\Index();
                $RSP = $this->version();
                $RSP['library'] = $Library->listAll();
                break;

            default:
                $RSP = $this->version();
                break;
        }

        echo json_encode($RSP);
        exit;
        return "";
    }

    private function version()
    {
        $RSP = [];
        $RSP['system'] = 'FIND 2.0';
        $RSP['version'] = 'v0.24.04.16';
        $RSP['timestamp'] = date("Y-m-d") . 'T' . date("H:i:s");
        $RSP['status'] = '200';
        return $RSP;
    }
}
