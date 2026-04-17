<?php

namespace App\Models\Find\Library;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'library';
    protected $primaryKey       = 'id_l';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_l',
        'l_name',
        'l_code',
        'l_id',
        'l_logo',
        'l_about',
        'l_visible',
        'l_net'
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

    function createLibrary($d3)
    {
        $RSP = [];
        $RSP['post'] = $_POST;
        return $RSP;
    }

    function saveLibrary()
    {
        $RSP = [];
        $id = get("id_l");
        $dd = array_merge($_POST, $_GET);

        if ($id == '0') {
            $dt = $this->where('l_name', $dd['l_name'])->first();
            if ($dt) {
                $RSP['status'] = '400';
                $RSP['error'] = 'Já existe uma biblioteca com este nome.';
                return $RSP;
            }

            unset($dd['l_id']);
            $dd['l_code'] = '';
            $dd['l_id'] = '';
            $newId = $this->set($dd)->insert();

            // calcula o código e atualiza só os campos necessários
            $code = 1000 + $newId;
            $this->update($newId, [
                'l_code'   => $code,
                'l_idcode' => $code,
            ]);

            $RSP['library'] = $this->find($newId);
            $RSP['status'] = '200';
            return $RSP;

            $dd['l_code'] = 1000+$id;
            $dd['l_idcode'] = $dd['l_code'];
            $this->set($dd)->where('id_l',$id)->update();

        }
        $RSP['post'] = $dd;
        return $RSP;
    }

    public function logoUrl(array $library): string
    {
        $code = trim((string) ($library['l_code'] ?? ''));
        $customLogo = trim((string) ($library['l_logo'] ?? ''));

        $candidates = [];

        if ($customLogo !== '') {
            $candidates[] = ['file' => FCPATH . 'img/logo/' . $customLogo, 'url' => base_url('img/logo/' . $customLogo)];
            $candidates[] = ['file' => FCPATH . 'img/' . $customLogo, 'url' => base_url('img/' . $customLogo)];
        }

        if ($code !== '') {
            $candidates[] = ['file' => FCPATH . 'img/logo/logo_' . $code . '.png', 'url' => base_url('img/logo/logo_' . $code . '.png')];
            $candidates[] = ['file' => FCPATH . 'img/logo/logo_' . $code . '.jpg', 'url' => base_url('img/logo/logo_' . $code . '.jpg')];
            $candidates[] = ['file' => FCPATH . 'img/logo/logo_' . $code . '.jpeg', 'url' => base_url('img/logo/logo_' . $code . '.jpeg')];
        }

        $candidates[] = ['file' => FCPATH . 'img/logo/no_logo.png', 'url' => base_url('img/logo/no_logo.png')];
        $candidates[] = ['file' => FCPATH . 'img/logo_library.png', 'url' => base_url('img/logo_library.png')];

        foreach ($candidates as $candidate) {
            if (is_file($candidate['file'])) {
                return $candidate['url'];
            }
        }

        return base_url('img/logo_find.png');
    }

    public function normalizeLibrary(array $line): array
    {
        return [
            'id' => $line['id_l'] ?? null,
            'name' => $line['l_name'] ?? ($line['Library'] ?? 'Biblioteca'),
            'code' => $line['l_code'] ?? ($line['ID'] ?? ''),
            'logo' => $this->logoUrl($line),
            'about' => trim((string) ($line['l_about'] ?? '')),
        ];
    }

    public function getSelectedLibrary(string $value): ?array
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $dt = $this->groupStart()
            ->where('l_code', $value)
            ->orWhere('id_l', $value)
            ->groupEnd()
            ->first();

        return is_array($dt) ? $this->normalizeLibrary($dt) : null;
    }

    function le($id)
    {
        $dt = $this->where('l_id', $id)->first();

        if (!is_array($dt)) {
            return [];
        }

        $lib = $this->normalizeLibrary($dt);
        return [
            'Library' => $lib['name'],
            'ID' => $lib['code'],
            'Logo' => $lib['logo'],
        ];
    }

    function listAll()
    {
        $dt = $this
            ->orderBy('l_name')
            ->where('l_visible', 1)
            ->findAll();

        $libs = [];
        foreach ($dt as $line) {
            $libs[] = $this->normalizeLibrary($line);
        }

        return $libs;
    }
}
