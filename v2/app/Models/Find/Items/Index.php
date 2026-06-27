<?php

namespace App\Models\Find\Items;

use CodeIgniter\Model;
use App\Models\Find\Check\CheckerModel;

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
    protected $allowedFields    = [
        'id_i',
        'i_tombo',
        'i_manifestation',
        'i_titulo',
        'i_status',
        'i_aquisicao',
        'i_indexer',
        'i_year',
        'i_localization',
        'i_ln1',
        'i_ln2',
        'i_ln3',
        'i_ln4',
        'i_type',
        'i_identifier',
        'i_uri',
        'i_library',
        'i_library_place',
        'i_library_classification',
        'i_created',
        'i_ip',
        'i_usuario',
        'i_dt_emprestimo',
        'i_dt_prev',
        'i_dt_renovavao',
        'i_exemplar',
        'i_work',
        'i_expression',
        'i_autores',
        'i_search',
        'i_ln1',
        'i_ln2',
        'i_ln3',
        'i_ln4'
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

    function index($d1 = '', $d2 = '', $d3 = '')
    {
        $RSP = [];
        switch ($d1) {
            case 'search':
                $RDFform2 = new \App\Models\FindServer\RDFform2();
                $term = get("searchTerm");
                $formID = get("formID");
                $RSP['term'] = $term;
                $RSP['formID'] = $formID;
                $RSP['options'] = $RDFform2->searchAPI($term, $formID);
                break;
            case 'concept':
                $RSP['ppsot'] = $_POST;
                break;
            case 'check':
                $RDFform = new \App\Models\FindServer\RDFform2();
                $RSP = $RDFform->checkRegister($d2);
                break;
            case 'edit':
                $RDFform = new \App\Models\FindServer\RDFform2();
                $RSP = $RDFform->editForm($d2, get("library"));
                break;
            case 'rdf':
                $Editor = new \App\Models\FindServer\RDFform();
                $RSP = $Editor->getForm($d2, get("library"));
                break;
            case 'property':
                $RDFform = new \App\Models\FindServer\RDFform();
                $RSP = $RDFform->getForm(get("type"), get("library"));
                break;
            case 'moveProperty':
                $RDFform = new \App\Models\FindServer\RDFform2();
                $RSP = $RDFform->moveProperty(get("type"), get("library"), get("subgroup"), get("id"), get("pos"));
                break;
            case 'formByLibrary':
                $RDFform = new \App\Models\FindServer\RDFform2();
                if ($d2 == '') {
                    $d2 = get("library");
                }
                if ($d3 == '') {
                    $d3 = get("id");
                }
                $RSP = $RDFform->formByLibrary($d2, $d1);
                break;
            case 'property_save':
                $RDFform = new \App\Models\FindServer\RDFform2();
                $RSP = $RDFform->property_save(get("type"), get("library"));
                break;
        }
        $RSP['d1'] = $d1;
        $RSP['d2'] = $d2;
        $RSP['d3'] = get("library");
        return $RSP;
    }

    public function getAuthorsByLibrary($library,$search = '')
    {
        $Class = new \App\Models\Find\Rdf\RDF_Class();
        $prop1 = $Class->getIdByName('hasAuthor');
        $prop2 = $Class->getIdByName('hasTranslator');


        $this
            ->select('d_r2, n_name, cc_use as id_cc')
            ->join('rdf_data', 'rdf_data.d_p in (' . $prop1 . ',' . $prop2 . ') and (rdf_data.d_r1 = find_item.i_manifestation or rdf_data.d_r1 = find_item.i_work)')
            ->join('rdf_concept', 'rdf_concept.id_cc = rdf_data.d_r2')
            ->join(rdf_name, 'rdf_concept.cc_pref_term = RDF_name.id_n');

        if ($library != '') {
            $this->where('i_library', $library);
        }

        $this->where('i_autores <> ""');
        if ($search != '') {
            $this
                ->groupStart()
                ->like('n_name', $search)
                ->groupEnd();
            $this->where('rdf_concept.id_cc = rdf_concept.cc_use');
        }
        $dt = $this->groupby('d_r2, n_name, cc_use')
            ->findAll();

        $authors = [];
        foreach ($dt as $line) {
            $id = $line['id_cc'];
            $authors[][$id] = $line['n_name'];
        }
        // Ordena pelo valor (nome)
        usort($authors, function($a, $b) {
            $nameA = reset($a);
            $nameB = reset($b);
            return strcoll($nameA, $nameB);
        });
        return $authors;
    }

    public function getIndexesByType($type, $lib, $place = '')
    {
        switch ($type) {
            case 'title':
                $ord = 'i_titulo';
                break;
            case 'author':
                $ord = 'i_autores';
                break;
            case 'subject':
                $ord = 'i_ln1';
                break;
            default:
                $ord = 'i_titulo';
        }
        $this
            ->select($ord . ' as label, i_identifier, max(id_i) as id_i, i_library')
            ->where('i_library', $lib)
            ->where('i_titulo <> ""');
        if ($place != '') {
            $this->where('i_library_place', $place);
        }
        $dt = $this->groupBy($ord . ', i_identifier, i_library')
            ->orderBy($ord)
            ->findAll();

        $LT = [];
        foreach ($dt as $id => $line) {
            $text = strtoupper(ascii($line['label']));
            $letra = substr($text, 0, 1);
            if (!isset($LT[$letra])) {
                $LT[$letra] = [];
            }
            $LT[$letra][] = [
                'label' => $line['label'],
                'isbn' => $line['i_identifier'],
                'ID' => $line['id_i'],
                'library' => $line['i_library']
            ];
        }
        ksort($LT);
        return $LT;
    }

    public function changeData()
        {
            $RDF_Data = new \-App\Models\Find\Rdf\RDF_data();
            $dt = $RDF_Data
                ->select('id_d, d_r1, d_r2, d_p, id_cc, cc_class')
                ->join('rdf_concept', 'd_r1 = id_cc')
                ->join('rdf_class', 'id_c = cc_class')
                ->where('d_p', 5)
                ->where('id_c', 16)
                ->findAll(1000);
            $dd = [];
            $dd['d_p'] = 17;

            foreach ($dt as $line) {
                $id = $line['id_d'];
                $RDF_Data->set($dd)->where('id_d', $id)->update();
            }
            if (count($dt) > 0) {
                echo "Processados " . count($dt) . " registros. Continuando...";
                echo '<meta http-equiv="refresh" content="0;">';
                exit;
            }
            return count($dt);
        }

    public function rebuildAllFields($offset, $limit = 100)
    {
        if ($offset == 0) {
            $this->changeData();
        }
        $CheckerModel = new CheckerModel();

        $dt = $this
            ->orderBy('id_i', 'ASC')
            ->findAll($limit, $offset);

        foreach ($dt as $line) {
            echo $CheckerModel->updateDataTitleAuthor($line);
        }

        if (count($dt) == 0)
            {   return ""; }
            else {
                return "Continue";
            }
    }

    public function reindexAll($offset = 0, $limit = 100)
    {
        $dt = $this->where('i_titulo !=', '')
            ->findAll($limit, $offset);

        foreach ($dt as $line) {

            $search  = ascii($line['i_titulo']);
            $search .= ' ' . ascii($line['i_autores']);
            $search .= ' ' . ascii($line['i_ln1']);
            $search .= ' ' . ascii($line['i_ln2']);
            $search .= ' ' . ascii($line['i_ln3']);
            $search .= ' ' . ascii($line['i_ln4']);
            $search = strtolower($search);
            $search = preg_replace('/[^\p{L}0-9 ]/u', '', $search);

            if ($search != $line['i_search']) {
                $this->set(['i_search' => $search])
                    ->where('id_i', $line['id_i'])
                    ->update();
            }
        }

        if (count($dt) == 0) {
            return "";
        } else {
            return "Continue";
        }
    }


    function check()
    {
        $RDFclass = new \App\Models\FindServer\RDFclass();
        $RDFdata = new \App\Models\FindServer\RDFdata();
        $Class = $RDFclass->getClass('isAppellationOfManifestation');
        if ($Class != []) {
            $Class = $Class['id_c'];
        } else {
            echo "OPS - Classe não encontrada";
            exit;
        }
        $dt = $this
            ->where('i_work', 0)
            ->where('i_manifestation >', 0)
            ->FindAll(1000);

        foreach ($dt as $id => $line) {
            $dti = $RDFdata
                ->select('d2.d_r1 as W, rdf_data.d_r1 as E, rdf_data.d_r2 as M')
                ->join('rdf_data as d2', 'd2.d_r2 = rdf_data.d_r1', 'left')
                ->where('rdf_data.d_p', $Class)
                ->where('rdf_data.d_r2', $line['i_manifestation'])
                ->first();

            $DD = [];
            $DD['i_work'] = $dti['W'];
            $DD['i_expression'] = $dti['E'];
            $this->set($DD)->where('id_i', $line['id_i'])->update();
        }
        return ["Total" => count($dt) . " itens"];
    }

    function addItem($ISBN, $LIBRARY)
    {
        if (($ISBN == '') or ($LIBRARY == '')) {
            return [
                'status' => '400',
                'msg'    => 'ISBN e Biblioteca são obrigatórios'
            ];
        }
        $dt = $this->where('i_identifier', $ISBN)
            ->where('i_library', $LIBRARY)
            ->first();
        if ($dt == []) {
            $dt = $this->where('i_identifier', $ISBN)
                ->first();
        }
        unset($dt['id_i']);
        $dt['i_library'] = $LIBRARY;
        $dt['i_tombo'] = $this->nextTombo($LIBRARY);
        $dt['i_created'] = date('Y-m-d H:i:s');
        $dt['i_ip'] = $_SERVER['REMOTE_ADDR'];
        $dt['i_exemplar'] = $this->nextExemplar($ISBN, $LIBRARY);
        $dt['i_dt_emprestimo'] = '0000-00-00';
        $dt['i_dt_prev'] = 0;
        $dt['i_dt_renovavao'] = 0;
        $dt['i_status'] = 1;
        $id = $this->insert($dt);
        $RSP['status'] = '200';
        $RSP['tombo'] = $dt['i_tombo'];
        $RSP['msg'] = 'Item adicionado com sucesso';
        return $RSP;
    }

    function nextExemplar($ISBN, $LIBRARY)
    {
        $dt = $this
            ->where('i_library', $LIBRARY)
            ->where('i_identifier', $ISBN)
            ->orderBy('i_exemplar', 'desc')
            ->first();
        if ($dt) {
            return $dt['i_exemplar'] + 1;
        }
        return 1;
    }

    function nextTombo($LIBRARY)
    {
        $dt = $this->where('i_library', $LIBRARY)
            ->orderBy('i_tombo', 'desc')
            ->first();
        if ($dt) {
            return $dt['i_tombo'] + 1;
        }
        return 1;
    }

    function buscaAvancada($termo, $place, $lib)
    {
        $limit = 48;
        $offset = 0;

        $builder = $this
            ->select('i_titulo, i_identifier, max(id_i) as id_i, i_library')
            ->where('i_library', $lib);
        if ($place > 0) {
            $builder = $builder->where('i_library_place', $place);
        }
        $termo = ascii($termo);
        $termo = strtolower($termo);
        $t = explode(' ', $termo);
        $first = true;
        foreach ($t as $w) {
            if (strlen($w) > 2) {
                if ($first) {
                    $builder = $builder->like('i_search', $w);
                    $first = false;
                } else {
                    $builder = $builder->Like('i_search', $w);
                }
            }
        }

        $dt = $builder->groupBy('i_titulo, i_identifier')
            ->orderBy('id_i desc')
            ->findAll($limit, $offset);

        if (count($dt) == 0) {
            echo '<div class="alert alert-warning">Nenhum resultado encontrado para "' . htmlspecialchars($termo) . '"</div>';
            echo '<div class="alert alert-warning"><tt>' . $this->getlastquery() . '</tt></div>';
        }

        return $this->prepare_record($dt);
    }

    function vitrine($lib = '')
    {
        $limit = 48;
        $offset = 0;
        $dt = $this
            ->select('i_titulo, i_identifier, max(id_i) as id_i, i_library')
            ->where('i_library', $lib)
            ->where('i_titulo <> ""')
            ->groupBy('i_titulo, i_identifier')
            ->orderBy('id_i desc')
            ->findAll($limit, $offset);

        return $this->prepare_record($dt);
    }

    function prepare_record($dt)
    {
        $RSP = [];
        $Covers = new \App\Models\Find\Cover\Index();
        foreach ($dt as $id => $line) {
            $dd = [];
            $dd['title'] = $line['i_titulo'];
            $dd['isbn'] = $line['i_identifier'];
            $dd['cover'] = $Covers->cover($line['i_identifier']);
            $dd['ID'] = $line['id_i'];
            $dd['library'] = $line['i_library'];
            array_push($RSP, $dd);
        }
        return $RSP;
    }

    function getPubItem($ID, $lib = '')
    {
        $RDF = new \App\Models\Find\Rdf\RDF();
        $dt = $RDF->le($ID);

        $wk = [];
        $dd = [];
        $dd['library'] = $lib;

        $Item = new \App\Models\Find\Items\Index();
        if ($dt != []) {
            $dd['Class'] = $dt['concept']['Class'];
            $dd['PrefLabel'] = $dt['concept']['name'];
            $dd['Language'] = $dt['concept']['lang'];
            $dd['ID'] = $ID;

            foreach ($dt['data'] as $ida => $line) {
                if ($dd['Class'] == 'Work') {
                    array_push($wk, $dd['ID']);
                    $Item->orWhere('i_work', $dd['ID']);
                }
            }

            $dti = $Item
                ->orderBy('i_titulo')
                ->findAll();
            foreach ($dti as $idi => $linei) {
                $xlib = $linei['i_library'];
                if ($lib != $xlib) {
                    unset($dti[$idi]);
                }
            }
        }
        $dd['works'] = $wk;
        return $dd;
    }

    function searchTitle($title, $library = '')
    {
        $t = explode(' ', $title);
        foreach ($t as $w) {
            if (strlen($w) > 2) {
                $this->like('i_titulo', $w);
            }
        }
        if ($library != '') {
            $this->where('i_library', $library);
        }
        $dt = $this->orderBy('i_titulo')->findAll(30);

        $RSP = [];
        $ISBN = [];
        foreach ($dt as $id => $line) {
            $ISBNb = $line['i_identifier'];
            if (!isset($ISBN[$ISBNb])) {
                $ISBN[$ISBNb] = 1;
                $da = $this->getISBN($line['i_identifier'], $line['i_library']);
                $RSP[] = $da;
            }
        }
        return $RSP;
    }

    function getISBN($isbn, $lib = '')
    {
        $Cover = new \App\Models\Find\Cover\Index();

        if ($lib == '') {
            $dt = $this
                ->where('i_identifier', $isbn)
                ->first();
        } else {
            $dt = $this
                ->where('i_identifier', $isbn)
                ->where('i_library', $lib)
                ->first();
        }

        $META = [];
        $RSP = [];

        if ($dt != []) {
            $RSP['title'] = $dt['i_titulo'];
            $RSP['isbn'] = $dt['i_identifier'];

            $RSP['items'] = $this->exemplares($isbn, $lib);
            $RSP['cover'] = $Cover->cover($isbn);

            $metadata = [];

            $RSP['ID'] = $dt['i_manifestation'];

            $RDF = new \App\Models\Find\Rdf\RDF();

            /********************* Expression ****/
            $idW = $dt['i_work'];
            if ($idW > 0) {
                $dtW = $RDF->le($idW);
            } else {
                $dtW = [];
            }

            /********************* Expression ****/
            $idE = $dt['i_expression'];
            if ($idE > 0) {
                $dtE = $RDF->le($idE);
            } else {
                $dtE = [];
            }

            /********************* Manifestation */
            $idM = $dt['i_manifestation'];
            if ($idM > 0) {
                $dtM = $RDF->le($idM);
            } else {
                $dtM = [];
            }
            $meta = [];
            if (!isset($dtM['data'])) {
                $dtM['data'] = [];
            }
            if (!isset($dtW['data'])) {
                $dtW['data'] = [];
            }
            if (!isset($dtE['data'])) {
                $dtE['data'] = [];
            }
            $meta['data'] = array_merge($dtM['data'], $dtW['data'], $dtE['data']);

            $Metadata = new \App\Models\Find\Metadata\Index();
            $META = $Metadata->metadata($meta, $META);

            /*********** Expression */
            $expression = $RDF->extract($dtM, 'isAppellationOfManifestation', 'A');

            $WORK = [];

            foreach ($expression as $ide => $expr) {
                $idE = $expr['ID'];
                $dtR = $RDF->le($expr['ID']);
                $WORK = $RDF->extract($dtR, 'isAppellationOfExpression', 'A');

                if (isset($WORK[0]['ID'])) {
                    $dtW = $RDF->le($WORK[0]['ID']);
                    $META = $Metadata->metadata($dtW, $META);
                }
            }

            $META = $Metadata->metadata($dtM, $META);


            $META = $this->prepara_classe_colors($META);
            $RSP['meta'] = $META;
        } else {
            $RSP = [];
        }
        return $RSP;
    }
    function prepara_classe_colors($META)
    {
        if (isset($META['ColorClassification'])) {
            $COLORS = $META['ColorClassification'];
            foreach ($COLORS as $ida => $line) {
                $name = $line['name'];
                $namex = $line['name'];
                $color1 = '#ffffff';
                $color2 = '#000000';
                if ($pos = strpos($name, '#')) {
                    $color1 = substr($name, $pos, strlen($name));
                    $color2 = substr($color1, 7, 7);
                    if ($color2 != '') {
                        $color1 = substr($color1, 0, 7);
                    } else {
                        $color2 = '#000000';
                    }
                    $name = trim(substr($name, 0, $pos));
                }
                $COLORS[$ida]['background'] = $color1;
                $COLORS[$ida]['textcolor'] = $color2;
                $COLORS[$ida]['name'] = $name;
                $COLORS[$ida]['namex'] = $namex;
            }
            $META['ColorClassification'] = $COLORS;
        }
        return $META;
    }

    function getItem($id, $library)
    {
        $dt = $this
            ->where('id_i', $id)
            ->where('i_library', $library)
            ->first();
        return $dt;
    }

    function getItemTombo($TomboID, $lib)
    {
        $Cover = new \App\Models\Find\Cover\Index();
        $cp = 'i_titulo, i_identifier, i_exemplar, i_library, i_tombo, ';
        $cp .= 'i_ln1, i_ln2, i_ln3, i_ln4, i_dt_emprestimo, i_dt_prev, ';
        $cp .= 'is_name, lp_name, id_is as status, us_nome, id_us';
        $RSP = $this
            ->select($cp)
            ->join('library_place', 'id_lp = i_library_place', 'LEFT')
            ->join('find_item_status', 'id_is = i_status', 'LEFT')
            ->join('users', 'i_usuario = id_us', 'LEFT')
            ->where('i_tombo', $TomboID)
            ->where('i_library', $lib)
            ->first();



        $RSP['atrasado'] = 0;

        // Supondo que $line['loan'] esteja em formato 'YYYY-MM-DD' ou similar
        $loanDate = $RSP['i_dt_prev'];
        if ($loanDate == '0') {
            $loanDate = '00000000';
        }
        $formatted = substr($loanDate, 0, 4) . '-' .
            substr($loanDate, 4, 2) . '-' .
            substr($loanDate, 6, 2);

        $RSP['i_dt_prev'] = $formatted;

        if ($RSP['status'] == '6') {
            $loanTimestamp = strtotime($formatted);
            $todayTimestamp = time();

            // Se a data de empréstimo for anterior a hoje, está atrasado
            if ($loanTimestamp < $todayTimestamp) {
                $RSP['atrasado'] = 1;
            }
        }
        return $RSP;
    }

    function exemplares($isbn, $lib)
    {
        $dt = $this
            ->join('library_place', 'id_lp = i_library_place', 'LEFT')
            ->join('find_item_status', 'id_is = i_status', 'LEFT')
            ->where('i_identifier', $isbn)
            ->where('i_library', $lib)
            ->findAll();
        $RSP = [];
        foreach ($dt as $id => $line) {
            $dd = [];
            $local = $line['i_ln1'] . ' ' . $line['i_ln2'];
            if ($line['i_ln3'] != '') {
                $local .= ' ' . $line['i_ln3'];
            }
            if ($line['i_ln4'] != '') {
                $local .= ' ' . $line['i_ln4'];
            }
            $dd['local'] = $local;
            $dd['exemplar'] = $line['i_exemplar'];
            $dd['tombo'] = $line['i_tombo'];
            $dd['status'] = $line['is_name'];
            $dd['place'] = $line['lp_name'];
            $dd['loan'] = $line['i_dt_emprestimo'];
            $dd['atrasado'] = 0;

            /* Atrasado */
            $loanTimestamp = strtotime($dd['loan']);
            $todayTimestamp = time();

            // Se a data de empréstimo for anterior a hoje, está atrasado
            if ($loanTimestamp < $todayTimestamp) {
                $dd['atrasado'] = 1;
            }
            array_push($RSP, $dd);
        }
        return $RSP;
    }
}
