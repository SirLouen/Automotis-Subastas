<?php
////////////////////////////////////////////////////
// CONEXIÓN A MENSATEK POR HTTP/S DESDE PHP
// versión PHP 
// versión API 4.0                       
// Última modificación 3 Septiembre 2006
////////////////////////////////////////////////////


// El puerto por defecto es 3377, se usa para evitar influencia de proxies 
// si no puede utilizar el 3377 por problemas de firewall utilice el puerto 80

/////////////////////////////////////////////////////////////////
// Definiciones necesarias
/////////////////////////////////////////////////////////////////
/* define('G_PUERTO',80); Si tiene un firewall que no deja comunicaciones en el 3377, puede utilizar el puerto 80 */
define('G_PUERTO',3377);

/////////////////////////////////////////////////////////////////
// Si desea comunicaciones seguras SSL debe tener activada la extensión SSL
// y quitar los comentarios de las dos siguientes sentencias
/////////////////////////////////////////////////////////////////
/* define('G_PUERTO',443);  Si tiene un firewall que no deja comunicaciones en el 3378, puede utilizar el puerto 443*/
// define('G_PUERTO',3378);
// define('G_DIR','ssl://atgroup1.securesites.net');

 
class cMensatek
{
    var $_correo;
    var $_pass;
    var $Res=array();
    var $Creditos=0;
    var $Resultado=0;
    var $idMensaje=0;
    
    // Constructor
    function cMensatek($correo,$pass)
    {
        $this->_correo=$correo;
        $this->_pass=$pass;
    }
    

    //////////////////////////////////////////////////////
    // OBTIENE EL NÚMERO DE CRÉDITOS RESTANTES DEL USUARIO
    // DEVUELVE:
    //  Float en $this->Creditos correspondiente al número de créditos en la cuenta.
    ///////////////////////////////////////////////////////

    function creditos()
    {
       $res=$this->_conecta("","/v4/creditos.php","Cred");
       $this->Creditos=$res["Cred"];
       return $this->Creditos;
    }

    //////////////////////////////////////////////////////
    // ENVÍA MENSAJES A MÓVILES
    // - Valores: Array con todas o alguna de las siguientes variables
    //      Destinatarios: Móvil/Móviles al/a los que se envía el mensaje, de la forma PrefijoTelefono (Ej:346000000 ó para varios destinatarios
    //           346000000;3519760000;443450000) separados por punto y coma ';'
    //      Mensaje: Mensaje que se envía
    //      Remitente: (Por defecto "") Es el teléfono, nombre de la empresa o persona que envía. Sólo válido si se envía mensaje profesional
    //            Si se deja en blanco y se selecciona mensaje profesional, se enviará desde el teléfono móvil registrado por el usuario qu envía en Mensatek
    //            ATENCIÓN: Si es alfanumérico el Máximo es de 11 caracteres.
    //      Fecha: Fecha en la que queda progrmado el envío, el mensaje se enviará en esa fecha. Por defecto "" que significa enviar ahora. Formato: Año-Mes-dia hora:minuto
    //          La referencia horaria es GMT+1 (Zona horaria de España)
    //      Flash: 0=No, 1=Sí
    //      Report: 0=No, 1=Sí  (1=se envía report de entrega al correo electrónico)
    //      Descuento: 0=No, 1=Sí 
    //      EmailReport: Correo electrónico que recibirá el report. Si no se utiliza y se ha seleccionado Report=1, se enviará al correo registrado como usuario en MENSATEK.(ATENCIÓN: Debe ser un correo válido). 
    //                   ::Atención::Si desea que se envíe un correo de report personalizado con su nombre de dominio contacte con el Departamento de Soporte
    //      Descuento: Se hará un 10% de descuento (en créditos) si incluye en el mensaje [MENSATEK.com]
    // DEVUELVE: Un array
    //  Res: Int
    //      >0 correspondiente al número de mensajes enviados.
    //      -1 Error de autenticación
    //      -2 no hay créditos suficientes.
    //      -3 Error en los datos de la llamada
    //  Msgid: Int
    //      identificador del mensaje enviado para utilizar en el report
    //  Cred: Float
    //      número de créditos que le restan.
    ///////////////////////////////////////////////////////

    function enviar($Valores)
    {
       $string="";
       foreach ($Valores as $var=>$valor) $string.="&".$var."=".urlencode($valor);
       $res=$this->_conecta($string,"/v4/enviar.php","Res;Msgid;Cred");
       $this->Creditos=$res["Cred"];
       $this->idMensaje=$res["Msgid"];
       return $res;
    }
    
    //////////////////////////////////////////////////////
    // REPORT DE ENVÍO
    // MsgId: Identificador de mensaje devuelto por la función de envío.
    // DEVUELVE:
    //  - Entero con el Número de reports
    //  - Carga Array en $this->Res con n valores (tantos como teléfonos de destino) del tipo 
    //         $this->Res[n]["Fecha"] Fecha/Hora de envío
    //         $this->Res[n]["Movil"] Móvil destino
    //         $this->Res[n]["Tiempo"] Tiempo (en segundos) que tardó en entregarse el mensaje al móvil (normalmente entre 2 s 20 segundos si el móvil está encendido).
    //         $this->Res[n]["Resultado"] String con el resultado del envío (entregado, móvil erróneo, etc...). Se compone de:
    //          1.- Resultado String(Mensaje entregado, esperando entrega, etc…)
    //          2.- Link a imagen que varía en función del resultado, sirve para utilizarlas como ayuda rápida al cliente (ver ejemplo en reports en Mensatek).
    //          El formato es: <img src="pix/bverde.gif" hspace="2" border="0">
    //              Posibles colores:
    //              bverde.gif (entregado),
    //              broja.gif (cualquier error),
    //              bnaranja.gif (entregado a red),
    //              bazul.gif (programado o esperando entrega).
    ///////////////////////////////////////////////////////

    function report($MsgId)
    {
        $res=$this->_conecta("&idM=".urlencode($MsgId),"/v4/report.php","");
        $n=0;
        $informes=explode("<Informe>",$res[0]);
        if (isset($informes[0])) foreach ($informes as $informe)
        {
           $resultado=explode("+",$informe);
           if (isset($resultado[2]))
           {
               $this->Res[$n]["Fecha"]=$resultado[0];
               $this->Res[$n]["Movil"]=$resultado[1];
               $this->Res[$n]["Tiempo"]=$resultado[2];
               $this->Res[$n]["Resultado"]=str_replace("</Informe>","",$resultado[3]);
               $n++;
           }  
        }

       return $n;
    }
    
    //////////////////////////////////////////////////////
    // SUBVENCIONAR CRÉDITOS A OTRA CUENTA DE USUARIO
    // CorreoDestino: Correo del usuario destino de los créditos.
    // Creditos: Número de crédtos a añadir al usuario  
    // DEVUELVE:
    //  - Si >0 Entero con el Número de créditos efectivamente añadidos al usuario o error
    //  - Si <0  
    //   -1 Errror de usuario
    //   -2 No hay suficientes créditos
    //   -3 Correo de destino no existe
    //   -4 Créditos <0
    ///////////////////////////////////////////////////////

    function subvencionar($CorreoDestino,$Creditos)       
    {
        $res=$this->_conecta("&CorreoDest=".urlencode($CorreoDestino)."&Creditos=".urlencode($Creditos),"/v4/subvencionar.php","Res");
        
        return $res["Res"];
    }
    

    // Funciones internas
    function _conecta($args,$dir,$regs)
    {
        $args="Correo=".urlencode($this->_correo)."&Passwd=".urlencode($this->_pass).$args;
        if (function_exists("curl_init"))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://api.mensatek.com".$dir."?".$args);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
            curl_setopt($ch,CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_PORT, G_PUERTO); 

            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

            curl_setopt($ch, CURLOPT_TIMEOUT, 50); 

            $sub=curl_exec ($ch);
           
            curl_close ($ch);
            $return=$this->_respuesta($sub,$regs);
            
        }
        else
        {
            $fp = @fsockopen ("api.mensatek.com", G_PUERTO, $errno, $errstr, 30);
            if (!fp) echo "Su sistema no permite trabajar con sockets, active la funcionalidad de sockets en PHP para utilizar la librería";
            else
            {
               $string="GET ".$dir."?".$args;
               fputs($fp, $string."  HTTP/1.1\r\n");
               fputs($fp, "Host: mensatek.com\r\n");
               fputs($fp, "Connection: close\r\n\r\n");
               $sub="";
               while (!feof($fp)) $sub.=fgets($fp, 128);
                                   
               fclose($fp);
               $return=$this->_respuesta($sub,$regs);
               
            }
        } 
        return $return;
    }
    
    function _respuesta($sub,$regs)
    {        
        $return=@array();
        if ($regs=="") $return[0]=$sub;
        else    
        {
            $reg=explode(";",$regs);
            foreach ($reg as $r) if ($r!=""&&($sub=strstr($sub,$r.":"))!==false)
            {
                if (($pos=strpos($sub,"\r\n"))!==false) $return[$r]=substr($sub,strlen($r)+1,$pos-strlen($r));
                else $return[$r]=substr($sub,strlen($r)+1);
            }
        }
        return $return;
        
    }
}
?>