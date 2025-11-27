<?php
include 'datos.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Imprimir</title>
</head>
<body>

<table border="1">
    <tr>
        <th></th>
        <th>Uds.</th>
        <th>Referencia</th>
        <th>Concepto</th>
        <th>Precio ud.</th>
        <th>Subtotal</th>
    </tr>

    <?php
        global $conceptos;
        $totaluds = 0;
        $totalbruto = 0;
        $subtotal = 0;
        $cont = 1;


        for($i = 0; $i < count($conceptos); $i++, $cont++){
            $subtotal = number_format($conceptos[$i]['unidades'] * $conceptos[$i]['precio_unidad'],2);
            print "<tr>";
            print "<td>" . $cont . "</td>";
            print "<td>" . $conceptos[$i]['unidades'] . "</td>";
            print "<td>" . $conceptos[$i]['referencia'] . "</td>";
            print "<td>" . $conceptos[$i]['concepto'] . "</td>";
            print "<td>" . number_format($conceptos[$i]['precio_unidad'],2) . " €</td>";
            print "<td>" . $subtotal . " €</td>";
            print "</tr>";

            $totaluds += $conceptos[$i]['unidades'];
            $totalbruto += $subtotal;
        }
    ?>

        <?php
            $descuento = 0;
            $iva = 0.21;
            print ("<tr>");
            print ("<td></td>");
            print "<td>". $totaluds. "</td>";
            print "<td colspan='3' align='right'>Bruto: </td>";
            print("<td>" . $totalbruto . " €</td></tr>");

            if($totalbruto >= 2000 && $totalbruto <= 3000 ){
                $descuento = 0.10;
            }

            if($totalbruto > 3000){
                $descuento = 0.20;
            }

            $descuentoapli = number_format($totalbruto*$descuento,2);
            $ivaapli = number_format(($totalbruto-$descuentoapli)*$iva,2);
            $neto = number_format(($totalbruto-$descuentoapli+$ivaapli),2);

            print("<tr>");
            print("<td colspan='5' align='right'>Descuento ( " . $descuento*100 . "% ): </td>");
            print("<td>-" . $descuentoapli . " €</td>");
            print("</tr>");

            print("<tr>");
            print("<td colspan='5' align='right'>IVA: </td>");
            print("<td>" . $ivaapli . " €</td>");
            print("</tr>");

            print("<tr>");
            print("<td colspan='5' align='right'>Neto: </td>");
            print("<td>" . $neto . " €</td>");
            print("</tr>");

            
        ?>
</table>
</body>
</html>