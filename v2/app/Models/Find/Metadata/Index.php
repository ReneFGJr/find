<?php

namespace App\Models\Find\Metadata;

use CodeIgniter\Model;

class Index extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'indices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    function metadata($dt, $RSP = [])
    {
        $fld = ['Title','Authors', 'Publisher', 'Place', 'CDD', 'CDU', 'Subject', 'Langage','Page',''];

        foreach($fld as $name)
            {
                if (!isset($RSP[$name]))
                    {
                        $RSP[$name] = [];
                    }
            }



        foreach ($dt['data'] as $id => $line) {
            $Class = $line['Class'];
            $Prop = $line['Property'];
            $value = $this->getValue($line);
            switch ($Prop) {
                case 'prefLabel':
                    array_push($RSP['Title'], $value);
                    break;
                case 'hasAuthor':
                    array_push($RSP['Authors'], $value);
                    break;
                case 'isPlacePublisher':
                    array_push($RSP['Place'], $value);
                    break;
                case 'isPublisher':
                    array_push($RSP['Publisher'], $value);
                    break;
                case 'isPlaceOfPublication':
                    array_push($RSP['Publisher'], $value);
                    break;
                case 'hasClassificationCDD':
                    array_push($RSP['CDD'], $value);
                    break;
                case 'hasClassificationCDU':
                    array_push($RSP['CDU'], $value);
                    break;
                case 'hasFormExpression':
                    array_push($RSP['Langage'],$value);
                    break;
                case 'hasSubject':
                    array_push($RSP['Subject'], $value);
                    break;
                case 'hasPage':
                    array_push($RSP['Page'], $value);
                    break;
                /********* None */
                case 'isAppellationOfExpression':
                    break;
                case 'isAppellationOfManifestation':
                    break;
                default:
                    echo '==='.$Prop;
                    pre($value);
                    exit;

            }
        }
        return $RSP;
    }

    function getValue($line)
        {
            $ID = $line['ID'];
            $name = $line['Caption'];
            $lang = $line['Lang'];
            $dd = [];
            $dd['name'] = $name;
            $dd['lang'] = substr($lang,0,2);
            $dd['ID'] = $ID;
            return $dd;
        }
}
