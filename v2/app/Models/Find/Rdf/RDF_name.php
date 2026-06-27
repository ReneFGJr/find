<?php
/* RDF */
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela RDF_name
 */
class RDF_name extends Model
{
    protected $table = 'rdf_name2';
    protected $primaryKey = 'id_n';
    protected $allowedFields = [
        'n_name',
        'n_lang',
        'n_type',
        'n_status',
        'n_created',
        'n_updated',
        // Adicione outros campos conforme a estrutura da tabela
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Cria ou retorna o id_n de um literal (nome) na tabela RDF_name
     * - Remove espaços extras
     * - Busca case sensitive
     * - Garante não duplicidade
     * - Usa transação para evitar race condition
     */
    public function createLiteral($name, $lang = 'pt_BR')
    {
        if (is_null($name)) {
            return null;
        }
        $name = trim(preg_replace('/\s+/', ' ', $name));
        if ($name == '') {
            return null;
        }
        $db = \Config\Database::connect();
        $db->transStart();
        $row = $this->where('n_name', $name)->where('n_lang', $lang)->first();
        if ($row) {
            $db->transComplete();
            return $row['id_n'];
        }
        $data = [
            'n_name' => $name,
            'n_lang' => $lang,
            'n_delete' => 0,
            'n_lock' => 0,
            'n_created' => date('Y-m-d H:i:s'),
        ];
        $this->insert($data);
        $idN = $this->getInsertID();
        $db->transComplete();
        return $idN;
    }
}
