<?php

// Definir las variables a enviar
$postvars = array(
    'admin_user' => 'usuario_admin',
    'admin_pass' => 'contraseña_admin',
    'ip_destino' => 'dirección_ip_destino',
    'target_user' => 'usuario_destino',
    'user_pass' => 'contraseña_destino'
);

// URL del script en el otro servidor
$url = 'https://vps06.xhost.cl/prueba_whmcs/prueba_ok.php';

// Inicializar cURL
$ch = curl_init();

// Establecer la URL a la que se va a enviar la solicitud
curl_setopt($ch, CURLOPT_URL, $url);

// Establecer que se espera una respuesta como resultado de la solicitud
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Establecer la solicitud como POST
curl_setopt($ch, CURLOPT_POST, true);

// Convertir el array de variables en formato de cadena para enviarlo como datos POST
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postvars));

// Ejecutar la solicitud cURL y almacenar la respuesta en una variable
$response = curl_exec($ch);

// Verificar si hay errores durante la ejecución de la solicitud cURL
if ($response === false) {
    $error_message = 'Error cURL: ' . curl_error($ch);
    // Guardar el error en el archivo de registro
    file_put_contents('resultados.txt', $error_message . PHP_EOL, FILE_APPEND);
} else {
    // Si no hay errores, guardar la respuesta del servidor en el archivo de registro
    file_put_contents('resultados.txt', 'Respuesta del servidor: ' . $response . PHP_EOL, FILE_APPEND);
}

// Cerrar la sesión cURL
curl_close($ch);
