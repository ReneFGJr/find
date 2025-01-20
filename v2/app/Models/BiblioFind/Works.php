<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

helper('sisdoc');

class Works extends Model
{
    protected $DBGroup          = 'findserver';
    protected $table            = 'find_work';
    protected $primaryKey       = 'id_w';
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

    function indexar()
        {
            $limit = 100;
            $offset = get("offset");
            if ($offset == '') { $offset = 0; }
            $IndiceReverso = new \App\Models\BiblioFind\IndiceReverso();

            $cp = 'w_ID, w_TITLE, w_AUTHORS, w_YEAR';

            $dtt = $this
                ->select('count(*) as total')
                ->first();
            $total = $dtt['total'];

            $cp = 'w_TITLE, w_AUTHORS, pb_name, id_w';
            $dt = $this
                ->select($cp)
                ->join('find_publisher', 'w_PUBLISHER = id_pb')
                ->findAll($limit,$offset);

            $sx = '<h4>Indexando - Total '.$total.', OffSet '.$offset.'</h4>';
            $sx .= '<h6>'.number_format($offset/$total*100,3,',','.').'%</h6>';
            $sc = '';
            foreach($dt as $idx=>$line)
                {
                    $term = $line['w_TITLE'].' ';
                    $term .= $line['w_AUTHORS'] . ' ';
                    $term .= $line['pb_name'] . ' ';
                    $IDc = $line['id_w'];
                    $IndiceReverso->indexar($term, $IDc);
                    $sc .= '<li>'.$line['w_TITLE'].'</li>';

                }
                $sx = bs(bsc($sc,12));
                if (count($dt) > 0)
                    {
                        $sx .= metarefresh('/bibliofind/reindex?offset=' . ($offset + $limit), 5);
                    } else {
                        $sx .= bsmessage("FIM da INDEXAÇÃO");
                    }
                return $sx;
        }

}
