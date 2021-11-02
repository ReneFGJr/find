<?php

namespace App\Models;

use CodeIgniter\Model;

class FindSearch extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'findsearches';
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

	function banner()
		{
			$sx = '	
			<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
			<ol class="carousel-indicators">
			  <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
			  <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
			  <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner">
			  <div class="carousel-item ">
				<img class="d-block w-100" src="'.URL.('img/banner_01.png').'" alt="First slide">
			  </div>
			  <div class="carousel-item active">
				<img class="d-block w-100" src="'.URL.('img/banner_02.png').'" alt="Second slide">
			  </div>
			  <div class="carousel-item">
				<img class="d-block w-100" src="'.URL.('img/banner_03.png').'" alt="Third slide">
			  </div>
			</div>
			<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
			  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
			  <span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
			  <span class="carousel-control-next-icon" aria-hidden="true"></span>
			  <span class="sr-only">Next</span>
			</a>
		  </div>			
			';
			return $sx;
		}

	function search()
		{
			$sx = bs('
			<form>
			<div class="input-group mb-3 my-4">				
				<input type="text" class="form-control" 
							placeholder="'.lang('find.search_info').'" 
							aria-label="'.lang('find.search_info').'" 
							aria-describedby="basic-addon2"
							>
				<div class="input-group-append">
				<button class="btn btn-danger" type="button">'.lang('find.search').'</button>				
				</div>
			</div>
			</form>
			');
			return $sx;
		}
}
