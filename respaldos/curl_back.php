<?php
// Definir las variables a enviar
$usuario_control = "olson";
$password_control = "123";
$ip_cliente = "192.168.5.125";
$usuario_cliente = "1234";
$password_cliente = "123";

// Crear array con las variables a enviar
$data = array(
    'usuario_control' => $usuario_control,
    'password_control' => $password_control,
    'ip_cliente' => $ip_cliente,
    'usuario_cliente' => $usuario_cliente,
    'password_cliente' => $password_cliente
);

// URL del script en el servidor receptor
$url = 'https://vps06.xhost.cl/prueba_whmcs/changePass.php';

// Inicializar cURL
$ch = curl_init($url);

// Configurar la solicitud cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// Verificación del certificado SSL del host remoto
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// Ejecutar la solicitud y obtener la respuesta
$response = curl_exec($ch);

// Verificar si hubo algún error
if ($response === false) {
    echo 'Error en la solicitud cURL: ' . curl_error($ch);
} else {
    echo 'Respuesta del servidor receptor: ' . $response;
}

// Cerrar la sesión cURL
curl_close($ch);
