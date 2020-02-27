<?php
class find_rdf extends CI_model
{
	function import($lib)
	{
		$http = 'http://192.168.0.115/sisdoc/find/index.php/main/m/'.$lib;
		$sx = file_get_contents($http);
		$dt = json_decode($sx);
		$dd = $dt->works;
		$sx = '<h1>Books</h1>';
		for ($r=0;$r < count($dd);$r++)
		{
			$id = $dd[$r];
			$dt = $this->book($id);
			echo '<pre>';
			print_r($dt);
			echo '</pre>';

			$isbn = $dt['isbn']['isbn13'];

			$lugar = $dt['tombo_own'];
                //$place = $this->books->place($lugar);
			$place = 9;
			if (isset($dt['hasFileName']))
			{
				$file = $dt['hasFileName'];
			} else {
				$file = '';
			}


			/* Item */
			$tipo['library'] = $lib;
			$tombo = sonumero($dt['tombo']);
			$idex = $this->books_item->tombo_insert($tombo,$isbn,$tipo,9,$place,0,1);

			$sql = "select * from find_item 
			LEFT JOIN find_manifestation ON i_manitestation = id_m
			where i_tombo = ".$tombo;		
			$rlt = $this->db->query($sql);
			$rlt = $rlt->result_array();

			$dt['item'] = $rlt[0]['id_i'];
			$sx .= $this->books->process_register($isbn,$dt,'FINDS');
			echo $sx;
			exit;
		}			
	}

	function book($isbn)
	{
		$type = 'FINDS';
		$dt = array();
		$dt['totalItems'] = 0;
		$url = $this->isbn->urls($isbn,$type);
		$t = file_get_contents($url);
		$ida = 0;
		$idt = 0;
		return($dt);
		
		$dt['serie'] = '';
		$dt['cover'] = '';
		$dt['editora'] = '';
		$dt['subject']= array();
		$dt['item'] = $isbn;
		$dt['descricao'] = '';	
		$dt['pages'] = '';
		$dt['authors'] = array();
		$dt['translator'] = array();
		$dt['error'] = 0;
		$dt['error_msg'] = msg('ISBN_inported');
		$dt['totalItems'] = 1;
		$dt['url'] = '';
		$dt['type'] = $type;
		$dt['library'] = '';
		
		
		$t = json_decode($t);
		foreach ($t as $key => $value) {
			echo '<br>'.$key.'=='.$value;
			switch ($key) {
				case 'hasTitle':
				$dt['title'] = nbr_author($value,18);
				break;

				case 'hasRegisterId':
				$dt['tombo'] = $value;
				break;

				case 'hasLocatedIn':
				$dt['tombo_library'] = $value;
				break;

				case 'isOwnedBy':
				$dt['tombo_own'] = $value;
				break;	

				case 'wayOfAcquisition':
				$dt['tombo_acquisition'] = $value;
				break;					


				case 'hasCover':
				$dt['cover'] = 'http://192.168.0.115/sisdoc/find/_repositorio/image/'.$value;
				break;

				case 'hasAuthor':
				$dt['authors'][$ida] = $value;
				$ida++;
				break;

				case 'hasLanguageExpression':
				$dt['expressao']['idioma'] = $value;
				$dt['expressao']['genere'] = 'books';
				break;

				case 'hasTranslator':
				$auth = $value;
				$dt['translator'][$idt] = $auth;
				$idt++;
				break;

				case 'hasClassificationCDU':
				$dt['cdu'] = $value;
				break;

				case 'hasPage':
				$dt['pages'] = $value;
				break;

				case 'isPlaceOfPublication':
				$dt['place'] = nbr_author($value,18);
				break;

				case 'dateOfPublication':
				$dt['data'] = $value;
				break;

				case 'hasISBN':
				$isbnx = $this->isbn->isbns(sonumero($value));
				$dt['isbn'] = $isbnx;
				$isbn = $isbnx['isbn13'];				
				
				default:
					# code...
				break;
			}
		}
		return($dt);
	}
}
?>