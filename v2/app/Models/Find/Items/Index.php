<?php

namespace App\Models\Find\Items;

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
    protected $allowedFields    = [
        'id_i',
        'i_tombo',
        'i_manitestation',
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
        'i_search'
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

    function addItem($ISBN,$LIBRARY)
    {
        if (($ISBN=='') or ($LIBRARY=='')) {
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
        $dt['i_exemplar'] = $this->nextExemplar($ISBN,$LIBRARY);
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

    function nextExemplar($ISBN,$LIBRARY)
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

    function vitrine($lib = '')
    {
        $Covers = new \App\Models\Find\Cover\Index();
        $limit = 48;
        $offset = 0;
        $dt = $this
            ->select('i_titulo, i_identifier, max(id_i) as id_i')
            ->where('i_library', $lib)
            ->where('i_titulo <> ""')
            ->groupBy('i_titulo, i_identifier')
            ->orderBy('id_i desc')
            ->findAll($limit, $offset);

        $RSP = [];
        foreach ($dt as $id => $line) {
            $dd = [];
            $dd['title'] = $line['i_titulo'];
            $dd['isbn'] = $line['i_identifier'];
            $dd['cover'] = $Covers->cover($line['i_identifier']);
            $dd['ID'] = $line['id_i'];
            $dd['library'] = $lib;
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
            $dd['Class'] = $dt['concept']['c_class'];
            $dd['PrefLabel'] = $dt['concept']['n_name'];
            $dd['Language'] = $dt['concept']['n_lang'];
            $dd['ID'] = $ID;

            foreach ($dt['data'] as $ida => $line) {
                if ($line['Class'] == 'Work') {
                    array_push($wk, $line['ID']);
                    $Item->orWhere('i_work', $line['ID']);
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
        foreach($t as $w)
            {
                if (strlen($w) > 2)
                    {
                        $this->like('i_titulo', $w);
                    }
            }
        if ($library != '')
            {
                $this->where('i_library', $library);
            }
        $dt = $this->orderBy('i_titulo')->findAll(30);

        $RSP = [];
        $ISBN = [];
        foreach ($dt as $id => $line) {
            $ISBNb = $line['i_identifier'];
            if (!isset($ISBN[$ISBNb]))
                {
                    $ISBN[$ISBNb] = 1;
                    $da = $this->getISBN($line['i_identifier'], $line['i_library']);
                    $RSP[] = $da;
                }
        }
        return $RSP;
    }

    function getISBN($isbn, $lib='')
    {
        $Cover = new \App\Models\Find\Cover\Index();

        if ($lib == '')
        {
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

            $RSP['ID'] = $dt['i_manitestation'];

            $RDF = new \App\Models\Find\Rdf\RDF();
            $idM = $dt['i_manitestation'];
            $dtR = $RDF->le($idM);

            $Metadata = new \App\Models\Find\Metadata\Index();
            $META = $Metadata->metadata($dtR, $META);

            /*********** Expression */
            $expression = $RDF->extract($dtR, 'isAppellationOfManifestation', 'A');

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


            $META = $this->prepara_classe_colors($META);
            $RSP['meta'] = $META;
        } else {
            $RSP['meta'] = "OPS ERRO";
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
