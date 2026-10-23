<?php
/* RDF */
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

helper('cookie');

/**
 * Model para a tabela rdf_form_class_2
 */
class RDF_form extends Model
{
    protected $table = 'rdf_form_class_2';
    protected $primaryKey = 'id_form';
    protected $allowedFields = [
        'form_frbr',
        'form_property',
        'form_range',
        'form_group',
        'form_group_subgroup',
        'form_library',
        'form_order',
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Carrega o formulário RDF conforme grupo e id do conceito
     * @param string $frbr Ex: 'W'
     * @param string|int $library Ex: '1000'
     * @param int $conceptId Ex: 105171
     * @return array
     */
    public function getForm($frbr, $conceptId, $library = '')
    {
        if ($conceptId == null || $conceptId < 1) {
            return [];
        }
        if ($library === '' || $library === null) {
            $library = get_cookie('library_code') ?? get_cookie('library') ?? null;
        }
        if (!$library) {
            // Redireciona para seleção de biblioteca
            redirect('/bibliotecas');
            exit;
        }

        $db = \Config\Database::connect();
        $sql = "
            SELECT DISTINCT id_d, d_r1, d_r2, d_literal,
                    id_form, form_frbr, form_group, form_group_subgroup, form_order,
                    id_c, c_class, c_order,
                    COALESCE(literal_name.id_n, concept_name.id_n) AS id_n,
                    id_cc, cc_use,
                    COALESCE(literal_name.n_name, concept_name.n_name) AS n_name,
                    COALESCE(literal_name.n_lang, concept_name.n_lang) AS n_lang,
                    CASE WHEN d_literal > 0 THEN 'TEXT' ELSE 'CONCEPT' END AS n_type,
                    form_range
            FROM rdf_form_class_2
            INNER JOIN rdf_class ON form_property = id_c
            LEFT JOIN rdf_data ON d_p = id_c AND d_r1 = ?
            LEFT JOIN rdf_concept ON id_cc = d_r2 AND (d_literal IS NULL OR d_literal = 0)
            LEFT JOIN rdf_name concept_name ON cc_pref_term = concept_name.id_n AND (d_literal IS NULL OR d_literal = 0)
            LEFT JOIN rdf_name literal_name ON d_literal = literal_name.id_n AND d_r2 = 0
            WHERE form_frbr = ? AND (form_library = ? OR form_library = '1000' OR form_library = '0')
            ORDER BY form_order
        ";

        $params = [
            $conceptId, $frbr, $library,
        ];
        $query = $db->query($sql, $params);
        $rows = $query->getResultArray();
        foreach ($rows as &$row) {
            $range = json_decode((string) $row['form_range'], true);
            if (!is_array($range)) {
                $range = preg_split('/[|,;]/', str_replace(['[', ']', '"', "'"], '', (string) $row['form_range']));
            }
            $range = array_map(static fn($value) => strtoupper(trim((string) $value)), $range);
            if (array_intersect(['132', 'TEXT', 'LITERAL'], $range)) {
                $row['n_type'] = 'TEXT';
                // A TEXT property must not display a concept's preferred name as its literal.
                if (empty($row['d_literal']) || (int) $row['d_r2'] !== 0) {
                    $row['id_n'] = $row['n_name'] = $row['n_lang'] = null;
                }
                $row['id_cc'] = $row['cc_use'] = 0;
            }
        }
        unset($row);
        return $rows;
    }

}
