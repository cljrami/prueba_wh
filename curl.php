<?php
// Define las variables
$admin_user = 'olson';
$admin_pass = '123';
$ip_destino = '192.168.5.125';
$target_user = '123';
$user_pass = '555';

// Define la URL del script
$url = 'https://vps06.xhost.cl/prueba_whmcs/prueba_ok.php';

// Crea un arreglo asociativo con las variables
$post_data = [
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

// Guarda la respuesta en results.txt
file_put_contents('results.txt', $response);

// Imprime un mensaje de éxito
echo "Variables enviadas mediante cURL a $url. Respuesta guardada en results.txt.";
