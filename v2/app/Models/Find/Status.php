<?php

namespace App\Models\Find;

use CodeIgniter\Model;

class Status extends Model
{
    protected $table = 'find_item_status';
    protected $primaryKey = 'id_is';
    protected $allowedFields = [
        'id_is',
        'is_name',
        'is_description',
        'is_color',
        'is_order',
        'is_active',
    ];
    protected $returnType = 'array';
    public $timestamps = false;
}
