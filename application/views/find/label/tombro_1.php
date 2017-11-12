<?php
if (!isset($col)) {
    $col = 6;
}

$et = $col * 14 / $dd3;

$pos = 99;
$nr = $dd1;
/*********** label */
$q = 1;
if (!isset($label)) { $label = '';
}
$nn = $dd1;
echo '<body style="margin-top: 0px; padding 0px;">';
for ($p = 1; $p <= $dd2; $p++) {
    //echo 'PAGE: '.$p.' - '.$et;
    echo '<table width="100%" border=1 cellpadding="0" cellspacing="0" style="page-break-after: always; font-family: Arial;">' . cr();
    for ($r = 0; $r < $et; $r++) {
        for ($t = 0; $t < $dd3; $t++) {
            if ($pos >= $col) {
                if ($pos < 50) { echo '</tr>';
                }
                $pos = 0;
                echo '<tr>';
            }
            echo '<td align="center" style="height: 70px; font-size: 20px;" valign="middle">';

            if (strlen($label) > 0) {
                echo '<span style="font-size: 14px">' . $label . '</span>' . cr();
                echo '<br>' . cr();
            }
            echo strzero($nr, 11) . ean13(strzero($nr, 11));
            echo '</td>';
            $pos++;
        }
        $nr++;
    }
    if ($pos > 0) {
        echo '</tr>';
    }
    echo '</table>';
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

function ean13($num)
    {
        $num = substr($num,0,12);
        $n1 = 0;
        $n2 = 0;
        for ($r=(strlen($num)-1);$r >= 0; $r--)
            {
                if (($r/2) == round($r/2))
                    {
                        $n1 = $n1 + round($num[$r]);
                    } else {
                        $n2 = $n2 + round($num[$r]);
                    }
            }
            $tot = round($n2*3)+round($n1);
            $tot = round($n1*3)+round($n2);
            $tot = 10-($tot % 10);
            if ($tot == 10) { $tot = 0; } 
            return($tot);
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
?>
</table>