<?php

namespace App\Models;

use CodeIgniter\Model;

class Faq extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'faq';
    protected $primaryKey       = 'id_faq';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'faq_question', 'faq_answer', 'faq_active', 'faq_created_at', 'faq_updated_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'faq_created_at';
    protected $updatedField  = 'faq_updated_at';

    public function listActive()
    {
        return $this->where('faq_active', 1)->orderBy('faq_created_at', 'DESC')->findAll();
    }
}
