<?php

include("config.php");
include("include/gen_functions.php");
include("lang/$lang.php");
session_start();

// Insercion de un nuevo Registro
if(isset($_POST['insertsubmit']))
{
	$usuario = $_POST['usuario'];
	$password = $_POST['password'];
	$password = md5($password);
	$nombre = $_POST['nombre'];
	$email = $_POST['email'];
	$telefono = $_POST['modelo'];
	$nivelusuario = $_POST['nivelusuario'];

	$query = mysql_query("INSERT INTO usuarios (`usuario`, `password`, `nombre`, `nivelusuario`, `email`, `telefono`) 
		VALUES ('$usuario', '$password', '$nombre', '$nivelusuario', '$email', '$telefono')");
	
 	echo "Usuario Insertado<br><br>";
	echo "<a href='usuarios.php'><img src='back.jpg' alt='Home' width='30' height='30'></a>";
	include("footer.php");
}
// Borrado de un Registro
elseif(isset($_POST['deletesubmit']))
{
	$userid = $_POST['userid'];
	$query = mysql_query("DELETE FROM usuarios WHERE userid = '$userid'");
	echo "Usuario Borrado<br><br>";
	echo "<a href='usuarios.php'><img src='back.jpg' alt='Home' width='30' height='30'></a>";
	include("footer.php");
}
// Formularios de Insercion y Borrado
else
{
	echo "Insertar Registro:<br>";		
	echo "<p><form method='post' action='?'>";
	echo "Usuario: <input name='usuario' type='text'><br>";
	echo "Contrase&ntilde;a: <input name='password' type='password'><br>";
	echo "Nombre: <input name='nombre' type='text'><br>";
	echo "Email: <input name='email' type='text'><br>";
	echo "Telefono: <input name='telefono' type='text'><br>";
	echo "Nivel Usuario: <input name='nivelusuario' type='text'><br>";
	echo "<input type='submit' name='insertsubmit' value='Insertar'>";
	echo "</form></p>";

	echo "<table border='1'>";
	echo "<tr><td colspan ='8' align='center'>Usuarios Activos</td></tr>";
	echo "<tr><td>Usuario</td><td>Borrar</td></tr>";	

	$query = mysql_query("SELECT * FROM usuarios");
	$rows = mysql_num_rows($query);
	for ($i=0;$i<$rows;$i++)
	{
		$usuariossarray = mysql_fetch_array($query);
		$nombre = $usuariossarray['nombre'];
		$userid = $usuariossarray['userid'];
				
		echo "<tr>";
		echo "<td>".$nombre."</td>";
		echo "<td>";
		echo "<form method='post' action='?'>";
		echo "<input type=hidden name='userid' value='$userid'>";
		echo "<input type=submit name='deletesubmit' value='Borrar'>";
		echo "</form>";
		echo "</td>"; 			
		echo "</tr>";
		
	}	
	echo "</table>";	

	include("footer.php");

}
		


?>
