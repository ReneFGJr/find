<?php

namespace App\Models\RDF2;

use CodeIgniter\Model;

class RDF extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'rdfs';
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

    function index($d1, $d2, $d3, $d4, $cab)
    {
        $sx = '';
        $RSP = [];

        switch ($d1) {
            case 'a':
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $cab;
                $sx .= $RDFform->editRDF($d2);
                return bs(bsc($sx, 12));
                break;
            case 'resume':
                $RDFdata = new \App\Models\RDF2\RDFdata();
                $sx .= $RDFdata->resume();
                return $sx;
                break;
            case 'withoutClass':
                $RDFdata = new \App\Models\RDF2\RDFdata();
                $sx .= $RDFdata->withoutClass($d2);
                return $sx;
                break;
            case 'rules':
                $RDFclassDomain = new \App\Models\RDF2\RDFclassDomain();
                $sx .= $RDFclassDomain->rules($d2);
                return $sx;
                break;
            case 'v':
                $sx = $this->view($d2);
                return $sx;
                break;
            case 'popup':
                $data['page_title'] = 'Brapci - POPUP';
                $data['bg'] = 'bg-pq';
                $sx .= $this->popup($d2, $d3, $d4);
                return $sx;
                break;
            case 'form':
                $data['page_title'] = 'Brapci - POPUP';
                $data['bg'] = 'bg-pq';
                $sx = '';
                $sx .= view('Brapci/Headers/header', $data);
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $RDFform->index($d2, $d3, $d4);
                $sx .= view('Brapci/Headers/footer', $data);
                return $sx;
                break;
            case 'import':
                return $RSP = $this->import();
                break;
            case 'source':
                break;
            case '404':
                $RSP = $this->default();
                break;
            case 'Class':
                $sx = '';
                $RDFclass = new \App\Models\RDF2\RDFclass();
                $RDFconcept = new \App\Models\RDF2\RDFconcept();
                $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();

                if ($d2 == '') {
                    $Class = $RDFclass->getClasses();
                    $sx = '<div style="column-count: 3;">';
                    $sx .= '<ul>';
                    foreach ($Class as $id => $line) {
                        $link = '<a href="' . PATH . '/rdf/Class/' . $line['Class'] . '">';
                        $linka = '</a>';
                        $sx .= '<li>' . $link . $line['Class'] . $linka . '</li>' . cr();
                    }
                    $sx .= '</ul>';
                    $sx .= '</div>';
                } else {
                    $dt =  $RDFclass->get($d2);
                    $sx .= h("Class", 6);
                    $sx .= h($dt['Class']);
                    $sx .= h($dt['prefix'], 5);

                    /****** Total registros */
                    $dtt = $RDFconcept->select('count(*) as total')
                        ->where('cc_class', $dt['id'])
                        ->groupBy('cc_class')
                        ->first();
                    if ($dtt != []) {
                        $sx .= h('Total of ' . number_format($dtt['total'], 0, ',', '.') . ' registers', 4);
                    }


                    $sx .= '<hr>';
                    $sx .= anchor(PATH . '/rdf/Class/', 'Voltar', ['class' => 'btn btn-outline-primary']);
                    $sx .= anchor(PATH . '/rdf/Class/' . $d2 . '/reimport', 'Reimporta', ['class' => 'ms-2 btn btn-outline-warning']);
                    $sx .= anchor(PATH . '/api/rdf/in/all', 'Importa', ['class' => 'ms-2 btn btn-outline-danger']);
                    if ($d3 == 'reimport') {
                        $RDFtoolsImport->reimport($dt['id']);
                    }
                }
                return bs(bsc($sx));
                break;
            default:
                return bs(bsc($this->menu(), 12));
                break;
        }
        $RSP['time'] = date("Y-m-dTH:i:s");
        echo json_encode($RSP);
        exit;
    }

    function menu()
    {
        $menu = [];
        $menu[PATH . '/rdf/Class'] = "Classes";
        $menu[PATH . '/rdf/withoutClass/-1'] = "WithOutClasses (-1)";
        $menu[PATH . '/rdf/withoutClass/0'] = "WithOutClasses (0)";
        $menu[PATH . '/rdf/withoutClass/1'] = "WithOutClasses (1)";
        $menu[PATH . '/rdf/resume'] = "Resume";
        $menu[PATH . '/rdf/rules'] = "Ontology (Rules)";
        return menu($menu);
    }

    function view($id)
    {
        $dt = $this->v($id);
        $sx = '';
        $sx .= h('Class: ' . $dt['concept']['c_class']);
        $sx .= h('prefLabel: ' . $dt['concept']['n_name'], 4);
        foreach ($dt['data'] as $id => $line) {
            $sx .= bsc($line['Class'], 3, 'small text-end');
            $name = $line['Caption'];
            if ($line['ID'] > 0) {
                $name = '<a href="' . PATH . '/v/' . $line['ID'] . '">' . $name . '</a>';
            }
            $sx .= bsc($name, 9);
        }
        return bs($sx);
    }

    function index_list($i, $l = 'A', $lang = '')
    {
        $cp = 'n_name, n_lang, id_cc';
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $idc = $RDFclass->getClass($i);
        $RDFconcept
            ->select($cp)
            ->join('brapci_rdf.rdf_literal', 'id_n = cc_pref_term')
            ->where('cc_class', $idc)
            ->where('id_cc = cc_use')
            ->where("substring(n_name,1,1) = '$l'");
        #->like('n_name',$l,'after');
        if ($lang != '') {
            $RDFconcept->where('n_lang', $lang);
        }
        $dt = $RDFconcept->orderBy('n_name')
            ->findAll(10000);
        return $dt;
    }

    function popup($d1, $d2, $d3)
    {
        $sx = '';
        $RDFliteral = new \App\Models\RDF2\RDFliteral();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $Language = new \App\Models\AI\NLP\Language();

        switch ($d1) {
            case 'literal':
                if (get("action")) {
                    $vlr = get("name");
                    $lock = get("lock");
                    $lang = get("lang");
                    if ($lock == '') {
                        $lock = '0';
                    }
                    $dd['n_name'] = $vlr;
                    $dd['n_lang'] = $lang;
                    $dd['n_lock'] = $lock;
                    $RDFliteral->set($dd)->where('id_n', $d2)->update();
                    echo wclose();
                } else {
                    $dt = $RDFliteral->find($d2);
                    $vlr = $dt['n_name'];
                    $lang = $dt['n_lang'];
                    $lock = $dt['n_lock'];
                }
                $idioma = $Language->languages();
                $params = ['name' => 'name', 'value' => $vlr, 'rows' => 5, 'class' => 'form-control full border border-secondary'];
                $RDFliteral = new \App\Models\RDF2\RDFliteral();
                $dt = $RDFliteral->find($d2);
                $sx = form_open();
                $sx .= form_textarea($params);
                $sx .= form_checkbox('lock', '1', $lock) . ' Registro travado';

                $sx .= '<div>';
                $sx .= 'Language: ' . $lang . ' ';
                $sx .= '<select name="lang">';
                foreach ($idioma as $id => $lg) {
                    $chk = '';
                    if ($lg == $lang) {
                        $chk = 'selected';
                    }
                    $sx .= '<option value="' . $lg . '" ' . $chk . '>' . $lg . '</option>';
                }
                $sx .= '</select>';
                $sx .= '</div>';

                $sx .= '<div>';
                $sx .= form_submit('action', lang('brapci.save'), ['class' => 'mt-4 btn btn-outline-primary']);
                $sx .= '</div>';
                $sx .= form_close();

                $sx = bs(bsc($sx, 12));
                break;
            case 'add':
                $sx .= '<div class="text-center">';
                $RDFform = new \App\Models\RDF2\RDFform();
                $sx .= $RDFform->add($d2, $d3);
                $sx .= '</div>';
                break;
            case 'delete':
                $sx .= '<div class="text-center">';
                ############################## DELETE
                $conf = get("confirm");
                if ($conf != '') {
                    $sx .= '<h1 class="text-center">' . lang('brapci.excluded_item') . '</h1>';
                    $sx .= '<span class="btn btn-outline-primary" onclick="wclose();">' . lang("brapci.close") . '</span>';
                    $RDFdata->where('id_d', $d2)->delete();
                    echo $RDFdata->getlastquery();
                } else {
                    $dt = $RDFdata
                        ->find($d2);
                    $sx .= '<a class="btn btn-outline-danger" href="' . PATH . '/popup/rdf/delete/' . $d2 . '?confirm=True">' . lang("brapci.exclude") . '</a>';
                }
                $sx .= '</div>';
        }
        return $sx;
    }

    /************* Default */
    function default()
    {
        $dt = [];
        $dt['status'] = '404';
        $dt['message'] = 'Action not informed';
        return $dt;
    }

    function show_class($dt)
    {
        $sx = '';
        $cnt = $dt['concept'];
        $sa = '<span style="font-size: 1.6em; font-weight: bold">' . $cnt['n_name'] . '</span>';
        $sa .= '<br>';
        $sa .= $cnt['prefix_ref'] . '.' . $cnt['c_class'];

        $sb = $cnt['n_lang'];
        $sb .= '<br><span class="small">Update: ' . $cnt['cc_update'] . '</span>';

        $sx .= bsc($sa, 10, 'border-bottom border-secondary');
        $sx .= bsc($sb, 2, 'border-bottom border-secondary text-end');
        return bs($sx);
    }

    /********************* getClassType */
    function getClassType($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->join('rdf_class', 'cc_class = id_c')
            ->where('id_cc', $id)->first();
        if ($dt != null) {
            return $dt['c_class'];
        }
        return "";
    }

    /************* V */
    function v($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();

        $data = [];
        $data['concept'] = $RDFconcept->le($id);

        $data['data'] = $RDFdata->le($id);

        return $data;
    }

    /************* V */
    function resume()
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept
            ->select('count(*) as total, c_class')
            ->join('rdf_class', 'id_c = cc_class')
            ->groupBy('c_class')
            ->orderBy('c_class')
            ->findAll();
        $d = [];
        foreach ($dt as $id => $line) {
            $class = $line['c_class'];
            $d[$class] = $line['total'];
        }
        pre($d);
    }

    function le($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $RDFdata = new \App\Models\RDF2\RDFdata();
        $d = [];
        $d['concept'] = $RDFconcept->le($id);
        $d['data'] = $RDFdata->le($id);

        /************************* Remover */
        if ($d['data'] == []) {
            $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
            $RDFtoolsImport->importRDF($id);
            $d['data'] = $RDFdata->le($id);
        }
        return $d;
    }

    function valid($id)
    {
        $RDFconcept = new \App\Models\RDF2\RDFconcept();
        $dt = $RDFconcept->find($id);
        if ($dt == null) {
            return false;
        } else {
            return true;
        }
    }

    /************* Import */
    function import()
    {
        $sx = '';
        $sx .= form_open_multipart();
        $sx .= form_upload('OWL');
        $sx .= form_submit('action', 'Send file');
        $sx .= form_close();

        if (isset($_FILES['OWL'])) {
            $RDFtoolsImport = new \App\Models\RDF2\RDFtoolsImport();
            $file = $_FILES['OWL']['tmp_name'];

            $sx .= $RDFtoolsImport->import($file);
            return $sx;
        }
        return $sx;
    }

    function recoverClass($class, $limit = 20, $offset = 0, $ord = 'N')
    {
        $ord = substr($ord, 0, 1);
        $RDFclass = new \App\Models\RDF2\RDFclass();
        $RDFconcept = new \App\Models\RDF2\RDFconcept();

        if ((sonumero($class)) != $class) {
            $class = $RDFclass->getClass($class);
        }
        if ($limit == '') {
            $limit = 3 * 100;
        }
        $dt = $RDFconcept
            ->select('id_cc')
            ->where('cc_class', $class);
        if ($ord = 'd') {
            $RDFconcept->orderBy('id_cc desc');
        }
        $dt = $RDFconcept->findAll($limit, $offset);
        return $dt;
    }

    function view_data($dt)
    {
        $RDFdata = new \App\Models\Rdf2\RDFdata();
        return $RDFdata->view_data($dt);
    }
    function extract($dt, $prop, $type = 'F')
    {
        /*
            F->first
            A->Array
            S->string (todos)
            */
        $dt = $dt['data'];
        $dr = [];
        $st = '';

        foreach ($dt as $id => $line) {
            if ($line['Property'] == $prop) {
                /******************************** FIRST */
                if ($type == 'F') {
                    return ($line['Caption']);
                }
                array_push($dr, $line['ID']);
                $st .= $line['Caption'] . ';';
            }
        }
        if ($type == 'A') {
            return $dr;
        }
        if ($type == 'S') {
            return $st;
        }
    }
    function E404()
    {
        $sx = '<h1>' . 'ERROR: 404' . '</h1>';
        $sx .= '<p>' . lang('rdf.concept_was_deleted') . '</p>';
        $sx .= '<button onclick="history.back()">Go Back</button>';
        return ($sx);
    }

    function remove($ID)
    {
        $Socials = new \App\Models\Socials();
        $user = $Socials->validToken();

        $RSP = [];
        $RSP['date'] = date("Y-m-d");

        if (isset($user['ID'])) {
            $perfil = ' ' . $user['perfil'];
            if (strpos(' '.$perfil, '#ADM') > 0) {
                $RDFconcept = new \App\Models\RDF2\RDFconcept();
                $RDFdata = new \App\Models\RDF2\RDFdata();
                $IssuesWorks = new \App\Models\Base\IssuesWorks();
                $Elastic = new \App\Models\ElasticSearch\Register();

                $dt = $RDFconcept->le($ID);
                $RSP['status'] = $dt['status'];

                $Elastic->where("ID",$ID)->delete();

                $IssuesWorks->where("siw_work_rdf",$ID)->delete();

                $dr = $RDFdata
                    ->where('d_r1', $ID)
                    ->Orwhere('d_r2', $ID)
                    ->findAll();
                foreach ($dr as $idr => $line) {
                    $dd = [];
                    $dd['d_r1'] = $line['d_r1'] * (-1);
                    $dd['d_p'] = $line['d_p'] * (-1);
                    $dd['d_r2'] = $line['d_r2'] * (-1);
                    $dd['d_literal'] = $line['d_literal'] * (-1);
                    $dd['d_update'] = date("Y-m-d");
                    $RDFdata->set($dd)
                        ->where('d_r1', $ID)
                        ->Orwhere('d_r2', $ID)
                        ->update();
                }

                $dd = [];
                $dd['cc_status'] = 9;
                $dd['cc_update'] = date("Y-m-d");
                if ($dt['cc_pref_term'] > 0) {
                    $dd['cc_pref_term'] = $dt['cc_pref_term'] * (-1);
                    $RSP['status'] = '300';
                    $RSP['message'] = 'Success!';
                } else {
                    $RSP['message'] = 'This record already removed!';
                }

                $RDFconcept->set($dd)->where("id_cc", $ID)->update();
            } else {
                $RSP['status'] = '501';
                $RSP['message'] = 'Access not Allow! - ' . trim($perfil);
            }
        } else {
            $RSP = $user;
        }

        return $RSP;
    }
}
