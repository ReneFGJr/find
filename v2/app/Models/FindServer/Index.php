<?php

namespace App\Models\FindServer;

use CodeIgniter\Model;

class Index extends Model
{
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'i_concept',
        'i_library',
        'i_manitestation',
        'i_titulo',
        'i_status',
        'i_search'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

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

    function register($dt)
        {
            $RDFconcept = new \App\Models\FindServer\RDFconcept();
            $RDFdata = new \App\Models\FindServer\RDFdata();
            $class = $dt['concept']['Class'];
            $class = 'Work';

            $name = $dt['concept']['pref_term'];
            $idC = $RDFconcept->createConcept($class, $name, $lang = 'pt_BR');
            $title = '[Não definido]';
            $authors = '';
            $isbn = sonumero($dt['concept']['pref_term']);

            foreach($dt['data'] as $item)
                {
                    $prop = $item['property'];
                    if ($prop == 'hasTitle')
                        {
                            $title = $item['literal'];
                        }

                    if ($prop == 'hasAuthor') {
                        $authors .= $item['literal'].' ';
                    }

                    $ID = $item['ID'];
                    $Class = $item['Class'];
                    $literal = $item['literal'];
                    $lang = $item['lang'];

                    if ($ID == 0)
                        {
                            $RDFdata->register($idC, $prop, 0, $literal);
                        } else {
                            $IDD = $RDFconcept->createConcept($Class, $literal, $lang);
                            $RDFdata->register($idC, $prop, $IDD, 0);
                        }

                }

            $Item = new \App\Models\Find\Items\Index();
            $dtd = [];
            $dtd['i_concept'] = $idC;
            $dtd['i_library'] = 0;
            $dtd['i_work'] = $idC;
            //$dtd['i_manitestation'] = $idC;
            $dtd['i_titulo'] = $title;
            $dtd['i_status'] = 5;
            $dtd['i_identifier'] = $isbn;
            $dtd['i_search'] = strtolower(ascii($title.' '.$isbn.' '.$authors));
            $Item->set($dtd)->insert();
            return $dtd;
        }
}
