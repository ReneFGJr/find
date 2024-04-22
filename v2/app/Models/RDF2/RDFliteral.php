<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFliteral extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdf_literal';
    protected $primaryKey       = 'id_n';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'n_name', 'n_lock', 'n_lang', 'n_md5'
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

    function register($name,$lang='pt_BR',$lock=1)
        {
            $new = true;

            $Language = new \App\Models\AI\NLP\Language();
            $lang = $Language->normalize($lang);

            $dt = $this->where('n_name',$name)->findAll();

            if (count($dt) > 0)
                {
                    foreach($dt as $idx=>$line)
                        {
                            if ($line['n_lang'] == $lang)
                                {
                                    $new = false;
                                    $ID = $line['id_n'];
                                }
                        }
                }

            $name = trim($name);
            if ($name == '')
                {
                    $name = '::Em Branco::';
                }


            /*********************************************** NOVO */
            if ($new)
                {
                    $d['n_name'] = $name;
                    $d['n_lang'] = $lang;
                    $ID = $this->set($d)->insert();
                }
            return $ID;
        }

        function ascii()
            {
                $sx = '';
                $dt = $this
                    ->like('n_name','%Ã£')
                    ->findAll(200);
                foreach($dt as $id=>$line)
                    {
                        $name = $line['n_name'];
                        $name = troca($name, '´',"'");
                        $name = utf8_decode($name);
                        $dd['n_name'] = $name;
                        $id = $line['id_n'];

                        if (strpos($name, chr(227)))
                            {
                                $sx .= bsmessage('ORIGINAL:'.$line['n_name'].'<br>CONVERT:'.$name.' - '.$id,3);
                                $name = $this->convert_manual($line['n_name']);
                                $sx .= bsmessage('CONVERT:' . $name . ' - ' . $id, 1);
                                $sx .= '<tt>'.hexdump($line['n_name']). '</tt>';
                                $dd['n_name'] = $name;
                                $this->set($dd)->where('id_n', $id)->update();
                            } else {
                                $this->set($dd)->where('id_n', $id)->update();
                                $sx .= h( $line['n_name'].'<br>TO: '.$name . '<br>==>'.$id,4);
                            }
                    }
                return $sx;
            }
    function convert_manual($n)
        {
            $n = troca($n, chr(195).chr(163).chr(194).chr(173), 'í');
            $n = troca($n, 'ã§','ç');
            $n = troca($n, 'ã£', 'ã');
            $n = troca($n, 'ã³','ó');
            $n = troca($n, 'ãª','ê');
            $n = troca($n, 'ãµ','õ');
            $n = troca($n, 'ã©','é');
            $n = troca($n, 'ãº','ú');
            $n = troca($n, 'ã¡','á');
            $n = troca($n, 'â',' ');
            return $n;
        }
}
