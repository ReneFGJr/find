<?php

namespace App\Models\BiblioFind;

use CodeIgniter\Model;

helper('nbr');

class Marc21 extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'marc21s';
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

    function index()
        {
            $data = [];
            $data['marc21'] = get('marc21');
            $sx = bsc(h('MARC21',4),12);
            $sx .= bsc(view('widgets/bibliofind/marc21',$data),6);
            $data['data'] = $this->dectect_type($data['marc21']);
            $sx .= bsc(view('widgets/bibliofind/bibliofind_view',$data),6);
            return bs($sx);
        }

    function dectect_type($t)
        {
            if (strpos($t,'$a') > 0) { $t = troca($t,'$','|'); }
            $t = '<pre>'.$t.'</pre>';
            $t = troca($t,chr(13),chr(10));
            $tln = explode(chr(10),$t);

            $dd = [];
            $dd['w_TITLE'] = $this->detectTitle($tln);
            $dd['w_AUTHORS'] = $this->detectAuthors($tln);
            $dd['w_EDITORA'] = $this->detectEditora($tln);
            $dd['w_SUBJECT'] = $this->detectSubject($tln);
            $dd['w_CODE'] = $this->detectCDD($tln);
            $dd['w_ISBN'] = $this->detectISBN($tln);
            $dd['w_YEAR'] = $this->detectYEAR($tln);
            $dd['w_TYPE'] = 'book';
            $dd['w_Language'] = 'pt';

            $Works = new \App\Models\BiblioFind\Works();


            $dd['w_RDF'] = $this->rdf_register($dd['w_ISBN'],$dd);
            $Works->register($dd['w_ISBN'], $dd);
            return $dd;
        }

    function rdf_register($ISBN,$dt)
        {
            $ISBN = 'ISBN:'.$ISBN;
            $RDFconcept = new \App\Models\RDF2\RDFconcept();
            $RDFdata = new \App\Models\RDF2\RDFdata();
            $RDFliteral = new \App\Models\RDF2\RDFliteral();

            $dd = [];
            $dd['Name'] = $ISBN;
            $dd['Lang'] = 'pt';
            $dd['Class'] = 'Work';
            $IDc = $RDFconcept->createConcept($dd);

            /* Titulo */
            $IDt = $RDFliteral->register($dt['w_TITLE'],'pt');
            $RDFdata->register($IDc, 'hasTitle', 0, $IDt);

            /* Ano */
            $dd['Name'] = $dt['w_YEAR'];
            $dd['Lang'] = 'pt';
            $dd['Class'] = 'Number';
            $IDyear = $RDFconcept->createConcept($dd);
            $lit = 0;
            $prop = 'dateOfPublication';
            $RDFdata->register($IDc, $prop, $IDyear, $lit);

            /* Publisher */
            $dd['Name'] = $dt['w_EDITORA'];
            $dd['Lang'] = 'pt';
            $dd['Class'] = 'Publisher';
            $IDpub = $RDFconcept->createConcept($dd);
            $lit = 0;
            $prop = 'isPublisher';
            $RDFdata->register($IDc, $prop, $IDpub, $lit);

            /* Authores */
            $auth = explode(';',$dt['w_AUTHORS']);
            $IDauthor = [];
            foreach($auth as $author)
                {
                    $dd['Name'] = $author;
                    $dd['Lang'] = 'pt';
                    $dd['Class'] = 'Person';
                    $IDauthor = $RDFconcept->createConcept($dd);
                    $lit = 0;
                    $prop = 'hasAuthor';
                    $RDFdata->register($IDc, $prop, $IDauthor, $lit);
                }


            /* Subject */
            $auth = explode(';', $dt['w_SUBJECT']);
            $IDauthor = [];
            foreach ($auth as $subject) {
                $dd['Name'] = $subject;
                $dd['Lang'] = 'pt';
                $dd['Class'] = 'Subject';
                $IDsubj = $RDFconcept->createConcept($dd);
                $lit = 0;
                $prop = 'hasSubject';
                $RDFdata->register($IDc, $prop, $IDsubj, $lit);
            }
            return $IDc;
        }

    function detectYEAR($marc21): string
    {
        $nome = '';
        foreach ($marc21 as $line) {
            $line = trim($line);
            if (substr($line, 0, 4) == '260 ') {
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    if (substr($dd[$r], 0, 1) == 'c') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $nome = troca($nome,'.','');
                    }
                }
            }
        }
        return $nome;
    }


    function detectISBN($marc21): string
    {
        $nome = '';
        foreach ($marc21 as $line) {
            $line = trim($line);
            if (substr($line, 0, 4) == '020 ') {
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    if (substr($dd[$r], 0, 1) == 'a') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $nome = nbr_author($nome, 7);
                        }
                    }
                }
            }
        return $nome;
    }

    function detectEditora($marc21): string
    {
        $tit = '';
        $authors = [];
        foreach ($marc21 as $line) {
            $line = trim($line);
            if ((substr($line, 0, 4) == '260 ')) {
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    if (substr($dd[$r], 0, 1) == 'b') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $nome = troca($nome,';','');
                        $nome = nbr_author($nome, 7);
                        $authors[] = $nome;
                    }
                }
            }
        }
        $auth = '';
        foreach ($authors as $author) {
            if ($auth != '') { $auth .= '; '; }
            $auth .= $author;
        }
        return $auth;
    }

    function detectCDD($marc21): array
    {
        $cutter = '';
        $code = '';
        $ano = '';
        $type = '';
        foreach ($marc21 as $line) {
            $line = trim($line);
            if ((substr($line, 0, 4) == '082 ') or (substr($line, 0, 4) == '080 ')) {
                if (substr($line, 0, 4) == '082 ') {
                    $type = 'CDD';
                } elseIf (substr($line, 0, 4) == '080 ') {
                    $type = 'CDU';
                }
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    $dd[$r] = trim($dd[$r]);
                    if (substr($dd[$r], 0, 1) == 'a') {
                        $code = trim(substr($dd[$r], 2, strlen($dd[$r])));
                    }
                    if (substr($dd[$r], 0, 1) == 'b') {
                        pre($dd);
                        $cutter = trim(substr($dd[$r], 2, strlen($dd[$r])));
                    }
                    if (substr($dd[$r], 0, 1) == 'c') {
                        $ano = trim(substr($dd[$r], 2, strlen($dd[$r])));
                    }

                }
            }
        }
        return [$type,$code,$cutter,$ano];
    }

    function detectSubject($marc21): string
    {
        $tit = '';
        $authors = [];
        foreach ($marc21 as $line) {
            $line = trim($line);
            if ((substr($line, 0, 4) == '650 ') or (substr($line, 0, 4) == '659 ')) {
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    if (substr($dd[$r], 0, 1) == 'a') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $nome = nbr_author($nome, 7);

                        if (substr($nome, -1, 1) == '.') {
                            $nome = substr($nome, 0, strlen($nome) - 1);
                        }
                        $authors[] = $nome;
                    }
                    if (substr($dd[$r], 0, 1) == 'x') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $idx = count($authors) - 1;
                        $authors[$idx] .= ' - '.trim($nome);
                    }
                }
            }
        }
        $auth = '';
        foreach ($authors as $author) {
            if ($auth != '') {
                $auth .= '; ';
            }
            $auth .= $author;
        }
        return $auth;
    }

    function detectAuthors($marc21): string
    {
        $tit = '';
        $authors = [];
        foreach ($marc21 as $line) {
            $line = trim($line);
            if ((substr($line, 0, 4) == '100 ') or (substr($line, 0, 4) == '700 ')) {
                $dd = explode('|', $line);
                for ($r = 0; $r < count($dd); $r++) {
                    if (substr($dd[$r], 0, 1) == 'a') {
                        $nome = trim(substr($dd[$r], 2, strlen($dd[$r])));
                        $nome = nbr_author($nome, 7);

                        if (substr($nome,-1,1) == ',') { $nome = substr($nome,0,strlen($nome)-1); }
                        $authors[] = $nome;
                    }
                }
            }
        }
        $auth = '';
        foreach ($authors as $author) {
            if ($auth != '') {
                $auth .= '; ';
            }
            $auth .= $author;
        }
        return $auth;
    }

    function detectTitle($marc21)
        {
            $tit = '';
            foreach($marc21 as $line)
                {
                    $line = trim($line);
                    if (substr($line,0,4) == '245 ')
                        {
                            $dd = explode('|',$line);
                            for ($r=0;$r < count($dd);$r++)
                                {
                                    if (substr($dd[$r],0,1) == 'a')
                                        {
                                            $tit = substr($dd[$r],2,strlen($dd[$r]));
                                        }

                                    if (substr($dd[$r], 0, 1) == 'b') {
                                        $tit .= substr($dd[$r], 2, strlen($dd[$r]));
                                    }
                                }
                        }
                }
            /* Tratamento */
            $tit = trim($tit);
            if (substr($tit,-1,1)=='/') { $tit = substr($tit,0,strlen($tit)-1); }
            $tit = troca($tit,' :',':');
            return $tit;
        }

}
