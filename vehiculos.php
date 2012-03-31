<?php

include("config.php");
include("include/gen_functions.php");
include("lang/$lang.php");
session_start();

// Insercion de un nuevo Registro
if(isset($_POST['insertsubmit']))
{
	$matricula = $_POST['matricula'];
	$marca = $_POST['marca'];
	$modelo = $_POST['modelo'];
	$combustible = $_POST['combustible'];
	$potencia = $_POST['potencia'];
	$cilindrada = $_POST['cilindrada'];
	$carroceria = $_POST['carroceria'];
	$plazas = $_POST['plazas'];
	$color = $_POST['color'];
	$kilometros = $_POST['kilometros'];
	$pvp = $_POST['pvp'];
	$usoanterior = $_POST['usoanterior'];
	$extras = $_POST['extras'];

	$query = mysql_query("INSERT INTO vehiculos (`matricula`, `marca`, `modelo`, `combustible`, `potencia`, `cilindrada`, `carroceria`, `plazas`, `color`, `kilometros`, `pvp`, `usoanterior`, `extras`, `fechainsercion`) 
		VALUES ('$matricula', '$marca', '$modelo', '$combustible', '$potencia', '$cilindrada', '$carroceria', '$$plazas', '$color', '$kilometros', '$pvp', '$usoanterior', '$extras', '$fechainsercion')");
	

	$vehiculo = mysql_insert_id(); 
 	$dialimite = $_POST['dialimite'];
 	$meslimite = $_POST['meslimite'];
 	$anolimite = $_POST['anolimite'];
 	$horafinal = date("H:i:s");
 	$fechalimite = $anolimite."-".$meslimite."-".$dialimite." ".$horafinal;
 	
 	$activa = "1";
 	
 	$sql = "INSERT INTO subastas (vehiculo, fechalimite, fechacreacion, activa)
	 			VALUES ('$vehiculo', '$fechalimite', now(), '$activa')";
	 	
	if (!(mysql_query($sql,$conexion)))
	{
		die('Error: '.mysql_error());
	}
 	else
 	{
 		$idsubasta = mysql_insert_id();
 			
 		$sql2 = mysql_query("UPDATE vehiculos SET subasta = '$idsubasta' WHERE id = '$vehiculo'");
 	} 	

 	echo "Vehiculo Insertado<br><br>";
	echo "<a href='vehiculos.php'><img src='back.jpg' alt='Home' width='30' height='30'></a>";
	include("footer.php");
}

// Borrado de un Registro
elseif(isset($_POST['deletesubmit']))
{
	$vid = $_POST['vid'];
	$query = mysql_query("DELETE FROM vehiculos WHERE id = '$vid'");

 	$subasta = $_POST['idsubasta'];
 	$sql = mysql_query("UPDATE subastas SET activa = '0' WHERE id = '$subasta'");
 	$sql2 = mysql_query("UPDATE vehiculos SET subasta = '' WHERE id = '$vid'");

	echo "Vehiculo Borrado<br><br>";
	echo "<a href='vehiculos.php'><img src='back.jpg' alt='Home' width='30' height='30'></a>";
	include("footer.php");
}

// Formularios de Insercion y Borrado
else
{
	echo "Insertar Registro:<br>";		
	echo "<p><form method='post' action='?'>";
	echo "Matricula: <input name='matricula' type='text'><br>";
	echo "Marca: <input name='marca' type='text'><br>";
	echo "Modelo: <input name='modelo' type='text'><br>";
	echo "Combustible: <input name='combustible' type='text'><br>";
	echo "Potencia: <input name='potencia' type='text'><br>";
	echo "Cilindrada: <input name='cilindrada' type='text'><br>";
	echo "Num Puertas: <input name='carroceria' type='text'><br>";
	echo "Plazas: <input name='plazas' type='text'><br>";
	echo "Color: <input name='color' type='text'><br>";
	echo "Kilometros: <input name='kilometros' type='text'><br>";
	echo "PVP: <input name='pvp' type='text'><br>";
	echo "Uso Anterior: <input name='usoanterior' type='text'><br>";
	echo "Extras: <input name='extras' type='text'><br>";
	echo "Fecha Inicio Subasta: <input type='text' size=2 maxlength='2' name='dialimite'><input type='text' size=4 maxlength='4' name='meslimite'><input type='text' size=4 maxlength='4' name='anolimite'>

	echo "<input type='submit' name='insertsubmit' value='Insertar'>";
	echo "</form></p>";

	echo "<table border='1'>";
	echo "<tr><td colspan ='8' align='center'>Vehiculos Activos</td></tr>";
	echo "<tr><td>Usuario</td><td>Borrar</td></tr>";	

	$query = mysql_query("SELECT * FROM vehiculos");
	$rows = mysql_num_rows($query);
	for ($i=0;$i<$rows;$i++)
	{
		$vehiculosarray = mysql_fetch_array($query);
		$matricula = $vehiculosarray['matricula'];
		$modelo = $vehiculosarray['modelo'];
		$vid = $vehiculosarray['id'];
				
		echo "<tr>";
		echo "<td>".$matricula."</td><td>".$modelo."</td>";
		echo "<td>";
		echo "<form method='post' action='?'>";
		echo "<input type=hidden name='vid' value='$vid'>";
		echo "<input type=submit name='deletesubmit' value='Borrar'>";
		echo "</form>";
		echo "</td>"; 			
		echo "</tr>";
		
	}	
	echo "</table>";	

	include("footer.php");

}
		


?>
