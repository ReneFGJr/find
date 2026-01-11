<?php

namespace App\Models\Manual;

use CodeIgniter\Model;
use Parsedown;

class ManualModel extends Model
{
    protected $table            = 'manual';
    protected $primaryKey       = 'id_m';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // Não usa soft delete
    protected $useSoftDeletes   = false;

    // Campos permitidos para insert/update
    protected $allowedFields = [
        'm_section_1',
        'm_section_2',
        'm_section_3',
        'm_section_4',
        'm_title',
        'm_tags',
        'm_nivel'
    ];

    // Datas
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = null; // não existe na tabela
    protected $deletedField  = null;

    // Validações (opcional, mas recomendado)
    protected $validationRules = [];
    protected $validationMessages = [];

    function markdown_to_html(string $markdown): string
    {
        $parsedown = new Parsedown();

        // Segurança: NÃO permitir HTML cru
        $parsedown->setSafeMode(true);

        $html = $parsedown->text($markdown);
        $html = str_replace('<code>', '<code class="language-markdown">', $html);
        return $html;
    }

    function section($dt)
    {
        $section = '';
        if ($dt['m_section_1'] != 0) {
            $section .= $dt['m_section_1'];
            if ($dt['m_section_2'] != 0) {
                $section .= '.' . $dt['m_section_2'];
                if ($dt['m_section_3'] != 0) {
                    $section .= '.' . $dt['m_section_3'];
                    if ($dt['m_section_4'] != 0) {
                        $section .= '.' . $dt['m_section_4'];
                    }
                }
            }
        }
        return $section;
    }

    function create_section($section)
    {
        $data = [
            'm_section_1' => $section[0] ?? 0,
            'm_section_2' => $section[1] ?? 0,
            'm_section_3' => $section[2] ?? 0,
            'm_section_4' => $section[3] ?? 0,
            'm_title' => 'Nova Seção ' . implode('.', $section),
            'm_tags' => '',
            'm_nivel' => 1
        ];
        $this->insert($data);
    }

    function index($d2, $d3)
    {
        if ($d2 == 'getSection') {
            $d3 = explode('.', $d3);
            $section =$d3;
            $this->where('m_section_1', $d3[0] ?? 0)
                ->where('m_section_2', $d3[1] ?? 0)
                ->where('m_section_3', $d3[2] ?? 0)
                ->where('m_section_4', $d3[3] ?? 0);
            $data = $this->first();
            if ($data) {
                return [
                    'status' => '200',
                    'data' => $data,
                    'title' => $data['m_title'],
                    'section' => $this->section($data),
                    'content' => $this->markdown_to_html($data['m_content'])
                ];
            } else {
                $this->create_section($d3);
                return ['status' => '404', 'content' => 'Section not found - ' . $section];
            }
        } else {
            return ['status' => '400', 'content' => 'Invalid action'];
        }
    }
}
