<?php
namespace App\Libraries;

class CatalogPermissions
{
    public function canCatalog(string $libraryCode): bool
    {
        $userId = (int) (session()->get('id_us') ?? 0);
        if ($userId <= 0 || trim($libraryCode) === '') return false;

        $rows = \Config\Database::connect()->table('users_group_members m')
            ->select('g.gr_hash, g.gr_name')
            ->join('users_group g', 'g.id_gr = m.grm_group', 'inner')
            ->where('m.grm_user', $userId)
            ->where('m.grm_library', $libraryCode)
            ->where('(m.grm_status = 1 OR m.grm_status IS NULL)')
            ->get()->getResultArray();

        foreach ($rows as $row) {
            $hash = strtoupper(trim((string) ($row['gr_hash'] ?? '')));
            $name = strtolower(trim((string) ($row['gr_name'] ?? '')));
            if (in_array($hash, ['#ADM', '#CAT'], true)
                || in_array($name, ['administrador', 'admin', 'catalogadores', 'catalogador'], true)) {
                return true;
            }
        }
        return false;
    }
}
