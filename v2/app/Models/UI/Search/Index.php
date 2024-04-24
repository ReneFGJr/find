<?php

namespace App\Models\UI\Search;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_name';
    protected $primaryKey       = 'id_n';
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

    function searchAPI($term, $class = '')
        {
            $lib = 1016;
            $ord = 'i_titulo';
            $Cover = new \App\Models\Find\Cover\Index();

            $item = new \App\Models\Find\Items\Index();
            $terms = explode(' ', $term);

            foreach ($terms as $n) {
                $item->like('i_titulo', $n);
            }
            $item->where('i_library',$lib);
            $item->orderBy($ord);
            $dt = $item->findAll(200);

            $RSP = [];
            foreach ($dt as $idb => $line) {
                $dd = [];
                $dd['title'] = $line['i_titulo'];
                $dd['isbn'] = $line['i_identifier'];
                $dd['cover'] = $Cover->cover($dd['isbn']);
                $dd['ID'] = $line['id_i'];
                $dd['library'] = $line['i_library'];
                array_push($RSP,$dd);
            }
            return $RSP;

        }

    function searchAPIrdf($term,$class='')
        {
            $Cover = new \App\Models\Find\Cover\Index();
            $RSP = [];
            $terms = explode(' ',$term);

            $this->join('rdf_concept', 'cc_pref_term = id_n');
            $this->join('rdf_class','cc_class = id_c');

            if ($class != '')
                {
                    $this->where('c_class',$class);
                }
            foreach($terms as $n)
                {
                    $this->like('n_name', $n);
                }
            $this->orderBy('n_name');
            $dt = $this->findAll(100);
            //echo $this->getlastquery();
            //pre($dt);
            $RSP = [];
            foreach($dt as $idb=>$line)
                {
                    $dd = [];
                    $dd['Title'] = $line['n_name'];
                    $dd['Cover'] = $Cover->cover('');
                }

            return $RSP;
        }
}
