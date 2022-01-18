<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfFormVC extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdfformvcs';
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

	function search($d1,$d2,$d3)
		{
			$RDFConcept = new \App\Models\RDF\RDFConcept();
			$sx = '<select class="form-control" size="5" name="dd51" id="dd51">';
			/********************************/
			$q = get("q");
			$sx .= $q;
			$sx .= $d2;
			if (strlen($q) >=3)
				{
					$sx .= '<option value="">Buscando ... '.$q.'</option>'.cr();
					$dt = $RDFConcept->like($q,$d2);
					for($r=0;$r < count($dt);$r++)
						{
							$ln = (array)$dt[$r];
							$idx = $ln['id_cc'];
							if ($ln['cc_use'] > 0) { $idx = $ln['cc_use']; }
							$name = $ln['n_name'];
							if (strpos($name,'#')) { $name = substr($name,0,strpos($name,'#')); }
							$sx .= '<option value="'.$idx.'">'.$name.'</option>'.cr();
						}
				}
			$sx .= '</select>';

			return $sx;
		}	

	function edit($d1,$d2,$d3,$range)	
	{
		$sx = '';
		$sx .= h($range,2);

		/************************* SALVA REGISTRO */
		$action = get("action");

		$path = PATH.MODULE.'rdf/form/edit/'.$d1.'/'.$d2.'/'.$d3;

		$sx .= form_open($path);
		$sx .= '<span class="small">'.lang('find.filter_to').' '.lang('find.'.$range).'</span>';
		$sx .= '<input type="text" id="dd50" name="dd50" class="form-control">';

		/* Select */
		$sx .= '<span class="small mt-1">'.lang('find.select_an').' '.lang('find.'.$range).'</span>';
		$sx .= '<div id="dd51a"><select class="form-control" size="5" name="dd51" id="dd51"></select></div>';
		$sx .= form_close();

		$bts = '';
		$bts .= '<input type="submit" id="b1" class="btn btn-outline-secondary" disabled value="'.lang('find.force_create').'"> ';
		$bts .= '<input type="submit" id="b2" class="btn btn-outline-primary" disabled value="'.lang('find.save_continue').'"> ';
		$bts .= '<input type="submit" id="b3" class="btn btn-outline-primary" disabled value="'.lang('find.save').'"> ';
		$bts .= '<button onclick="window.close();" id="b4" class="btn btn-outline-danger">'.lang('find.cancel').'</buttontype=>';

		$sx .= bsc($bts,12);

		$js = '';
		$js .= '<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>';
		$js .= '<script>
		/************ keyup *****************/
		jQuery("#dd50").keyup(function() 
		{
			var $key = jQuery("#dd50").val();
			$.ajax(
				{
					type: "POST",
					url: "'.PATH.MODULE.'rdf/search/'.$range.'/?q="+$key,
					success: function(data){
						$("#dd51a").html(data);
					}
				}
			);
		});
		</script>';
		return $sx.$js;
	}	
}
