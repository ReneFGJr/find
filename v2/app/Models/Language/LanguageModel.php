<?php
namespace App\Models\Language;

use CodeIgniter\Model;

class LanguageModel extends Model
{
    protected $table = 'language';
    protected $primaryKey = 'id_lg';
    protected $allowedFields = [
        'lg_code',
        'lg_name',
        'lg_orderm',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    function getLanguageByCode($code)
    {
        return $this->where('lg_code', $code)->first();
    }

    function getAllLanguages()
    {
        return $this->orderBy('lg_orderm', 'ASC')->findAll();
    }
}
