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

    function makeSearch($lib)
        {
            $item = new \App\Models\Find\Items\Index();
            $dt = $item
                ->where('i_search','')
                ->where('i_library', $lib)
                ->findAll(100);

            foreach($dt as $id=>$line)
                {
                    $t = $line['i_titulo'];
                    $t .= ' ' .$line['i_identifier'];
                    $lib = $line['i_library'];
                    $dta = $item->getISBN($line['i_identifier'],$lib);

                    if (isset($dta['meta']['Authors']))
                        {
                            foreach($dta['meta']['Authors'] as $ida=>$linea)
                                {
                                    $t .= ' ' . $linea['name'];
                                }
                        }
                    $dd['i_search'] = strtolower(ascii($t));
                    $ID = $line['i_identifier'];
                    $item->set($dd)->where('i_identifier',$line['i_identifier'])->update();
                }
        }

    function searchTitle($title,$library)
        {
            $item = new \App\Models\Find\Items\Index();
            $dt = $item->searchTitle($title, $library);
            return $dt;
        }

    function searchISBN($isbn,$library)
        {
            $item = new \App\Models\Find\Items\Index();
            $dt = $item->getISBN($isbn, $library);
            if ($dt == [])
                {
                    $dt = $item->getISBN($isbn,'');
                }

            if ($dt == [])
                {
                    $Z3950 = new \App\Models\Z3950\Index();
                    $RSP = $Z3950->searchISBN($isbn);

                    /*************************  ****** Register a Book */
                    $RDF = new \App\Models\FindServer\Index();
                    $RDF->register($RSP);

                    $dt = $item->getISBN($isbn,'');
                }

            return $dt;
        }

    function searchAPI($term, $class = '')
        {
            $lib = get("library");
            //$this->makeSearch($lib);


            $ord = 'i_titulo';
            $Cover = new \App\Models\Find\Cover\Index();

            $item = new \App\Models\Find\Items\Index();
            $terms = explode(' ', $term);


            $cp = 'i_titulo, i_identifier, i_library';


            $item->select($cp.', max(id_i) as id_i');
            foreach ($terms as $n) {
                $item->like('LOWER(i_search)', mb_strtolower($n));
            }
            $item->where('i_library',$lib);
            $item->groupby($cp);
            $item->orderBy($ord);
            $dt = $item->findAll(200);

            //echo $item->getlastquery();
            //exit;
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
