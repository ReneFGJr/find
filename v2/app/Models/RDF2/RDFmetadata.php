<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFmetadata extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfmetadatas';
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

    function simpleMetadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $dt = $RDF->le($ID);
        $dd = [];

        $sm = [
            'hasTitle' => [],
            'hasCover' => [],
            'hasSectionOf' => [],
            'hasAuthor' => [],
        ];

        $da = $dt['data'];
        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];

            if ($prop == 'isPartOfSource') {
                $dr['jnl_frbr'] = $line['ID'];
            }

            if (isset($sm[$prop])) {
                if (!isset($dd[$prop][$lang])) {
                    $dd[$prop][$lang] = [];
                }
                $dc = [];
                $dc[$line['Caption']] = $line['ID'];
                array_push($dd[$prop][$lang], $dc);
            }
        }

        /*************** IDIOMA Preferencial */
        $lg = $this->langPref();
        $de = [];
        foreach ($sm as $prop => $line) {
            foreach ($lg as $id => $lang) {
                if (isset($dd[$prop][$lang])) {
                    if (!isset($de[$prop])) {
                        if (isset($dd[$prop][$lang][0])) {
                            $vlr = $dd[$prop][$lang][0];
                            $de[$prop] = trim(key($vlr));
                        }
                    }
                }
            }
        }

        $dr['ID'] = $ID;
        $dr['data'] = $de;
        return $dr;
    }

    function langPref()
    {
        $dt = ['pt', 'es', 'en', 'nn'];
        return $dt;
    }

    function metadata($ID)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        if (is_array($ID)) {
            $dt = $ID;
        } else {
            $dt = $RDF->le($ID);
        }

        $dd = [];

        $class = $dt['concept']['c_class'];
        switch ($class) {
            case 'Issue':
                return $this->metadataIssue($dt);
                break;
            case 'Article':
                return $this->metadataWork($dt);
                break;
            case 'Book':
                return $this->metadataWork($dt);
                break;
            case 'BookChapter':
                return $this->metadataWork($dt);
                break;
            case 'Proceeding':
                return $this->metadataWork($dt);
                break;
            case 'Journals':
                return $this->metadataSource($dt);
                break;
            case 'Subject':
                return $this->metadataSubject($dt);
                break;
            case 'Section':
                return $this->metadataGeral($dt);
                break;
            case 'Person':
                return $this->metadataPerson($dt);
                break;
            default:
                return $this->metadataGeral($dt);
                exit;
        }
    }

    function metadataPerson($dt)
    {
        $limit = 1000;
        $ABNT = new \App\Models\Metadata\Abnt();
        $dataset = new \App\Models\ElasticSearch\Search();

        $dr = [];
        $dr['name'] = $dt['concept']['n_name'];
        $dr['name_abnt'] = nbr_author(ascii($dt['concept']['n_name']),2);

        $ID1 = $dt['concept']['id_cc'];
        $ID2 = $dt['concept']['cc_use'];
        if ($ID1 <> $ID2) {
            $dr['ID'] = $ID2;
        } else {
            $dr['ID'] = $ID1;
        }

        //$dr['data'] = $dt['data'];

        $dataset->select('*');
        foreach ($dt['data'] as $id => $line) {
            $ID = $line['ID'];
            $dataset->orwhere('ID', $ID);
        }
        $dataset->orderBy('CLASS, YEAR desc');
        $dx = $dataset->findAll($limit);

        $works = [];
        $coauthors = [];
        $coath = [];
        $coathID = [];
        $prod = [];
        $prod_label = [];
        $tag = [];
        $journal = [];
        $netw = [];
        $neta = [];
        $netd = [];
        $node = [];

        $dta = get("di");
        if ($dta == '') {
            $dta = 1990;
        }

        for ($r = $dta; $r <= (date("Y") + 1); $r++) {
            array_push($prod_label, $r);
            array_push($prod, 0);
        }
        $ds = [];
        $ds['Article'] = $prod;
        $ds['Proceeding'] = $prod;
        $ds['BookChapter'] = $prod;
        $ds['Book'] = $prod;

        foreach ($dx as $id => $line) {
            $JSON = (array)json_decode($line['json']);



            $type = $line['CLASS'];

            /************************************** Cloud */
            if (isset($JSON['Subject'])) {
                $wd = (array)$JSON['Subject'];
                if (isset($wd['pt'])) {
                    $wd = (array)$wd['pt'];
                    foreach ($wd as $idw => $kwd) {
                        if (isset($tag[$kwd])) {
                            $tag[$kwd] = $tag[$kwd] + 1;
                        } else {
                            $tag[$kwd] = 1;
                        }
                    }
                }
            }
            /************************************** Journal */
            if (isset($JSON['Issue'])) {
                $IssueJ = (array)$JSON['Issue'];
                if (isset($IssueJ['journal'])) {
                    $IssueJ = (string)$IssueJ['journal'];
                    if (isset($journal[$IssueJ])) {
                        $journal[$IssueJ] = $journal[$IssueJ] + 1;
                    } else {
                        $journal[$IssueJ] = 1;
                    }
                }
            }

            /************************************** Producao */
            $year = $line['YEAR'] - $dta;
            if ($year >= 0) {
                if (isset($ds[$type][$year])) {
                    $ds[$type][$year] = $ds[$type][$year] + 1;
                }
            }


            $ref = $ABNT->short($JSON, False);
            if (!isset($works[$type])) {
                $works[$type] = [];
            }
            array_push($works[$type], $ref);
            /********** Authors */

            /******************** Network */
            $auth = $JSON['authors'];
            $netwa = [];
            foreach ($auth as $ida => $linenm) {
                $name = ascii($linenm->name);
                $name = nbr_author($name, 2);
                array_push($netwa, $name);
                if (!isset($neta[$name]))
                    {
                        $neta[$name] = 1;
                    } else {
                        $neta[$name] = $neta[$name] + 1;
                    }
            }
            /************************************** */

            for ($ar = 0; $ar < count($netwa); $ar++) {
                for ($as = $ar + 1; $as < count($netwa); $as++) {
                    $n1 = $netwa[$ar];
                    $n2 = $netwa[$as];
                    if (isset($netw[$n1][$n2])) { {
                            $netw[$n2][$n1] = $netw[$n1][$n2] + 1;
                        }
                    } else {
                        if (isset($netw[$n2][$n1])) {
                            $netw[$n2][$n1] = $netw[$n2][$n1] + 1;
                        } else {
                            if (!isset($netw[$n1])) {
                                $netw[$n1] = [];
                            }
                            $netw[$n1][$n2] = 1;
                        }
                    }
                }
            }
            /*****************************************/
            $netd = [];
            foreach($netw as $n1=>$n2)
                {
                    foreach($n2 as $nm2=>$tot)
                        {
                            $dd = [];
                            $dd['from'] = $n1;
                            $dd['to'] = $nm2;
                            $dd['width'] = $tot;
                            array_push($netd,$dd);
                        }
                }

            /******************** Coauthors */
            foreach ($auth as $ida => $linenm) {
                $linenm = (array)$linenm;
                $nomea = trim($linenm['name']);
                $IDa = $linenm['ID'];
                $coathID[$nomea] = $IDa;
                if (isset($coauthors[$nomea])) {
                    $coauthors[$nomea] = $coauthors[$nomea] + 1;
                } else {
                    $coauthors[$nomea] = 1;
                }
            }
        }

        $dr['chart_years'] = [];
        $dr['chart_years']['labels'] = $prod_label;
        $dr['chart_years']['data'] = $ds;


        /********** Ordena coauthors */
        ksort($coauthors);
        foreach ($coauthors as $nome => $total) {
            $a = [];
            $nome = trim($nome);
            $a['nome'] = $nome;
            $a['ID'] = $coathID[$nome];
            $a['colaborations'] = $total;
            array_push($coath, $a);
        }
        $dr['works'] = $works;
        $dr['coauthors'] = $coath;

        /************ Grafico Coautorias */
        arsort($coauthors);
        $limit = 10;
        $max = 0;
        $ini = 0;
        $last = 0;
        $graph = [];
        $outros = 0;
        $graph['labels'] = [];
        $graph['total'] = [];
        foreach ($coauthors as $name => $total) {
            if ($name != $dr['name']) {
                $ini++;
                if (($ini < $limit) or ($total == $last)) {
                    array_push($graph['labels'], $name);
                    array_push($graph['total'], $total);
                } else {
                    $outros = $outros + $total;
                }
                $last = $total;

                if ($total > $max) {
                    $max = $total;
                }
            }
        }

        /************ Network - Node */
        foreach($neta as $name=>$tot)
            {
                $color = '#000088';
                if ($tot > 10)
                    {
                        $color = '#0000FF';
                    }
                if ($name == $dr['name_abnt'])
                    {
                        $color = '#FF0000';
                    }
                //$dn = ['id'=>$name, 'color'=>'#0000ff', 'marker'=>['radius'=>$tot]];
                $tot = round(log($tot))*4+1;

                $dn = ['id' => $name, 'color' => $color, 'marker' => ['radius' => $tot]];
                array_push($node,$dn);
            }

        if ($outros > 0) {
            array_push($graph['labels'], 'Outros');
            array_push($graph['total'], $outros);
        }
        $dr['chart_coauthors'] = $graph;

        $dr['network']['data'] = ($netd);
        $dr['network']['nodes'] = ($node);

        /************************************** Cloud */
        $wtag = [];
        arsort($tag);
        foreach ($tag as $kw => $total) {
            $tg['text'] = $kw;
            $tg['value'] = $total * 10;
            array_push($wtag, $tg);
        }
        $dr['dataTAG'] = $wtag;

        /************************************* Publisher */
        arsort($journal);
        $jour = [];
        $jour['labels'] = [];
        $jour['data'] = [];
        foreach ($journal as $nameJ => $total) {
            $Aj = [];
            if ($nameJ != '') {
                array_push($jour['labels'], $nameJ);
                array_push($jour['data'], $total);
            }
        }
        $dr['dataJOUR'] = $jour;

        /****************************** Remissivas */
        $dr['variants'] = $this->remissivas($dr['ID']);

        return $dr;
    }

    function remissivas($ID)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->select('n_name as name')
            ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term ')
            ->where('cc_use', $ID)
            ->groupBy('n_name')
            ->orderBy('n_name')
            ->findAll();
        return $dt;
    }

    function metadataSubject($dt)
    {
        $limit = 2000;
        $ABNT = new \App\Models\Metadata\Abnt();
        $dataset = new \App\Models\ElasticSearch\Search();
        $dr = [];
        $dr['publisher'] = $dt['concept']['n_name'];
        $dr['ID'] = $dt['concept']['id_cc'];
        $dr['data'] = $dt['data'];

        $n = 0;
        $dataset->select('*');
        foreach ($dt['data'] as $ida => $linea) {
            $type = $linea['Class'];
            $ok = 0;
            switch ($type) {
                case 'Proceeding':
                    $ok = 1;
                    break;
                case 'Article':
                    $ok = 1;
                    break;
                case 'Book':
                    $ok = 1;
                    break;
                case 'BookChapter':
                    $ok = 1;
                    break;
            }
            if ($ok == 1) {
                if ($n == 0) {
                    $dataset->where('ID', $linea['ID']);
                } else {
                    $dataset->orwhere('ID', $linea['ID']);
                }
                $n++;
            }
        }

        /***************************** Works */
        $works = [];
        if ($n > 0) {
            $dx = $dataset->findAll($limit);
            foreach ($dx as $id => $line) {
                $JSON = (array)json_decode($line['json']);
                $ref = $ABNT->short($JSON, False);
                array_push($works, $ref);
            }

            if ($n >= $limit) {
                array_push($works, "Limitado em $limit registros");
            }
        }
        $dr['works'] = $works;
        return $dr;
    }

    function metadataGeral($dt)
    {
        $dr = [];
        $dr['publisher'] = $dt['concept']['n_name'];
        $dr['ID'] = $dt['concept']['id_cc'];
        $dr['data'] = $dt['data'];
        return $dr;
    }

    function metadataSource($dt)
    {
        $dr = [];
        $dr['publisher'] = $dt['concept']['n_name'];
        $dr['ID'] = $dt['concept']['id_cc'];
        $issue = [];
        $works = [];
        $collection = [];

        $data = $dt['data'];

        foreach ($data as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];

            /*******************/
            switch ($prop) {
                case 'hasCollection':
                    array_push($collection, $line['ID']);
                    break;

                case 'hasEmail':
                    $dr['Email'] = $line['Caption'];
                    break;

                case 'hasIssueOf':
                    array_push($issue, $line['ID']);
                    break;
            }
        }

        $dr['collection'] = $collection;

        $Issues = new \App\Models\Base\Issues();
        $ISSUE = [];
        $YEARS = [];

        $Source = new \App\Models\Base\Sources();
        $dt = $Source->where('jnl_frbr', $dr['ID'])->first();
        $IDjnl = $dt['id_jnl'];
        $dr = array_merge($dr, $dt);

        /******************** ISSUE */
        $dti = $Issues->where('is_source',$IDjnl)
            ->orderBy('is_vol desc, is_nr desc')
            ->findAll();

        foreach ($dti as $idi=>$linei) {
            $dti = $Issues->getMetada(0,$linei);
            $ANO = $dti['YEAR'];
            if (!isset($YEARS[$ANO])) {
                $YEARS[$ANO] = $ANO;
            }
            array_push($ISSUE, $dti);
        }
        krsort($YEARS);
        $sYEARS = [];
        foreach ($YEARS as $year) {
            array_push($sYEARS, $year);
        }

        $dr['issue'] = $ISSUE;
        $dr['issue_years'] = $sYEARS;

        $dr = array_merge($dr, $dt);

        $Cover = new \App\Models\Base\Cover();
        $dr['cover'] = $Cover->cover($dt['id_jnl']);

        return $dr;
    }

    function metadataIssue($dt, $simple = false)
    {
        $ABNT = new \App\Models\Metadata\Abnt();
        $Issues = new \App\Models\Base\Issues();
        $IssuesWorks = new \App\Models\Base\IssuesWorks();
        $IDissue = $dt['concept']['id_cc'];
        $di = $Issues
            ->Join('brapci.source_source', 'id_jnl = is_source')
            ->where('is_source_issue', $IDissue)->first();
        $dr = [];
        if ($di != []) {
            $dr['Class'] = 'Issue';
            $dr['publisher'] = $di['jnl_name'];
            $dr['jnl_rdf'] = $di['is_source'];
            $dr['is_year'] = $di['is_year'];
            $dr['is_nr'] = $di['is_nr'];
            $dr['is_vol_roman'] = $di['is_vol_roman'];
            $dr['is_vol'] = $di['is_vol'];

            $wk = $IssuesWorks->issueWorks($IDissue);
            $works = [];
            foreach ($wk as $id => $line) {
                $JSON = (array)json_decode($line['json']);
                $ref = $ABNT->short($JSON, False);
                array_push($works, $ref);
            }
            $dr['works'] = $works;
        }
        return $dr;
    }

    function metadataWork($dt, $simple = false)
    {
        $LEGEND = new \App\Models\Metadata\Legend();
        $RDF = new \App\Models\RDF2\RDF();
        $Issues = new \App\Models\Base\Issues();
        $ID = $dt['concept']['id_cc'];
        $da = $dt['data'];
        /************ DD*/
        $dd = [];
        $meta = [];

        foreach ($da as $id => $line) {
            $lang = $line['Lang'];
            $prop = $line['Property'];
            if (!isset($dd[$prop][$lang])) {
                $dd[$prop][$lang] = [];
            }
            $dc = [];
            $dc[$line['Caption']] = $line['ID'];
            array_push($dd[$prop][$lang], $dc);
        }

        $dr['ID'] = $ID;
        $dr['Class'] = $dt['concept']['c_class'];
        $dr['title'] = troca((string)$this->simpleExtract($dd, 'hasTitle'), "\n", '');
        $dr['title'] = troca($dr['title'], "\r", '');
        $dr['creator_author'] = [];
        $dr['Authors'] = '';
        if (isset($dd['hasOrganizator'])) {
            $dr['creator_author'] = $this->arrayExtract($dd, 'hasOrganizator', '(org)');
        } else {
            $dr['creator_author'] = $this->arrayExtract($dd, 'hasAuthor');
        }
        $dr['description'] = troca((string)$this->simpleExtract($dd, 'hasAbstract'), "\n", '');
        $dr['description'] = troca($dr['description'], "\r", '');
        $dr['subject'] = $this->arrayExtract($dd, 'hasSubject');

        $year = $this->simpleExtract($dd, 'wasPublicationInDate');
        if ($year != null) {
            $dr['year'] = $year;
        }

        /***************************** ISSUE */
        $ISSUE1 = $this->arrayExtract($dd, 'hasIssueOf');
        $ISSUE = $ISSUE1;

        if (isset($ISSUE[0])) {
            $dtIssue = $RDF->le($ISSUE[0]['ID']);
            $simpleIssue = true;
            $dtIssue = $this->metadataIssue($dtIssue, $simpleIssue);
            $dr['Issue'] = $dtIssue;
            $dr['year'] = $dtIssue['is_year'];
            if (isset($dtIssue['Publication'])) {
                $dr['publisher'] = $dtIssue['Publication'];
            } else {
                $dr['publisher'] = ':: Not informed Yet ::';
            }
            $dr['legend'] = $LEGEND->show($dtIssue);
        }

        /**************************************** Publisher */
        $editora = $this->arrayExtract($dd, 'isPublisher');
        $place = $this->arrayExtract($dd, 'isPlaceOfPublication');
        $publisher = '';
        for ($r = 0; $r < count($editora); $r++) {
            $ln1 = $editora[$r];
            if (isset($place[$r])) {
                if ($publisher != '') {
                    $publisher .= '; ';
                }
                $ln2 = $place[$r];
                $publisher .= $ln2['name'] . ': ' . $ln1['name'];
            } else {
                if ($publisher != '') {
                    $publisher .= '; ';
                }
                $publisher .= $ln1['name'] . ': [s.n.]';
            }
        }
        $dr['publisher'] = $publisher;

        /************************** Resource */
        $dr['resource_pdf'] = PATH . '/download/' . $ID;

        /************************************************************* COVER */
        $RDFimage = new \App\Models\RDF2\RDFimage();
        $dr['cover'] = $this->simpleExtract($dd, 'hasCover');


        /*************************************** SOURCE TYPES ****************/
        $Class = $dt['concept']['c_class'];

        /*************************************** SOURCE BOOK CHAPTER *********/
        if ($Class == 'BookChapter') {
            /* Recupera Livros */
            $book = $this->arrayExtract($dd, 'hasBookChapter');
            if (isset($book[0])) {
                $bk  = [];
                $bookID = $book[0]['ID'];
                $book = $RDF->le($bookID);

                $dr['cover'] = $this->ExtractFromData($book, 'hasCover', 'text');
                $bk['cover'] = $dr['cover'];
                $bk['Publisher'] = $this->ExtractFromData($book, 'isPublisher', 'text');
                $bk['Title'] = $this->ExtractFromData($book, 'hasTitle', 'text');
                $dr['book'] = $bk;
                $dr['resource_pdf'] = PATH . '/download/' . $bookID;
            }
        }

        /*************************************** SOURCE JOURNAL / PROCEEDING */
        if ($publisher == '') {
            $Source = new \App\Models\Base\Sources();
            $dj = $this->arrayExtract($dd, 'isPartOfSource');
            if (isset($dj[0])) {
                $dj = $Source->where('jnl_frbr', $dj[0]['ID'])->first();
                $dr['publisher'] = $this->simpleExtract($dd, 'isPartOfSource');
                $Cover = new \App\Models\Base\Cover();
                $dr['cover'] = $Cover->cover($dj['id_jnl']);
            }
        }

        /******************* ISBN */
        $ISBN = new \App\Models\ISBN\Index();
        $isbn = $this->arrayExtract($dd, 'hasISBN');
        $dr['isbn'] = '';
        foreach ($isbn as $value) {
            $visbn = $value['name'];
            $visbn = $ISBN->format($visbn);
            if ($dr['isbn'] != '') {
                $dr['isbn'] .= ' | ';
            }
            $dr['isbn'] .= $visbn;
        }


        /************************** Pages */
        $hasPage = $this->simpleExtract($dd, 'hasPage');
        if ($hasPage != '') {
            $dr['pages'] = $hasPage;
        }

        /*********************** Section */
        switch ($dr['Class']) {
            case 'Book':
                $dr['section'][0] = ['name' => 'Book - Livro'];
                break;
            default:
                $dr['section'] = $this->arrayExtract($dd, 'hasSectionOf');
                if ($dr['section'] == []) {
                    $dr['section'][0] = ['name' => 'No Section'];
                }
                break;
        }

        /********** Não exist Subject */
        if (!isset($dd['hasSubject'])) {
            $dd['hasSubject']['pt'] = [];
        }

        if (!isset($dd['hasAbstract'])) {
            $dd['hasAbstract']['pt'] = [];
        }

        if ($simple == false) {
            $dr['data'] = $dd;
        }

        $Cited = new \App\Models\Cited\Index();
        $dr['cites'] = $Cited->show_ref($ID);

        $dr['meta'] = $this->metadataHeader($dr);

        return $dr;
    }

    function metadataHeader($m)
        {
            $RSP = [];


        foreach($m as $key=>$value)
            {

                switch($key)
                    {
                        /*************** DC.Creator.PersonalName */
                        case 'creator_author':
                            foreach($value as $ida=>$linea)
                                {
                                    $dd = [];
                                    $dd['name'] = 'DC.Creator.PersonalName';
                                    $dd['content'] = $linea['name'];
                                    Array_push($RSP,$dd);
                                    $dd['name'] = 'citation_author';
                                    $dd['content'] = $linea['name'];
                                    Array_push($RSP, $dd);

                                }
                            break;

                         /**************** DC.Subject */
                        case 'subject':
                            foreach ($value as $ida => $linea) {
                                $dd = [];
                                $dd['name'] = 'DC.Subject';
                                $dd['content'] = $linea['name'];
                                Array_push($RSP, $dd);
                            }
                            break;

                        /**************** DC.Title */
                        case 'title':
                              $dd = [];
                              $dd['name'] = 'DC.Title';
                              $dd['content'] = $value;
                              Array_push($RSP, $dd);
                            break;

                    /**************** DC.Subject */
                    case 'subject':
                        foreach ($value as $ida => $linea) {
                            $dd = [];
                            $dd['name'] = 'DC.Subject';
                            $dd['content'] = $linea['name'];
                            Array_push($RSP, $dd);
                        }
                        break;

                    }
            }
            return $RSP;
        }

    function arrayExtract($dt, $class, $suf = '')
    {
        $RSP = [];
        if (isset($dt[$class])) {
            $data = $dt[$class];
            foreach ($data as $lg) {
                foreach ($lg as $ida => $line) {
                    $name = [];
                    $name['name'] = trim(key($line));
                    $name['ID'] = $line[key($line)];
                    if ($suf != '') {
                        $name['complement'] = $suf;
                    }
                    array_push($RSP, $name);
                }
            }
        }
        return $RSP;
    }

    function ExtractFromData($dt, $class, $type = 'T')
    {
        $type = UpperCase(substr($type, 0, 1));
        if (isset($dt['data'])) {
            $dt = $dt['data'];
        }

        $tx = '';
        $ts = '';
        $ta = [];
        foreach ($dt as $id => $line) {
            $prop = trim($line['Property']);
            if ($prop == $class) {
                if ($tx != '') {
                    $tx .= '; ';
                }
                $tx .= $line['Caption'];
                if ($ts == '') {
                    $ts = $line['Caption'];
                }
                array_push($ta, ['ID' => $line['ID'], 'name' => $line['Caption']]);
            }
        }
        switch ($type) {
            case 'T':
                return $tx;
                break;
            case 'S':
                return $ts;
                break;
            default:
                return $ta;
                break;
        }
    }

    function simpleExtract($dt, $class)
    {
        $lang = $this->langPref();
        if (isset($dt[$class])) {
            foreach ($dt as $nn => $line) {
                if ($nn == $class) {
                    foreach ($lang as $lg) {
                        if (isset($line[$lg])) {
                            $rsp = key($line[$lg][0]);
                            return ($rsp);
                        }
                    }
                }
            }
        }
    }
}
