<?php

namespace App\Models\Bookself;

use CodeIgniter\Model;

class PlaceBookshelf extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library_place_bookshelf';
    protected $primaryKey       = 'id_bs';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_bs', 'bs_name', 'bs_image', 'bs_bs', 'bs_LIBRARY', 'bs_place'
    ];

    protected $typeFields    = [
        'hidden', 'string:100', 'string:100', 'string:100', 'set:' . LIBRARY, 'sql:id_lp:lp_name:library_place:lp_LIBRARY=\'' . LIBRARY . '\''
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

    function index($d1 = '', $d2 = '', $d3 = '', $d4 = '')
    {
        $this->path = PATH . MODULE . 'admin/PlaceBookshelf';
        $this->path_back = PATH . MODULE . 'admin/PlaceBookshelf';
        switch ($d1) {
            case 'edit':
                $sx = $this->edit($d2);
                break;
            case 'viewid':
                $sx = $this->viewid($d2);
                break;
            default:
                $this->where('bs_LIBRARY', LIBRARY);
                $sx = $this->tableview($id);
                break;
        }
        return $sx;
    }
    function tableview($id)
    {
        $dt = $this
            ->join('library_place', 'id_lp = bs_place', 'right')
            ->where('lp_LIBRARY', $id)
            ->findAll();

        $sx = '<table class="table">';
        $sx .= '<tr>
                        <th width="1%">#</th>
                        <th width="40%">' . lang('find.place') . '</th>
                    </tr>';

        $xlib = '';

        for ($r = 0; $r < count($dt); $r++) {
            $line = $dt[$r];

            $lib = $line['id_lp'];
            if ($xlib != $lib) {
                $xlib = $lib;
                $link = onclick(PATH . MODULE . 'popup/bookshelf/' . $line['id_lp'] . '/edit/0', 800, 800) . bsicone('plus') . '</span>';
                $sx .= '<tr>';
                $sx .= '<td colspan="2" class="h6">' . $line['lp_name'] . '</td>';
                $sx .= '<td width="2%">&nbsp;</td>';
                $sx .= '<td width="2%">' . $link . '</td>';
                $sx .= '</tr>';
            }

            if ($line['id_bs'] != '') {
                $link = onclick(PATH . MODULE . 'popup/bookshelf/' . $line['id_lp'] . '/edit/' . $line['id_bs'], 800, 500) . bsicone('edit') . '</span>';

                $sx .= '<tr>';
                $sx .= '<td>&nbsp;</td>';
                $sx .= '<td>' . $line['bs_name'] . '</td>';
                $sx .= '<td width="2%">' . $link . '</td>';
                $sx .= '</tr>';
            }
        }

        if (count($dt) == 0) {
            $link = onclick(PATH . MODULE . 'popup/PlaceBookshelf/edit/' . $id);
            $sx .= '<tr>';
            $sx .= '<td>' . $link . '</td>';
            $sx .= '</tr>';
        }
        $sx .= '</table>';
        return ($sx);
    }

    function edit($id = 0, $id2 = '', $id3 = '')
    {
        $this->id = $id3;
        $this->path = PATH . MODULE . 'popup/bookshelf/' . $id;
        $this->path_back = 'wclose';
        $_POST['bs_place'] = $id;
        $sx = form($this);
        return $sx;
    }

    function viewid($id)
    {
        $dt = $this->find($id);
        $sx = h($dt['bs_name']);
        return $sx;
    }
}
