<?php
class frbr_clients extends CI_model {
	
	function inport_class($tp='C')
		{
			
		}
	
	function export_class($tp='C') {
		$sql = "select * from rdf_class where c_type = '$tp' ";
		$rlt = $this -> db -> query($sql);
		$rlt = $rlt -> result_array();

		#versao do encoding xml
		$dom = new DOMDocument("1.0", "ISO-8859-1");

		#retirar os espacos em branco
		$dom -> preserveWhiteSpace = false;

		#gerar o codigo
		$dom -> formatOutput = true;

		#criando o nó principal (root)
		$root = $dom -> createElement("rdf");

		#nó filho (contato)
		$data = $dom -> createElement('update', date("YmdHis"));
		$root -> appendChild($data);

		for ($r = 0; $r < count($rlt); $r++) {
			$line = $rlt[$r];
			$classe = $dom -> createElement("class");
			foreach ($line as $key => $value) {
				#setanto nomes e atributos dos elementos xml (nós)
				$c1 = $dom -> createElement($key, $value);
				#adiciona os nós (informacaoes do contato) em contato
				$classe -> appendChild($c1);				
			}
			$root -> appendChild($classe);
		}

		#adiciona o nó contato em (root) agenda
		
		$dom -> appendChild($root);

		# Para salvar o arquivo, descomente a linha
		$dom->save("_documment/class_".$tp.".xml");

		#cabeçalho da página
		header("Content-Type: text/xml");
		# imprime o xml na tela
		print $dom -> saveXML();
	}

}
?>
