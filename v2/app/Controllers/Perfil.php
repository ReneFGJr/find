<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class Perfil extends Controller
{
    public function index()
    {
        $userId = session()->get('id_us');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users_group_members m')
            ->select('m.*, g.gr_name, l.l_name')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'left')
            ->join('library l', 'l.l_code = m.grm_library', 'left')
            ->where('m.grm_user', $userId)
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)')
            ->orderBy('l.l_name, g.gr_name');
        $perfis = $builder->get()->getResultArray();

        return view('perfil', [
            'perfis' => $perfis
        ]);
    }
}
