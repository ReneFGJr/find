<?php

namespace App\Models\Rdf;

use CodeIgniter\Model;

class RdfForm extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'rdfforms';
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

function form($id, $dt) {
		$class = $dt['cc_class'];

		$sx = '';
		$js1 = '';     

		/* complementos */
		switch($class) {
			default :
			$cp = 'n_name, cpt.id_cc as idcc, d_p as prop, id_d, d_literal';
			$sqla = "select $cp from rdf_data as rdata
			INNER JOIN rdf_class as prop ON d_p = prop.id_c 
			INNER JOIN rdf_concept as cpt ON d_r2 = id_cc 
			INNER JOIN rdf_name on cc_pref_term = id_n
			WHERE d_r1 = $id and d_r2 > 0";
			$sqla .= ' union ';
			$sqla .= "select $cp from rdf_data as rdata
			LEFT JOIN rdf_class as prop ON d_p = prop.id_c 
			LEFT JOIN rdf_concept as cpt ON d_r2 = id_cc 
			LEFT JOIN rdf_name on d_literal = id_n
			WHERE d_r1 = $id and d_r2 = 0";
			/*****************/
			$sql = "select * from rdf_form_class
			INNER JOIN rdf_class as t0 ON id_c = sc_propriety
			LEFT JOIN (" . $sqla . ") as t1 ON id_c = prop 
			LEFT JOIN rdf_class as t2 ON sc_propriety = t2.id_c
			where sc_class = $class 
			order by sc_ord, id_sc, t0.c_order";

			$rlt =  (array)$this->db->query($sql)->getResult();
			$sx .= '<table width="100%" cellpadding=5>';
			$js = '';
			$xcap = '';
			$xgrp = '';
			for ($r = 0; $r < count($rlt); $r++) {
				$line = (array)$rlt[$r];
				$grp = $line['sc_group'];
				if ($xgrp != $grp)
				{
					$sx .= '<tr>';
					$sx .= '<td colspan=3 class="middle" style="border-top: 1px solid #000000; border-bottom: 1px solid #000000;" align="center">';
					$sx .= msg($grp);
					$sx .= '</td>';
					$sx .= '</tr>';
					$xgrp = $grp;
				}


				$cap = msg($line['c_class']);

				/************************************************************** LINKS EDICAO */
				$idc = $id; /* ID do conceito */
				$form_id = $line['id_sc']; /* ID do formulário */
				/* $class =>  ID da classe */

				$furl = base_url(PATH.'rdf/form/'.$class.'/'.$line['id_sc'].'/'.$id);

				$link = '<a href="#" id="action_' . trim($line['c_class']) . '" 
				onclick="newxy(\''.$furl.'\',800,400);">';
				$linka = '</a>';
				$sx .= '<tr>';
				$sx .= '<td width="25%" align="right" valign="top">';

				if ($xcap != $cap) {
					$sx .= '<nobr><i>' . msg($line['c_class']) . '</i></nobr>';
					$sx .= '<td width="1%" valign="top">' . $link . '[+]' . $linka . '</td>';
					$xcap = $cap;
				} else {
					$sx .= '&nbsp;';
					$sx .= '<td>-</td>';
				}
				$sx .= '</td>';

				/***************** Editar campo *******************************************/
				$sx .= '<td style="border-bottom: 1px solid #808080;">';
				if (strlen($line['n_name']) > 0) {
					$linkc = '<a href="' . base_url(PATH . '/v/' . $line['idcc']) . '" class="middle">';
					$linkca = '</a>';
					if (strlen($line['idcc']) == 0)
					{
						$linkc = '';
						$linkca = '';
					}

					$sx .= $linkc . $line['n_name'] . $linkca;


					/********************** Editar caso texto */
					$elinka = '</a>';
					if (strlen($line['idcc']) == 0)
					{
						$onclick = onclick(PATH.'rdf/text/'.$line['d_literal'],$x=600,$y=400,$class="btn-warning p-1 text-white supersmall rounded");
						$elink = $onclick;
						
						$sx .= '&nbsp; '.$elink . '[ed]' . $elinka;
					}
					/********************* Excluir lancamento */
					$onclick = onclick(PATH.'rdf/exclude/'.$line['id_d'],$x=600,$y=400,$class="btn-danger p-1 text-white supersmall rounded");
					$link = $onclick;
					$sx .= '&nbsp; '. $link .'[X]' . $elinka;
					$sx .= '</span>';
				}

				$sx .= '</td>';
				$sx .= '</tr>';				
			}
			$sx .= '</table>';
			break;
		}		
		return ($sx);
	}	

function form_class_edit($id,$class='')
		{
			$RDFPrefix = new \App\Models\RDF\RDFPrefix();
			$sql = "
			SELECT id_sc, sc_class, sc_propriety, sc_ord, id_sc,
			t1.c_class as c_class, t2.prefix_ref as prefix_ref,
			t3.c_class as pc_class, t4.prefix_ref as pc_prefix_ref,
			sc_group, sc_library
			FROM rdf_form_class
			INNER JOIN rdf_class as t1 ON t1.id_c = sc_propriety
			LEFT JOIN rdf_prefix as t2 ON t1.c_prefix = t2.id_prefix

			LEFT JOIN rdf_class as t3 ON t3.id_c = sc_range
			LEFT JOIN rdf_prefix as t4 ON t3.c_prefix = t4.id_prefix

			where sc_class = $class
			AND ((sc_global =1 ) or (sc_library = 0) or (sc_library = ".LIBRARY."))
			order by sc_ord";

			$rlt = (array)$this->db->query($sql)->getResult();
			
			$sx = '<div class="col-md-12">';
			$sx .= '<h4>'.msg("Form").'</h4>';
			$sx .= '<table class="table">';
			$sx .= '<tr><th width="4%">#</th>';
			$sx .= '<th width="47%">'.msg('propriety').'</th>';
			$sx .= '<th width="42%">'.msg('range').'</th>';
			$sx .= '<th width="5%">'.msg('group').'</th>';
			$sx .= '</tr>';
			for ($r=0;$r < count($rlt);$r++)			
			{
				$line = (array)$rlt[$r];
				$link = '<a href="#" onclick="newxy(\''.base_url(PATH.'config/class/formss/'.$line['sc_class'].'/'.$line['id_sc']).'\',800,600);">';
				$linka = '</a>';
				$sx .= '<tr>';

				$sx .= '<td align="center">';
				$sx .= $line['sc_ord'];
				$sx .= '</td>';

				/* CLASS */
				$prop = $RDFPrefix->prefixn($line);
				$sx .= '<td>';	
				$sx .= $link;			
				$sx .= msg($line['c_class']).' ('.$prop.')';
				$sx .= $linka;
				$sx .= '</td>';

				/* RANGE */
				$dt = array();
				$dt['c_class'] = $line['pc_class'];
				$dt['prefix_ref'] = $line['pc_prefix_ref'];
				$sx .= '<td>';
				$sx .= $RDFPrefix->prefixn($dt);
				$sx .= '</td>';

				/* GROUP */
				$dt = array();
				$sx .= '<td>';
				$sx .= $line['sc_group'];
				$sx .= '</td>';

				$sx .= '</tr>';
			}
			$sx .= '</table>';
			$sx .= '</div>';

			$link = '<a href="#" onclick="newxy(\''.base_url(PATH.'config/class/formss/'.$id.'/0').'\',800,600);">';
			$linka = '</a>';
			$sx .= $link.'novo'.$linka;

			return($sx);
		}	
}
