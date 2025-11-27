<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Generar</title>
</head>
<body>
<?php
    include 'datos.php';
    global $conceptos;
    session_name('ud1_24'); // Empiezo la sesion con el nombre
    session_start();

    if(isset($_POST['reiniciar'])){ // El boton de reiniciar
        session_destroy();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if(!isset($_SESSION["numero_version"])){ // Version
        $_SESSION["numero_version"] = 1;
    } else {
        $version = $_SESSION["numero_version"];
    }

    if(!isset($_SESSION["albaran"])){ // Si es la primera vez, creamos el array vacio
        $_SESSION["albaran"] = [];

        $albaran = $_SESSION["albaran"];
    } else {
        $albaran = $_SESSION["albaran"];
    }

    print("<h1>Version:" . $version . "</h1>");
?>
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

    // Agregar a la tabla y si no se puede lanzar un error
    if(isset($_POST["referencia"]) && isset($_POST["concepto"]) && isset($_POST["precio_unidad"]) && isset($_POST["unidades"])){
        //                                                                                                                       Estos isset son para que no salga el mensaje de error cuando pulso los botones
        if($_POST["referencia"] != "" && $_POST["concepto"] != "" && $_POST["precio_unidad"] != "" && isset($_POST["unidades"]) != "" && !isset($_POST["mas"]) && !isset($_POST["menos"]) && $_POST['precio_unidad'] >= 0){

            $referencia = $_POST["referencia"]; // Recojo y añado a la tabla si están todos los conceptos y ninguno está vacio
            $concepto = $_POST["concepto"];
            $precio_unidad = $_POST["precio_unidad"];
            $unidades = $_POST["unidades"];

            $albaran[] = [
                    'referencia' => $referencia,
                    'concepto' => $concepto,
                    'precio_unidad' => $precio_unidad,
                    'unidades' => $unidades
            ];

            $_SESSION["albaran"] = $albaran;

            $version++;
            $_SESSION["numero_version"] = $version; // Si se añade correctamente aumento la versión y la guardo en la sesion

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;

        } else {
            $_SESSION["errores"] = "No se ha podido añadir el producto al albarán";
        }


    }

    // Los errores
    if (isset($_SESSION['errores'])) {
        echo("<p style='color: red'>" . $_SESSION['errores'] . "</p>");
        unset($_SESSION['errores']);
    }

    // Los botones + y - de las unidades
    if(isset($_POST['mas']) || isset($_POST['menos'])){ // Miro si alguno de los botones ha sido pulsado

        if(isset($_POST['mas'])){ // si mas tiene valor es que ha pulsado el +, si no pues el -
            $valor = "mas";
            $refrecibida = $_POST['mas'];
        } else {
            $valor = "menos";
            $refrecibida = $_POST['menos'];
        }


        for($i = 0; $i < count($albaran); $i++) {
            if($albaran[$i]['referencia'] == $refrecibida){ // Recorro el albarán para buscar ese producto por referencia e incrementar o decrementar las unidades
                if($valor == "mas"){
                    $albaran[$i]['unidades']++;
                }

                if($valor == "menos" && $albaran[$i]['unidades'] > 0){
                    $albaran[$i]['unidades']--;
                }

            }
        }

        $_SESSION['albaran'] = $albaran; // Luego guardo los cambios en la sesion

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }



    $totaluds = 0;
    $totalbruto = 0;
    $subtotal = 0;
    $cont = 1;
    // La tabla
    for($i = 0; $i < count($albaran); $i++, $cont++){
        $subtotal = $albaran[$i]['unidades'] * $albaran[$i]['precio_unidad'];
        print "<tr>";
        print("<td>" . $cont . "</td>");
        print("<form method='post'>
                <td><button type='submit' name='mas' value=". $albaran[$i]['referencia'] . ">+</button>". $albaran[$i]['unidades'] . "
                    <button type='submit' name='menos' value=". $albaran[$i]['referencia'] . ">-</button>
                </td>"); // Añado los botones que pasan como value la referencia de ese producto
        print "<td>" . $albaran[$i]['referencia'] . "</td>";
        print "<td>" . $albaran[$i]['concepto'] . "</td>";
        print "<td>" . $albaran[$i]['precio_unidad'] . " €</td>";
        print "<td>" . $subtotal . " €</td>";

        $totaluds += $albaran[$i]['unidades'];
        $totalbruto += $subtotal;

    }

        $descuento = 0;
        $iva = 0.21;
        print ("<tr>");
        print("<td></td>");
        print "<td align='center'>". $totaluds. "</td>";
        print "<td colspan='3' align='right'>Bruto: </td>";
        print("<td>" . $totalbruto . " €</td></tr>");

        if($totalbruto >= 2000 && $totalbruto <= 3000 ){
            $descuento = 0.10;
        }

        if($totalbruto > 3000){
            $descuento = 0.20;
        }

        $descuentoapli =$totalbruto*$descuento;
        $ivaapli =($totalbruto-$descuentoapli)*$iva;
        $neto = ($totalbruto-$descuentoapli+$ivaapli);

        print("<tr>");
        print("<td colspan='5' align='right'>Descuento ( " . $descuento*100 . "% ): </td>");
        print("<td>-" . $descuentoapli . " €</td>");
        print("</tr>");

        // 7 Eliminar

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
<br> <br>
<form method="post" >
    Referencia: <input type="text" minlength="1" name="referencia"> <br>
    Concepto:<input type="text" minlength="1" name="concepto"><br>
    Unidades:<input type="number" min="0" name="unidades"><br>
    Precio:<input type="text" name="precio_unidad"><br>
    <br>
    <input type="submit" value="Enviar">
</form>
<form method="post">
    <br>
    <input type="hidden" name="reiniciar" value="reiniciar">
    <input type="submit" value="Eliminar todo">

</form>
</body>
    </html>
