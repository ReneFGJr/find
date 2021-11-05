<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfFormText extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdfformtexts';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = false;
	protected $protectFields        = true;
	protected $allowedFields        = [];

	// Dates
	protected $useTimestamps        = false;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];

	function edit($id)	
		{
			$texto = '';

			$RDFLiteral = new \App\Models\Rdf\RDFLiteral();

			/************************* SALVA REGISTRO */
			$action = get("action");
			if ($action != '')
				{
					$texto = get("descript");
					$data = array('n_lang'=>get("lang"),'n_name'=>$texto);

					$RDFLiteral->atualiza($data,$id);
					return wclose();
				} else {
					if ($id > 0)
					{
						$dt = $RDFLiteral->le($id);
						$texto = $dt['n_name'];
					}
				}
			

			$path = PATH.'rdf/text/'.$id;
			$txt = '';
			$txt = form_open($path);
			$txt .= '<span class="supersmall">'.lang('rdf.textarea').'</span>';
			$txt .= '<textarea id="descript" name="descript" style="width: 100%; height: 100px;" class="form-control">'.$texto.'</textarea>';

			$txt .= form_submit('action', lang('rdf.save'), 'class="btn btn-primary supersmall m-3"');
			$txt .= form_close();
			return $txt;
		}
}
