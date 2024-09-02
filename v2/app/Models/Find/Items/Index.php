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
        'id_i', 'i_tombo', 'i_manitestation',
        'i_titulo', 'i_status', 'i_aquisicao',
        'i_indexer', 'i_year', 'i_localization',
        'i_ln1', 'i_ln2', 'i_ln3',
        'i_ln4', 'i_type', 'i_identifier',
        'i_uri', 'i_library', 'i_library_place',
        'i_library_classification', 'i_created', 'i_ip',
        'i_usuario', 'i_dt_emprestimo', 'i_dt_prev',
        'i_dt_renovavao', 'i_exemplar', 'i_work',
        'i_expression', 'i_search'
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
        $RDF = new \App\Models\RDF2\RDF();
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
                    $Item->orWhere('i_work',$line['ID']);
                }
            }

            $dti = $Item
                    ->orderBy('i_titulo')
                    ->findAll();
            foreach($dti as $idi=>$linei)
                {
                    $xlib = $linei['i_library'];
                    if ($lib != $xlib)
                        {
                            unset($dti[$idi]);
                        }
                }
        }
        $dd['works'] = $wk;
        return $dd;
    }

    function getISBN($isbn, $lib)
    {
        $Cover = new \App\Models\Find\Cover\Index();
        $dt = $this
            ->where('i_identifier', $isbn)
            ->where('i_library', $lib)
            ->first();

        $META = [];
        $RSP = [];

        if ($dt != []) {
            $RSP['title'] = $dt['i_titulo'];
            $RSP['isbn'] = $dt['i_identifier'];

            $RSP['items'] = $this->exemplares($isbn, $lib);
            $RSP['cover'] = $Cover->cover($isbn);

            $RSP['ID'] = $dt['i_manitestation'];

            $RDF = new \App\Models\RDF2\RDF();
            $dtR = $RDF->le($dt['i_manitestation']);

            $Metadata = new \App\Models\Find\Metadata\Index();
            $META = $Metadata->metadata($dtR, $META);

            /*********** Expression */
            $expression = $RDF->extract($dtR, 'isAppellationOfManifestation', 'A');
            foreach ($expression as $ide => $expr) {
                $dtR = $RDF->le($expr);
                $meta = $Metadata->metadata($dtR, $META);
                $META = array_merge($META, $meta);
            }

            /*********** Work */
            $Work = $RDF->extract($dtR, 'isAppellationOfExpression', 'A');
            foreach ($Work as $ide => $expr) {
                $dtR = $RDF->le($expr);
                $meta = $Metadata->metadata($dtR, $META);
                $META = array_merge($META, $meta);
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
            array_push($RSP, $dd);
        }
        return $RSP;
    }
}
