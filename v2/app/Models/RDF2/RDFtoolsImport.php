<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDFtoolsImport extends Model
{
    protected $DBGroup          = 'rdf2';
    protected $table            = 'rdt_temp_import';
    protected $primaryKey       = 'id_ti';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ti_ID', 'ti_update'
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

    function zeraDB()
    {
        $sql = "TRUNCATE `rdf_class_domain`";
        $this->db->query($sql);
    }

    function reimport($id=0)
        {

            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            $dt = $RDFconcept->select('id_cc')->where('cc_class',$id)->FindAll();
            $ids = [];
            $n = 0;
            foreach($dt as $line)
                {
                    if ($n==0)
                        {
                            $this->where('ti_ID',$line['id_cc']);
                        } else {
                            $this->Orwhere('ti_ID', $line['id_cc']);
                        }
                    if ($n > 100)
                        {
                            $n = 0;
                            $this->delete();
                        }
                }
            if ($n > 0) {
                $this->delete();
            }
        }

    function import($file)
    {
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
        $RDFproperty = new \App\Models\RDF2\RDFproperty();

        if (file_exists($file)) {
            $this->zeraDB();

            $xml = simplexml_load_file($file);
            /********************** Prefix - NameSpace */
            $namespaces = $xml->getNamespaces(true);
            $this->getNameSpace($namespaces);

            /********************* Content - READ */
            $txt = file_get_contents($file);
            $txt = troca($txt, 'owl:', 'owl_');
            $txt = troca($txt, 'rdf:', 'rdf_');
            $txt = troca($txt, 'rdfs:', 'rdfs_');
            $txt = troca($txt, 'xml:', 'xml_');
            $txt = troca($txt, 'xmlns:', 'xmlns_');
            $xml = simplexml_load_string($txt);

            $url = [];

            /************ Classes **************************************/
            foreach ($xml as $prop => $vlr) {
                $att = [];
                foreach ($vlr->attributes() as $a => $b) {
                    $att[$a] = (string)$b;
                }

                /*************** Class */
                $label = trim($vlr->rdfs_label);
                if (($label != '') and ($prop == 'owl_Class')) {
                    $prefix = 'brapci';
                    $ClassId = $RDFclass->register($prefix, $label);
                    $url[$att['rdf_about']] = $ClassId;
                }
            }

            /************ Proprierty ***********************************/
            $Property = [];
            foreach ($xml as $prop => $vlr) {
                /*************** Prop */
                $label = trim($vlr->rdfs_label);
                if (($label != '') and ($prop == 'owl_ObjectProperty')) {
                    $prefix = 'brapci';
                    $idP = $RDFproperty->register($prefix, $label);
                    $Property[$label] = $idP;
                }
            }

            /************ Domain and Range  ****************************/
            foreach ($xml as $prop => $vlr) {
                $ids = 0;
                $label = trim($vlr->rdfs_label);

                if ($prop == 'owl_ObjectProperty') {

                    /* Recupera URL do Label */
                    $type = trim((string)$vlr->rdfs_label);

                    /*************** Domain */
                    foreach ($vlr->rdfs_domain as $data => $p2) {

                        foreach ($p2->attributes() as $a => $b) {
                            $b = (string)$b;

                            $idc = $url[$b];
                            $idp = $Property[$label];

                            foreach ($vlr->rdfs_range as $data => $r2) {
                                foreach ($r2->attributes() as $a => $b) {
                                    $b = (string)$b;
                                    $idr = $url[$b];
                                    $RDFclassDomain->register($idc, $idp, $idr);
                                }
                            }
                    }
                }
                }
            }
            echo "FIM da IMPORTAÇÂO";
            exit;
        }
    }

    function getNameSpace($nameSpace)
    {
        $RDFprefix = new \App\Models\RDF2\RDFprefix();
        foreach ($nameSpace as $prefix => $uri) {
            $RDFprefix->register($prefix, $uri);
        }
    }

    /*************************************************** ALL */
    function importRDFOld()
    {
        $RDFtools = new \App\Models\RDF2\RDFtoolsImport();
        $dt = $this
            ->join("rdf_concept","id_cc = ti_ID","right")
            ->where("ti_ID is null")
            ->findAll(250);

        foreach($dt as $id=>$line)
            {
                $ID = $line['id_cc'];
                $RSP = $RDFtools->importRDF($ID, true);

                $d['ti_ID'] = $ID;
                $this->set($d)->insert();

                echo $ID.'; ';
            }
        echo "FIM\n";
        if (count($dt) > 1) {
            echo metarefresh('', 1);
        }
    }
    /*************************************************** ALL */
    function importRDFAll()
    {
        echo "GetAll<hr>";
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->join('brapci.rdf_concept as b_rdf', 'rdf_concept.id_cc = b_rdf.id_cc', 'right')
            ->where('rdf_concept.id_cc is null')
            //->where('rdf_concept.cc_class > 0 ')
            ->findAll(500);

        foreach ($dt as $id => $line) {
            $RSP = $this->importRDF($line['id_cc']);
            echo $line['id_cc'] . ' ' . $line['cc_class'] . '<br>';
            if ($RSP['status'] != '200') {
                echo "ERRO";
                pre($RSP);
            }
        }
        echo "FIM\n";
        if (count($dt) > 10)
            {
                echo metarefresh('', 1);
            }
    }

    function classConvert($class)
    {
        $c = [];
        $c['Journal'] = 'Journals';
        $c['ArticleSection'] = 'Section';
        $c['isPubishIn'] = 'isPubishOf';
        $c['ProceedingSection'] = 'Section';
        $c['Pages'] = 'Page';
        $c['Volume'] = 'PublicationVolume';
        $c['Author'] = 'Person';
        $c['Agent'] = 'Person';
        $c['fullText'] = '';
        if (isset($c[$class])) {
            $class = $c[$class];
        }
        return $class;
    }

    function propConvert($class)
    {
        $c = [];
        $c['dateOfPublication'] = 'wasPublicationInDate';
        $c['hasSummary'] = '';
        $c['hasVolume'] = '';
        $c['fullText'] = '';
        $c['hasIssue'] = 'hasIssueOf';
        $c['isPubishIn'] = 'isPartOfSource';
        $c['hasIdRegister'] = 'hasID';
        $c['hasIssue'] = 'hasIssueOf';
        $c['hasTitleAlternative'] = 'hasTitle';
        $c['hasPublicationVolume'] = 'hasVolume';

        if (isset($c[$class])) {
            $class = $c[$class];
        }
        return $class;
    }

    /*************************************************** */
    function importRDF($id, $force = false)
    {
        $RSP = [];
        $RDF1 = new \App\Models\Rdf\RDF();
        $Volume = new \App\Models\AI\NLP\Text\Volume();
        $NumberVolume = new \App\Models\AI\NLP\Text\NumberVolume();

        $dt1 = $RDF1->le($id);

        if ($dt1 != []) {
            $class = $this->classConvert($dt1['concept']['c_class']);
            $dt1['concept']['c_class'] = $class;

            $RDF2 = new \App\Models\RDF2\RDF();
            $RDFconcept = new \App\Models\RDF2\RDFconcept();

            switch ($class) {
                case '':
                    //$RSP = $this->importGeneric($dt1);
                    $RSP['status'] = '200';
                    break;

                case 'Article':
                    $RSP = $this->importArticle($dt1);
                    break;
                case 'Book':
                    $RSP = $this->importBook($dt1);
                    break;
                case 'BookChapter':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CDU':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CDD':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ClassificationAncib':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Collection':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ContentType':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'CorporateBody':
                    $RSP = $this->importCorporateBody($dt1);
                    break;
                case 'Country':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Date':
                    $RSP = $this->importDate($dt1);
                    break;
                case 'DOI':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ExclusiveDisjunction':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'File':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Gender':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Image':
                    $RSP = $this->importImage($dt1);
                    break;
                case 'ISBN':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'ISSN':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Issue':
                    $RSP = $this->importIssue($dt1);
                    break;
                case 'CnpqPQ':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'License':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Linguage':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Number':
                    $RSP = $this->importNumber($dt1);
                    break;
                case 'Page':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Person':
                    $RSP = $this->importPerson($dt1);
                    break;
                case 'Place':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'PublicationVolume':
                    $dt1['concept']['n_name'] = $Volume->normalize($dt1['concept']['n_name']);
                    $dt1['concept']['n_lang'] = 'nn';
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'PublicationNumber':
                    $dt1['concept']['n_name'] = $NumberVolume->normalize($dt1['concept']['n_name']);
                    $dt1['concept']['n_lang'] = 'nn';
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Publisher':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Journals':
                    $RSP = $this->importJournals($dt1);
                    break;
                case 'Proceeding':
                    $RSP = $this->importProceeding($dt1);
                    break;
                case 'RORID':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'SerieName':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Section':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'Subject':
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'FileStorage':
                    /* TO CHECK */
                    $RSP = $this->importGeneric($dt1);
                    break;
                case 'FileType':
                    /* TO CHECK */
                    $RSP = $this->importGeneric($dt1);
                    break;

                default:
                    $RSP['status'] = '510';
                    $RSP['message'] = $class . ' don´t have method';
                    if (isset($dt1['concept']['id_cc'])) {
                        $RSP['ID'] = $dt1['concept']['id_cc'];
                    } else {
                        $RSP['ID'] = 'Invalid ID';
                    }
            }
        }

        $RSP['time'] = date("Y-m-d H:i:s");
        return $RSP;
    }

    function createConcept($dt1)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFclass = new \App\Models\RDF2\RDFclass();

        $d['ID'] = $dt1['concept']['id_cc'];
        $d['Class'] = $dt1['concept']['c_class'];
        $d['Name'] = $dt1['concept']['n_name'];
        $d['Lang'] = $dt1['concept']['n_lang'];
        $IDC = $RDFconcept->createConcept($d);
        if ($IDC < 0) {
            $RSP['status'] = '500';
            switch ($IDC) {
                case -1:
                    $RSP['message'] = 'Classe ' . $d['Class'] . ' não exite';
                    break;
                default:
                    $RSP['message'] = 'Erro não informado';
            }
        } else {
            $RSP['status'] = '200';
        }
        $RSP['Term'] = $dt1['concept']['n_name'] . '@' . $dt1['concept']['n_lang'];
        $RSP['ID'] = $IDC;
        $RSP['Class'] = $dt1['concept']['c_class'];
        return $RSP;
    }
    /************************************************ DATA */
    function importData($dt, $ID, $force = false)
    {
        $RDF1 = new \App\Models\Rdf\RDF();
        $RDF2 = new \App\Models\RDF2\RDF();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFrules = new \App\Models\RDF2\RDFrules();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFliteral = new \App\Models\RDF2\RDFliteral();

        /**************************** DATAS */
        if (isset($dt['data'])) {
            $dados = $dt['data'];

            $validator = $RDFrules->validator($dt);

            if ($validator == true) {
                foreach ($dados as $id => $line) {
                    /************************* Propriedade */
                    $prop = trim($line['c_class']);
                    if ($prop != '') {
                        $prop = $this->propConvert($prop);
                        if ($prop != '') {
                            $id_prop = $RDFclass->getClass($prop);
                            if ($id_prop == 0) {
                                pre($line, false);
                                echo "OPS - Propriedade não existe '$prop'\n";
                            }
                            /********************** Dados das propriedades */
                            $lit = 0;
                            $ID2 = $line['d_r2'];
                            if ($ID2 == $ID) {
                                $ID2 = $line['d_r1'];
                            }

                            if ($line['d_literal'] == 0) {
                                //echo "Registrar";
                                $RDFdata->register($ID, $id_prop, $ID2, $lit);
                            } else {
                                /*********************** Literal */
                                //echo "Registrar Literal";
                                $name = $line['n_name'];
                                $lang = $line['n_lang'];
                                $ID2 = 0;
                                $lit = $RDFliteral->register($name, $lang);
                                $RDFdata->register($ID, $id_prop, $ID2, $lit);
                            }
                        }
                    } else {
                        $id_prop = 0;
                    }
                }
            } else {
                echo "Erro de Validação";
            }
        }
    }

    /********************************************* FIM DATA */

    function importDate($dt1)
    {
        /********** TO DO */
        $RSP['status'] = 200;
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importImage($dt1)
    {
        /********** TO DO */
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }


    /*********************************************************** ISSUE */
    function importIssue($dt1)
    {
        $RDF = new \App\Models\RDF2\RDF();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $Issue = new \App\Models\Base\Issues();
        $IssueWorks = new \App\Models\Base\IssuesWorks();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        /********** TO DO */
        $ID = $dt1['concept']['id_cc'];
        $RSP = $this->createConcept($dt1);

        /****************************** */
        $J = $Issue
            ->join('source_source', 'is_source = id_jnl')
            ->where('is_source_issue', $ID)->first();

        if ($J == null) {
            echo "X NAO EXISTE ISSUE REGISTRADO XXXXXXXXXX==$ID<br>";
            $dt = $this->metadataIssue($dt1);
            pre($dt);
            echo "ISSUE not registred $ID";
            //$Issue
            pre($dt1);
            exit;
        }
        $JNL = $J['is_source'];
        $ID2 = $J['jnl_frbr'];
        $lit = 0;

        if ($ID2 > 0)
            {
                $prop_journal = $RDFclass->getClass('hasPartOfPublication');
                $RDFdata->register($ID2, $prop_journal, $ID, $lit);
            }

        $prop_issue = $RDFclass->getClass('hasIssueOf');
        foreach ($dt1['data'] as $id => $line) {
            $class = $line['c_class'];

            $ID2 = $line['d_r1'];
            if ($ID2 == $ID) {
                $ID2 = $line['d_r2'];
            }

            switch ($class) {
                default:
                    echo h('Sem proriedade: '.$class,1);
                    break;
                case 'altLabel':
                    /* NOOP */
                    break;
                case 'prefLabel':
                    /* NOOP */
                    break;
                case 'dateOfPublication':
                    $prop = 'dateOfPublication';
                    $propJ = $RDFclass->getClass($prop);
                    $lit = 0;
                    $RDFdata->register($ID, $propJ, $ID2, $lit);
                    break;
                case 'isPubishIn':
                    $prop = 'isPubishIn';
                    $propJ = $RDFclass->getClass($prop);
                    $lit = 0;
                    echo "===";
                    $RDFdata->register($ID, $propJ, $ID2, $lit);
                    break;
                case 'hasPublicationNumber':
                    $prop = 'hasVolumeNumber';
                    $propJ = $RDFclass->getClass($prop);
                    $lit = 0;
                    $RDFdata->register($ID, $propJ, $ID2, $lit);
                    break;
                case 'hasPublicationVolume':
                    $prop = 'hasVolume';
                    $propJ = $RDFclass->getClass($prop);
                    $lit = 0;
                    $RDFdata->register($ID, $propJ, $ID2, $lit);
                    break;
                case  'hasIssue':
                    /* hasIssueOf */
                    $dt = $RDFconcept->le($ID2);
                    if ($dt == null)
                        {
                            echo  "ERRO ISSUE $ID";
                            return [];
                        }
                    $concept = $dt['c_class'];

                    switch ($concept) {
                        case 'Journals':
                            $propJ = $RDFclass->getClass('hasPartOfPublication');
                            $lit = 0;
                            $RDFdata->register($ID2, $propJ, $ID, $lit);
                            break;
                        case 'Proceeding':
                            $lit = 0;
                            $RDFdata->register($ID, $prop_issue, $ID2, $lit);
                            $IssueWorks->register($JNL, $ID, $ID2);
                            break;
                        case 'Issue':
                            echo h('==='.$concept, 4);
                            pre($dt, false);
                            break;
                        case 'Article':
                            $prop = 'hasIssueOf';
                            $propJ = $RDFclass->getClass($prop);
                            $lit = 0;
                            $RDFdata->register($ID, $propJ, $ID2, $lit);
                            break;
                        default:
                            echo '<br>=CONCEPT==>' . $concept;
                            break;
                    }
                    //pre($dt);
                    //$lit = 0;
                    //echo $ID . '==' . $prop_issue.'=='.$ID2.'<br>';
            }
        }
        //$RSP['data'] = $this->importData($dt1, $RSP['ID']);
        /* ISSUE */
        return $RSP;
    }

    function metadataIssue($dt)
    {
        $RDF2 = new \App\Models\RDF2\RDF();
        $RDF2data = new \App\Models\RDF2\RDFdata();
        $RDFclass = new \App\Models\RDF2\RDFclass();
        if ($dt['concept']['c_class'] = 'Issue') {
            $data = $dt['data'];
            $ID = $dt['concept']['id_cc'];
            foreach ($data as $id => $line) {
                $class = $line['c_class'];
                $d1 = $line['d_r1'];
                $d2 = $line['d_r2'];
                $p = $line['d_p'];
                $l = $line['d_literal'];
                $id_cncpt = $d1;
                if ($id_cncpt == $ID) {
                    $id_cncpt = $d2;
                }
                switch ($class) {
                    case 'hasIssue':
                        $Class2 = $RDF2->getClassType($id_cncpt);
                        switch ($Class2) {
                            case 'Proceeding':
                                $id_prop = $RDFclass->getClass('hasIssueOf');
                                $lit = 0;
                                $RDF2data->register($ID, $id_prop, $id_cncpt, $lit);
                                break;
                            default:
                                echo '<br>' . $Class2;
                                break;
                        }
                        break;
                    default:
                        echo "OK $class - ($id_cncpt) -- $d2 - $d1<br>";
                        break;
                }
            }
            exit;
        } else {
            echo "Erro de Classe";
            pre($dt);
        }
    }

    function importPerson($dt1)
    {
        $dt1['concept']['n_name'] = nbr_author($dt1['concept']['n_name'], 7);
        $dt1['concept']['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importCorporateBody($dt1)
    {
        $dt1['concept']['n_name'] = nbr_author($dt1['concept']['n_name'], 7);
        $dt1['concept']['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importJournals($dt1)
    {
        $dt['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importNumber($dt1)
    {
        $dt['n_lang'] = 'nn';
        $RSP = $this->createConcept($dt1);
        return $RSP;
    }

    function importGeneric($dt1)
    {
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importProceeding($dt1)
    {
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importBook($dt1)
    {
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }

    function importArticle($dt1)
    {
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RSP = $this->createConcept($dt1);
        $RSP['data'] = $this->importData($dt1, $RSP['ID']);
        return $RSP;
    }
}
