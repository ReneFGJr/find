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
    protected $allowedFields    = [
        'w_ID',
        'w_RDF',
        'w_TYPE',
        'w_TITLE',
        'w_AUTHORS',
        'w_YEAR',
        'w_PUBLISHER',
        'w_Language',
        'w_Indexed'
    ];

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

    function v($id)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDF->v($id);
        $sx = '';
        $sx .= bsc(view('widgets/bibliofind/RDF/concept', $dt),12);
        $sx .= bsc(view('widgets/bibliofind/RDF/proprerties', $dt),12);
        return bs($sx);
    }

    function register($ISBN,$dd)
        {
            $Publishers = new \App\Models\BiblioFind\Publishers();
            $dd['w_PUBLISHER'] = $Publishers->register($dd['w_EDITORA']);
            $dd['w_ID'] = $ISBN;
            $dd['w_Indexed'] = 0;
            $dt = $this->where('w_ID',$ISBN)->first();
            if (!$dt)
                {
                    $this->insert($dd);
                    $dt = $this->where('w_ID',$ISBN)->first();
                } else {
                    $this->update($dt['id_w'],$dd);
                    $dt = $this->where('w_ID',$ISBN)->first();
                }
            $this->indexar();
            return $dt;
        }

    function indexar($limit=100)
        {
            $offset = get("offset");

            if ($offset == '') {
                $this->set(['w_Indexed' => 0])->where('id_w > 0')->update();
                $offset = 0;
                }
            $IndiceReverso = new \App\Models\BiblioFind\IndiceReverso();

            $cp = 'w_ID, w_TITLE, w_AUTHORS, w_YEAR';

            $dtt = $this
                ->select('count(*) as total')
                ->where('w_Indexed',0)
                ->first();
            $total = $dtt['total'];
            if ($total == 0)
                {
                    return bs(bsc(bsmessage("FIM da INDEXAÇÃO"),12));
                }
            $cp = 'w_TITLE, w_AUTHORS, pb_name, id_w';
            $dt = $this
                ->select($cp)
                ->join('find_publisher', 'w_PUBLISHER = id_pb')
                ->where('w_Indexed', 0)
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
                    $dd['w_Indexed'] = 1;
                    $this->update($IDc,$dd);
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
