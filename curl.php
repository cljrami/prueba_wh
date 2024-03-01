<?php

// URL del script remoto
$url = 'https://vps06.xhost.cl/prueba_whmcs/prueba_ok.php';

// Datos a enviar en la solicitud POST
$data = array(
    'olson' => $admin_user,
    '123' => $admin_pass,
    '192.168.5.125' => $ip_destino,
    '132' => $target_user,
    '123' => $user_pass
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
    $error_message = 'Error en la solicitud cURL: ' . curl_error($ch);

    // Registrar el error en el archivo de registro
    $log_entry = "[" . date('Y-m-d H:i:s') . "] Error: $error_message\n";
    file_put_contents('registro.txt', $log_entry, FILE_APPEND | LOCK_EX);

    echo $error_message;
} else {
    echo 'Respuesta del servidor remoto: ' . $response;
}

// Cerrar la sesión cURL
curl_close($ch);
