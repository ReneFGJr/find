<?php

namespace App\Models\Find\Indexes;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'find_item';
    protected $primaryKey       = 'id_i';
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

    function getStatus()
        {
            $lib = get("library");
            $cp = 'i_status, is_name, count(*) as total';
            $this->select($cp)
                ->join('find_item_status', 'i_status = id_is')
                ->where('i_library', $lib)
                ->groupBy('i_status')
                ->orderBy('i_status, is_name');
            $dt = $this->findAll();
            $RSP = [];
            $RSP['library'] = $lib;
            $status = [];
            foreach($dt as $d)
                {
                    $st = ['id'=>$d['i_status'],'name'=>$d['is_name'],'total'=>$d['total']];
                    $status[] = $st;
                }
            $RSP = $status;
            return $RSP;
        }

    function getStatusRow($id)
        {
            //return array_merge($_POST,$_GET);
            $lib = get("library");
            $status = get("status");
            $cp = '*';
            $this->select($cp)
                ->join('find_item_status', 'i_status = id_is')
                ->where('i_library', $lib)
                ->where('i_status', $status)
                ->orderBy('i_tombo DESC, is_name');
            $dt = $this->findAll();

            $RSP = [];
            $RSP['library'] = $lib;
            $RSP['books'] = $dt;
            $RSP['statusID'] = $status;
            return $RSP;
        }

    function getIndex($type,$lib)
        {
            $RSP = [];
            $Generate = new \App\Models\Find\Indexes\Generate();
            $RSP['message'] = $Generate->expression($lib);

            if ($lib == '')
                {
                    $RSP['message'] = 'Biblioteca não informada';
                    $RSP['_GET'] = $_GET;
                    $RSP['_POST'] = $_GET;
                    $RSP['lib'] = $lib;
                }
            return $RSP;
        }

        function indexManifestation($lib,$class= 'hasSubject')
            {
                $this->select('d_r2 as ID, n_name as name');
                $this->join('rdf_data', 'i_manitestation = d_r1');
                $this->join('rdf_class', 'd_p = id_c');
                $this->join('rdf_concept', 'd_r2 = id_cc');
                $this->join('rdf_literal', 'cc_pref_term = id_n');
                $this->where('i_library', $lib);
                $this->where('i_manitestation > 0');
                $this->where('c_class', $class);
                $this->groupBy('d_r2, n_name');
                $this->orderby('n_name');
                $dt = $this->findAll();

                return $dt;
            }

        /****************************************/
        function indexWork($lib,$class= 'hasAuthor')
            {
                $this->select('d_r2 as ID, n_name as name');
                $this->join('rdf_data','i_work = d_r1');
                $this->join('rdf_class','d_p = id_c');
                $this->join('rdf_concept', 'd_r2 = id_cc');
                $this->join('rdf_literal', 'cc_pref_term = id_n');
                $this->where('i_library',$lib);
                $this->where('i_work > 0');
                $this->where('c_class', $class);
                $this->groupBy('d_r2, n_name');
                $this->orderby('n_name');
                $dt = $this->findAll();

                return $dt;
            }
}
