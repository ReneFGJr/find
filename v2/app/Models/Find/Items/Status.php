<?php
namespace App\Models\Find\Items;

use CodeIgniter\Model;

class Status extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'find_item_status';
    protected $primaryKey       = 'id_is';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_is', 'is_name', 'is_description', 'is_active'
    ];
    protected $useTimestamps = false;
}
