<?php
/* RDF */
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela rdf_class
 */
class RDF_class extends Model
{
    protected $table = 'rdf_class';
    protected $primaryKey = 'id_c';
    protected $allowedFields = [
        'c_class',
        'c_type',
        'c_status',
        'c_created',
        'c_updated',
        // Adicione outros campos conforme a estrutura da tabela
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    function getIdByName($name)
    {
        $dt = $this
            ->where('c_class', $name)
            ->first();
        return $dt ? $dt['id_c'] : null;
    }
}
