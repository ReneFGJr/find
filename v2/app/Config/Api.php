<?php

namespace App\Controllers;

helper(['boostrap', 'url', 'sessions', 'cookie','sisdoc']);
$session = \Config\Services::session();

class Api extends BaseController
{
    public function index($verb='',$d2='',$d3='')
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

        if ($verb=='') { $d1 = get("verb"); }


        switch($verb)
            {
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
            $RSP['timestamp'] = date("Y-m-d").'T'.date("H:i:s");
            $RSP['status'] = '200';
            return $RSP;
        }
}
