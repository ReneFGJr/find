<?php

namespace App\Models\Catalog;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function index($d1='',$d2='',$d3='') {
        $RSP = [];
        $RSP['status'] = '200';
        $RSP['msg'] = 'OK';

        switch($d1) {
            case 'status':
                $Index = new \App\Models\Find\Indexes\Index();
                $RSP = $Index->getStatus();
                break;
            case 'getIndex':
                $lib = $d2;
                $Index = new \App\Models\Find\Indexes\Index();
                $RSP['terms'] = $Index->getIndex($d2,$d3);
                break;

            case 'itemAdd':
                $RSP['status'] = '200';
                $RSP['msg'] = 'Item adicionado com sucesso';
                $RSP['data'] = $_POST;

                $Item = new \App\Models\Find\Items\Index();
                $ISBN = get('isbn');
                $Library = get('library');
                $RSP = $Item->addItem($ISBN,$Library);
                break;

            case 'itemSearch':
                $q = get("searchQuery");
                $UI = new \App\Models\UI\Search\Index();
                $qn = sonumero($q);
                if ($qn == $q) {
                    $ITEM = $UI->searchISBN($q,$d3);
                    $RSP['data'][] = $ITEM;
                } else {
                    $ITEM = $UI->searchTitle($q,$d3);
                    $RSP['data'] = $ITEM;
                }
                break;
            case 'getIsbn':
                $Book = new \App\Models\Find\Items\Index();
                /************* ISBN */
                if ($d2 != '') {
                    $isbn = $d2;
                } else {
                    $isbn = get("isbn");
                }
                /************* Biblioteca */
                if ($d3 != '') {
                    $lib = $d3;
                } else {
                    $lib = get("lib");
                }
                $RSP = $Book->getISBN($isbn, $lib);
                $RSP['dados']['isbn'] = $isbn;
                $RSP['dados']['lib'] = $lib;
                break;

            default:
                $RSP['status'] = '400';
                $RSP['msg'] = 'Ação inválida';
                $RSP['post'] = $_POST;
                $RSP['get'] = $_GET;
                $RSP['verb'] = $d1;
                break;
        }

        return $RSP;
    }

}
