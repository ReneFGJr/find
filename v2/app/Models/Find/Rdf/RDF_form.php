<?php
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela rdf_form_class_2
 */
class RDF_Form extends Model
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
        helper('cookie');
        if (!$library == '') {
            $library = get_cookie('library_code') ?? get_cookie('library') ?? null;
        }
        if (!$library) {
            // Redireciona para seleção de biblioteca
            redirect('/bibliotecas');
            exit;
        }

        $db = \Config\Database::connect();
        $sql = "
            SELECT DISTINCT * FROM (
                SELECT
                    id_form, form_frbr, form_group, form_group_subgroup, form_order,
                    id_c, c_class, c_order, id_n,
                    id_cc, cc_use, n_name, n_lang, 'CONCEPT' AS n_type, form_range
                FROM rdf_form_class_2
                INNER JOIN rdf_class ON form_property = id_c
                LEFT JOIN rdf_data ON d_p = id_c and d_r2 = ?
                LEFT JOIN rdf_concept ON id_cc = d_r1
                LEFT JOIN rdf_name ON cc_pref_term = id_n
                WHERE form_frbr = ? and (form_library = ? or form_library = '1000')
                AND form_range <> '[\"132\"]'

                UNION ALL

                SELECT
                    id_form, form_frbr, form_group, form_group_subgroup, form_order,
                    id_c, c_class as c_class, c_order, id_n,
                    id_cc, cc_use, n_name, n_lang, 'CONCEPT' AS n_type, form_range
                FROM rdf_form_class_2
                INNER JOIN rdf_class ON form_property = id_c
                LEFT JOIN rdf_data ON d_p = id_c and d_r1 = ?
                LEFT JOIN rdf_concept ON id_cc = d_r2
                LEFT JOIN rdf_name ON cc_pref_term = id_n
                WHERE form_frbr = ? and (form_library = ? or form_library = '1000')
                AND form_range <> '[\"132\"]'

                UNION ALL

                SELECT
                    id_form, form_frbr, form_group, form_group_subgroup, form_order,
                    id_c, c_class, c_order, id_n,
                    0 as id_cc, 0 as cc_use, n_name, n_lang, 'TEXT' AS n_type, form_range
                FROM rdf_form_class_2
                INNER JOIN rdf_class ON form_property = id_c
                INNER JOIN rdf_data ON d_p = id_c and d_r2 = 0 and d_r1 = ?
                INNER JOIN rdf_name ON d_literal = id_n
                WHERE form_frbr = ? and (form_library = ? or form_library = '1000' or form_library = '0')
            ) AS all_forms
            ORDER BY form_order
        ";
        $params = [
            $conceptId, $frbr, $library, // 1º SELECT
            $conceptId, $frbr, $library, // 2º SELECT
            $conceptId, $frbr, $library  // 3º SELECT
        ];
        $query = $db->query($sql, $params);
        //echo '<pre>';
        //echo $db->getLastQuery(); // Debug: exibe a consulta SQL gerada
        //exit;
        return $query->getResultArray();
    }

}
