<?php
require 'C:\xampp\php\lib\PHPMailer-master\PHPMailerAutoload.php';
//definimos la clase producto
class Producto {
	public $codigo, $nombre, $descripcion, $precio, $categoria;

	//definimos la funcion
	function __construct($codigo, $nombre, $descripcion, $precio, $categoria) {
		$this -> categoria = $categoria;
		$this -> codigo = $codigo;
		$this -> nombre = $nombre;
		$this -> precio = $precio;
		$this -> descripcion = $descripcion;

	}

}

function productos_categoria($categoria) {
	$mostrarProductos = simplexml_load_file('configuracion_xml.xml');
	$devueltos = array();
	foreach ($mostrarProductos -> producto as $pr) {
		if ($pr -> categoria == $categoria) {
			array_push($devueltos, $pr);
		}
	}
	return $devueltos; 
}
//funcion para poner los productos en la tabla y nos de la suma total
function factura_producto(){
	$total = 0;
	$value = 0;
    $mostrarProductos = simplexml_load_file('configuracion_xml.xml');
	foreach ($_SESSION['compras'] as $key => $value) {
	  foreach ($mostrarProductos -> producto as $pr) {
			if ($pr -> codigo == $key) {
			 if ($value != 0) {
				 echo "<tr><td>$pr->codigo</td>";
				 echo "<td>$pr->nombre</td>";
				 echo "<td>$pr->descripcion</td>";
				 echo "<td>$value</td>";
				 echo "<td>$pr->precio</td>";
				 echo "<td>" . $value * $pr -> precio . "</td></tr>";
				$total += $value * $pr -> precio;
				}
			}
		}
				
	}
	
	echo "<tr><td>TOTAL</td>";
	echo "<td colspan='4'></td>";
	echo "<td>$total</td></tr>";
}

//funcion para añadir productos a la lista de la compra
		function lista_productos($key,$value){
			 $mostrarProductos = simplexml_load_file('configuracion_xml.xml');
			 foreach ($mostrarProductos -> producto as $pr){
				if($pr -> codigo == $key){
				if($value !=0){
				echo "<tr>";	
				echo "<td>$pr->codigo</td>";
				echo "<td>$pr->nombre</td>";
				echo "<td>$pr->descripcion</td>";
				echo "<td>$pr->precio</td>";
				echo "<td>$value</td>";
				$url = "borrar_producto.php?codigo=$pr->codigo";
				echo "<td><a href='$url'>borrar_producto</a></td>";
				echo "</tr>";
				}
			}	
		}
	}	
//esta fucion devuelve el sms para luego enviarlo mediante el gmail
function codigo_gmail($cadena) {
	$total = 0;
	$mostrarProductos = simplexml_load_file('configuracion_xml.xml');
	foreach ($_SESSION['compras'] as $key => $value) {
		foreach ($mostrarProductos -> producto as $pr) {
			if ($pr -> codigo == $key) {
				$cadena = $cadena . "Productos: " . $pr -> nombre . " " . "Cantidad: " . $value . " " . "precio:" . $pr -> precio . "\n" . "\n";
				$total += $value * $pr -> precio;
			    }
		}
	}
	$cadena = $cadena . "El total de su factura es:" . $total;
	return $cadena;
}

//funcion para valida el correo
/*function correo_valido($cadena) {
	return preg_match('/[A-Za-z0-9-_]+@[A-Za-z0-9-_]+.[A-Za-z0-9-_]+/', $cadena);
}*/
//funcion para enviar el correo
function mandar_correo($cadena) {
	$campo_correo = $_REQUEST['correo'];
	date_default_timezone_set('Etc/UTC');
	$mail = new PHPMailer;
	$mail -> isSMTP();
	$mail -> SMTPDebug = 0;
	$mail -> Debugoutput = 'html';
	$mail -> Host = 'smtp.gmail.com';
	$mail -> Port = 587;
	$mail -> SMTPSecure = 'tls';
	$mail -> SMTPAuth = true;
	//Ponemos la variable  donde tenemos guardado el usuario y contraseña que ponemos en el txt
	$ruta = "./DatosCorreo.txt";
	$usuarios=coger_datos_correo($ruta);
	$mail -> Username = current($usuarios);
	//Ponemos nuestra contraseña del correo
	$mail -> Password = next($usuarios);
	//desde donde es enviado
	$mail -> setFrom('from@example.com', 'First Last');
	//Establecer un correo de respuesta
	$mail -> addReplyTo('replyto@example.com', 'First Last');
	//ponemos el correo a quien queremos enviar el formulario
	$mail -> addAddress($campo_correo);
	//Establecer un asunto
	$mail -> Subject = 'PHPMailer GMail SMTP test';
	//aqui se pone la variable cadena
	$mail -> Body = $cadena;
	//nos envia el correo o dice que hay un error
	if (!$mail -> send()) {
		echo "Mailer Error: " . $mail -> ErrorInfo;
	} else {
		echo "Mensaje enviado con exito!";

	}

}
?>
<?php
//ponemos esta funcion para que nos coja el usuario y la contraseña del txt
function coger_datos_correo($ruta) {
	$fp = fopen($ruta, 'r');
	$usuarios = array();
	while (!feof($fp)){
		$linea = fgets($fp);
		array_push($usuarios,$linea);
	}
	fclose($fp);
	return $usuarios;
}
?>