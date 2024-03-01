<?php
// Datos para enviar al script remoto
$admin_user = 'olson';
$admin_pass = '123';
$ip_destino = '192.168.1.156'; // Dirección IP de la máquina remota
$target_user = '123';
$user_pass = '123';

// URL del script remoto
$url = 'https://vps06.xhost.cl/prueba_whmcs/prueba_ok.php';

// Datos a enviar en la solicitud POST
$data = array(
    'admin_user' => $admin_user,
    'admin_pass' => $admin_pass,
    'ip_destino' => $ip_destino,
    'target_user' => $target_user,
    'user_pass' => $user_pass
);

// Inicializar cURL
$ch = curl_init();

// Establecer la URL de destino
curl_setopt($ch, CURLOPT_URL, $url);

// Establecer la solicitud POST
curl_setopt($ch, CURLOPT_POST, 1);

// Establecer los datos a enviar
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Seguir redireccionamientos si los hay
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Establecer la opción para recibir la respuesta como cadena
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Ejecutar la solicitud y obtener la respuesta
$response = curl_exec($ch);

// Verificar si ocurrió algún error
if ($response === false) {
    echo 'Error en la solicitud cURL: ' . curl_error($ch);
} else {
    echo 'Respuesta del servidor remoto: ' . $response;
}

// Cerrar la sesión cURL
curl_close($ch);
