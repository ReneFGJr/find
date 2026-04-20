<?php
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

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
        $sql = "SELECT
                form_frbr, form_group, form_group_subgroup, form_order,
                id_c, c_class, c_order,
                id_cc, cc_use, n_name, n_lang
            FROM rdf_form_class_2
            INNER JOIN rdf_class ON form_property = id_c
            LEFT JOIN rdf_data ON d_p = id_c and d_r2 = ?
            LEFT JOIN rdf_concept ON id_cc = d_r2
            LEFT JOIN rdf_name ON cc_pref_term = id_n
            WHERE form_frbr = ? and (form_library = ? or form_library = '1000')
            ORDER BY form_order";
        $query = $db->query($sql, [$conceptId, $frbr, $library]);
        //echo $db->getLastQuery(); // Debug: exibe a consulta SQL gerada
        return $query->getResultArray();
    }

}
