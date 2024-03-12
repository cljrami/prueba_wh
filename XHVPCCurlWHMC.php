<?php
// if (!defined("WHMCS")) {
//     die("This file cannot be accessed directly");
// }
function vpc_ChangePassword_MetaData()
{
    return array(
        'DisplayName' => 'Modulo vpc_ChangePassword ',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '5985', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '5986', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}


// Datos a enviar
$serverusername = "olson";
$passwordserver = "123";
$domain = "192.168.5.125";
$user = "123";
$pass = "12345678910";



// Construir el array de datos a enviar
$postvars = array(
    'username' => $serverusername,
    'passwd' => $passwordserver,
    'domain' => $domain,
    'user' => $user,
    'pass' => $pass,
);







// Inicializar la sesión cURL
$curl = curl_init();

// URL de destino
$url = 'https://vps06.xhost.cl/prueba_whmcs/changepasswordv2.php';

// Configurar las opciones de cURL
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postvars);

// Ejecutar la petición cURL y obtener la respuesta
$response = curl_exec($curl);

// Verificar si la petición tuvo éxito
if ($response === false) {
    echo 'La petición cURL falló: ' . curl_error($curl);
} else {
    echo 'La petición cURL se realizó correctamente.';

    // Imprimir los resultados de las validaciones hechas en el servidor remoto
    echo $response;
}

// Cerrar la conexión cURL
curl_close($curl);
