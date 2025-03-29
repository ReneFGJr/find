<?php

namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

class RDF extends Model
{
    var $DBGroup          = 'find';
    var $table            = 'rdfs';
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

    function search($q,$class='')
        {
            $prop = 0;
            if ($class != '')
                {
                    $prop = $this->class($class);
                }
            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $RDFConcept->table = 'find.' . $RDFConcept->table;

            $cp = 'id_cc as id, cc_use as use, n_name as name, c_class as class, c_prefix as prefix, prefix_ref';

            $RDFConcept
                ->select($cp)
                ->join('find.rdf_name', 'cc_pref_term = id_n')
                ->join('find.rdf_class', 'cc_class = id_c')
                ->join('find.rdf_prefix', 'c_prefix = id_prefix');

                $c = [',','.','#','!','?','$','%','&'];
                foreach($c as $id=>$ch)
                    {
                        $q = troca($q, $ch, ' ');
                    }
                $t = explode(' ',$q);

                for($r=0;$r < count($t);$r++)
                    {
                        if ($r==0)
                            {
                                $RDFConcept->where('n_name like "%' . $t[$r] . '%"');
                            } else {
                                $RDFConcept->Where('n_name like "%' . $t[$r] . '%"');
                            }

                    }


            if ($prop > 0)
                {
                    $RDFConcept->where('cc_class',$prop);
                }
            $RDFConcept->orderBy('n_name');
            $dt = $RDFConcept->findAll(10);
            return $dt;
        }

    function concept($name,$class,$lang='NnN')
        {
        $ld_class = $class;
        $id_literal = $this->literal($name, $lang, True);

        if ($class != sonumero($class))
            {
                $ld_class = $this->class($class);
            }

        $idc = $this->register_concept($ld_class, $id_literal);
        return $idc;
        }

    function register_concept($idc, $idn)
        {
            $RDFConcept = new \App\Models\Rdf\RDFConcept();
            $RDFConcept->table = 'find.' . $RDFConcept->table;
            $dt = $RDFConcept
                ->where('cc_class',$idc)
                ->where('cc_pref_term',$idn)
                ->first();

            if ($dt == '')
                {
                    $dt['cc_class'] = $idc;
                    $dt['cc_user'] = 0;
                    $dt['cc_created'] = date("Y-m-dTH:i:s");
                    $dt['cc_pref_term'] = $idn;
                    $dt['cc_origin'] = '';
                    $dt['cc_update'] = date("Y-m-dTH:i:s");
                    $dt['cc_status'] = 1;
                    $dt['cc_library'] = 0;
                    $dt['c_equivalent'] = 0;
                    $idc = $RDFConcept->set($dt)->insert();
                } else {
                    $idc = $dt['id_cc'];
                }
            return $idc;
        }

    function class($class)
        {
        $RDFClass = new \App\Models\Rdf\RDFClass();
        $RDFClass->table = 'find.' . $RDFClass->table;

        $idc = $RDFClass->class($class,false);
        return $idc;
        }

    function literal($name,$lg='pt-BR', $force=1)
    {
        $RDFLiteral = new \App\Models\Rdf\RDFLiteral();
        $RDFLiteral->table = 'find.' . $RDFLiteral->table;

        $idn = $RDFLiteral->name($name, $lg, $force);
        return $idn;
    }

    function show_data($id)
        {
            $RDFData = new \App\Models\Rdf\RDFData();
            $RDFData->table = 'find.' . $RDFData->table;
            $dt = $RDFData
                ->join('find.rdf_class', 'd_p = id_c')
                ->join('find.rdf_prefix', 'c_prefix = id_prefix')
                ->join('find.rdf_name', 'd_literal = id_n', 'left')
                ->where('d_r1',$id)
                ->findAll();

            $rdf = [];
            foreach($dt as $idl=>$line)
                {
                    pre($line,false);
                    $dd = [];
                    $prop = $line['prefix_ref'].':'.$line['c_class'];
                    $value = $line['n_name'];
                    if ($value != '')
                        {
                            $dd[$prop] = $value.'@'.$line['n_lang'];
                        } else {
                            $dd[$prop] = 'find:'.$line['d_r2'].'#';
                        }
                    array_push($rdf,$dd);

                }
            return $rdf;

        }

    function prop($resource_1,$prop, $resource_2,$literal)
        {
            $RDFData = new \App\Models\Rdf\RDFData();
            $RDFData->table = 'find.' . $RDFData->table;

            if ($prop != sonumero($prop))
                {
                    $prop = $this->class($prop);
                }

            $dt = $RDFData
                ->where('d_r1', $resource_1)
                ->where('d_p', $prop)
                ->where('d_r2', $resource_2)
                ->where('d_literal', $literal)
                ->first();

            if ($dt == '')
                {
                    $dt['d_r1'] = $resource_1;
                    $dt['d_p'] = $prop;
                    $dt['d_r2'] = $resource_2;
                    $dt['d_literal'] = $literal;
                    $dt['d_library'] = 0;
                    $dt['d_user'] = 0;
                    $dt['d_update'] = date("Y-m-d");
                    $idr = $RDFData->set($dt)->insert();
                }
            return 1;
        }
}
