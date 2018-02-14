<?php
class barcodes extends CI_model {
    function isbn13($n)
        {
            // 9 7 8 0 3 0 6 4 0 6 1 5
            $a = 1;
            $vt = 0;
            $n = sonumero($n);
            for ($r=0;$r < strlen($n);$r++)
                {
                   $vt = $vt + round(substr($n,$r,1)) * $a;
                   //echo '<br>'.substr($n,$r,1).'x'.$a.'='.$vt;
                   if ($a == 1)
                    { $a = 3; } else { $a = 1; } 
                    
                }
           /*******************/
           while ($vt >= 10)
                { $vt = $vt - 10; }
           if ($vt > 0)
            {
                $vt = 10 - $vt;
            }
           return($vt);
        }
	function mod11($numero = "") {
		$resto2 = modulo_11($numero, 9, 1);
		$digito = 11 - $resto2;
		if ($digito == 10 || $digito == 11) {
			$dv = 0;
		} else {
			$dv = $digito;
		}
		return $dv;
	}

	function ean13($num) {
		$num = substr($num, 0, 12);
		$n1 = 0;
		$n2 = 0;
		for ($r = (strlen($num) - 1); $r >= 0; $r--) {
			if (($r / 2) == round($r / 2)) {
				$n1 = $n1 + round($num[$r]);
			} else {
				$n2 = $n2 + round($num[$r]);
			}
		}
		$tot = round($n2 * 3) + round($n1);
		$tot = round($n1 * 3) + round($n2);
		$tot = 10 - ($tot % 10);
		if ($tot == 10) { $tot = 0;
		}
		return ($tot);
	}

	function modulo_11($num, $base = 9, $r = 0) {
		/**
		 *   Autor:
		 *           Pablo Costa <pablo@users.sourceforge.net>
		 *
		 *    Calculo do Modulo 11 para geracao do digito verificador
		 *    de boletos bancarios conforme documentos obtidos
		 *    da Febraban - www.febraban.org.br
		 *
		 *   Entrada:
		 *     $base: valor maximo de multiplicacao [2-$base]
		 *     $r: quando especificado um devolve somente o resto
		 *
		 *     Retorna o Digito verificador.
		 *
		 */

		$soma = 0;
		$fator = 2;

		/* Separacao dos numeros */
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num, $i - 1, 1);
			// Efetua multiplicacao do numero pelo falor
			$parcial[$i] = $numeros[$i] * $fator;
			// Soma dos digitos
			$soma += $parcial[$i];
			if ($fator == $base) {
				// restaura fator de multiplicacao para 2
				$fator = 1;
			}
			$fator++;
		}

		/* Calculo do modulo 11 */
		if ($r == 0) {
			$soma *= 10;
			$digito = $soma % 11;
			if ($digito == 10) {
				$digito = 0;
			}
			return $digito;
		} elseif ($r == 1) {
			$resto = $soma % 11;
			return $resto;
		}
	}
}
?>
