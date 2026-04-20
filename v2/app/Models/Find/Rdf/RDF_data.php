<?php
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela rdf_data
 */
class RDF_data extends Model
{
    protected $table = 'rdf_data';
    protected $primaryKey = 'id_d';
    protected $allowedFields = [
        'd_r1',
        'd_p',
        'd_r2',
        'd_literal',
        'd_creadted',
        'd_update',
        'd_library',
        'd_user',
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    // Adicione métodos customizados conforme necessário
}
