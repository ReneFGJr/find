<?php

namespace App\Models\Find\Indexes;

use CodeIgniter\Model;

class Generate extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'find_item';
    protected $primaryKey       = 'id_i';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['i_expression', 'i_work'];

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

    function expression($lib)
        {
            $RDF = new \App\Models\RDF2\RDF();
            $sx = '';
            $this->where('i_expression',0);
            $this->where('i_library',$lib);
            $this->where('i_manitestation <> 0');
            $this->where('i_expression = 0');

            $dt = $this->findAll(1000);
            foreach($dt as $idl=>$linel)
                {
                    $manifestation = $linel['i_manitestation'];
                    $ID = $linel['id_i'];

                    $dtm = $RDF->le($manifestation);
                    $expre = $RDF->extract($dtm, 'isAppellationOfManifestation','A');
                    if (count($expre) > 0)
                        {
                            $dd = [];
                            $dd['i_expression'] = $expre[0];

                            /*************** WORK */
                            $dte = $RDF->le($expre[0]);
                            $work = $RDF->extract($dte, 'isAppellationOfExpression', 'A');

                            if (isset($work[0]))
                                {
                                    $dd['i_work'] = $work[0];
                                    $this->set($dd)->where('id_i', $ID)->update();
                                }
                        } else {
                            pre($expre);
                        }
                }
                $sx .= count($dt).' as converted';
                return $sx;
        }
}
