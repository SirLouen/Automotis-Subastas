<?php
/*
 * Created on 18/04/2011
 *
 * automotis  Copyright (C) <2011>  Manuel Camargo
 * This program comes with ABSOLUTELY NO WARRANTY; 
 * This is free software, and you are welcome to redistribute it
 * under certain conditions; Read README file for more information

 */
 
$lang = "es-es";
$base="automotislite";
$host="localhost";
$user="trial";
$password="trial";
$pass="trial";
$adminemail = "mcamargo@peugeotcadiz.com";
$adminname = "Manuel Camargo";
$correomensatek = "mcamargo@peugeotcadiz.com";
$passwordmensatek = "trial";

include("lang/$lang.php");

// Conexion a la base de Datos Automotis

$conexion = mysql_connect($host,$user,$password);
$result = mysql_select_db($base,$conexion) or die("$lang_config_bderror"); 

?>
