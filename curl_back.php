<?php
// Define las variables
//  $usuario_control = "olson";
//  $password_control = "123";
//  $ip_cliente = "192.168.5.125";
//  $usuario_cliente = "123";
//  $password_cliente = "1234";
$admin_user = 'olson';
$admin_pass = '123';
$ip_destino = '192.168.5.125';
$target_user = '123';
$user_pass = 'loli2000'; // Ejemplo de contraseña

// Define la URL del script remoto
$url = 'https://vps06.xhost.cl/prueba_whmcs/changePass.php';

// Crea un arreglo asociativo con las variables
$post_data = [
    //     'usuario_control' => $usuario_control,
    //     'password_control' => $password_control,
    //     'iip_cliente ' => $ip_cliente,
    //     'usuario_cliente' => $usuario_cliente,
    //     'password_cliente' => $password_cliente,
    'admin_user' => $admin_user,
    'admin_pass' => $admin_pass,
    'ip_destino' => $ip_destino,
    'target_user' => $target_user,
    'user_pass' => $user_pass,
];

// Configura la solicitud cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Ejecuta la solicitud y obtén la respuesta
$response = curl_exec($ch);

// Cierra la conexión cURL
curl_close($ch);


// Verifica si el cambio de contraseña se realizó con éxito
if (strpos($response, "Cambio de contraseña realizado con éxito") !== false) {
    echo '<script type="text/javascript">';
    echo 'alert("Cambio de contraseña realizado con éxito.");';
    echo '</script>';
} elseif (strpos($response, "El usuario especificado no existe en el equipo remoto") !== false) {
    echo '<script type="text/javascript">';
    echo 'alert("Error: El usuario especificado no existe en el equipo remoto.");';
    echo '</script>';
} else {
    echo '<script type="text/javascript">';
    echo 'alert("Error: No se pudo cambiar la contraseña.");';
    echo '</script>';
}

// Obtiene la información del log remoto
$log_url = 'https://vps06.xhost.cl/prueba_whmcs/log.txt';
$log_content = file_get_contents($log_url);

// Guarda la información del log remoto en un archivo local
file_put_contents('log.txt', $log_content);