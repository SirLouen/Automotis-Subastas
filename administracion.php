<?php
/*
 * Created on 08/05/2011
 *
 * Automotis DMS  Copyright (C) <2011>  mcamargo
 * This program comes with ABSOLUTELY NO WARRANTY; 
 * This is free software, and you are welcome to redistribute it
 * under certain conditions; Read README file for more information

 */

include("config.php");
include("include/gen_functions.php");
include("lang/$lang.php");
session_start();

if ($_SESSION['nivelusuario'] >= 5)
{
	
	if ($_GET['idsubasta'])
	{
		include("infosubastas.php");
		include("footer.php");
	}
	
	else
	{
	
	?>
	
		<table border="1" align="center">
		<tr><td colspan ="6" align="center">Buscador de Subastas</td></tr>
		<tr><td>Num Id</td><td>Fecha Creacion</td><td>Matricula</td><td>Marca</td><td>Modelo</td><td>Estado</td></tr>
	
		<?
		$maxsubastas = 7;
		
		$sql = mysql_query("SELECT * FROM subastas ORDER BY id DESC");
		$filas = mysql_num_rows($sql);
		for ($i=0;$i<$maxsubastas&&$i<$filas;$i++)
		{
			$arraysubastas = mysql_fetch_array($sql);
			$idsubasta = $arraysubastas['id'];
			$activa = $arraysubastas['activa'];
			if ($activa)
				$activa = "ACTIVA";
			else
				$activa = "FINALIZADA";
			
			echo "<tr>";
			echo "<td><a href='administracion.php?idsubasta=".$idsubasta."'>".$idsubasta."</td>";
			echo "<td>".$arraysubastas['fechacreacion']."</td>";
			$vehiculo = $arraysubastas['vehiculo'];
			$sql2 = mysql_query("SELECT * FROM vehiculos WHERE id = '$vehiculo'");
			$arrayvehiculo = mysql_fetch_array($sql2);
			echo "<td>".$arraysubastas['matricula']."</td>";
			echo "<td>".$arrayvehiculo['marca']."</td>";
			echo "<td>".$arrayvehiculo['modelo']."</td>";
			echo "<td>".$activa."</td>";			
			
			echo "</tr>";
		 }
		 
		echo "</table>";
		
		include("footer.php");
	}
}	

?>
