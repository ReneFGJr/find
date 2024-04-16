<?php

namespace App\Controllers;

use App\Controllers\BaseController;

$this->session = \Config\Services::session();
$language = \Config\Services::language();

helper(['boostrap', 'url', 'graphs', 'sisdoc_forms', 'form', 'nbr', 'sessions', 'cookie']);
$session = \Config\Services::session();

$this->Socials = new \App\Models\Socials();
define("PATH", $_SERVER['app.baseURL'] . $_SERVER['app.prefix']);
define("MODULE", 'find/');
define("URL", $_SERVER['app.baseURL']);
define('PREFIX', $_SERVER['PREFIX']);

class Find extends BaseController
{
	function index()
		{
			print("HELLO WORLD!");
		}
}