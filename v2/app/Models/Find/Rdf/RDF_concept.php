<?php
/* RDF */
namespace App\Models\Find\Rdf;

use CodeIgniter\Model;

/**
 * Model para a tabela rdf_concept
 */
class RDF_Concept extends Model
{
    protected $table = 'rdf_concept';
    protected $primaryKey = 'id_cc';
    protected $allowedFields = [
        'cc_class',
        'cc_use',
        'cc_created',
        'cc_pref_term',
        'cc_origin',
        'cc_update',
        'cc_status',
        'cc_library',
        'cc_version',
        'c_equivalent',
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';
}
