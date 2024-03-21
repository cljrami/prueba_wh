<?php



// if (!defined("WHMCS")) {
//     die("This file cannot be accessed directly");
// }
function vpc_ChangePassword_MetaData()
{
    return array(
        'DisplayName' => 'XHVPC_ChangePassword',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '5985', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '5986', // Default SSL Connection Port
        //'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        //'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}

// Datos a enviar
$serverusername = "olson";
$passwordserver = "123";
$domain = "192.168.5.125";
$user = "123";
$pass = "12345678910";

function vpc_ChangePassword($serverusername, $passwordserver, $domain, $user, $pass)
{
    // Construir el array de datos a enviar
    $postvars = array(
        'username' => $serverusername,
        'passwd' => $passwordserver,
        'domain' => $domain,
        'user' => $user,
        'pass' => $pass,
    );

    // Verificar si la función cURL está disponible en este servidor
    if (!is_callable('curl_exec')) {
        echo "La función cURL no está disponible en este servidor.";
        return;
    }

    // URL de destino
    $url = 'https://vps06.xhost.cl/prueba_whmcs/vpc_ChangePassword.php';

    // Verificar si la URL de destino es válida
    if (!$url) {
        echo "La URL de destino no es válida.";
        return;
    }

    // Inicializar la sesión cURL
    $curl = curl_init();

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
        echo 'La accion cURL falló: ' . curl_error($curl);
    } else {
        echo 'La accion cURL se realizó correctamente.';

        // Imprimir los resultados de las validaciones hechas en el servidor remoto
        echo $response;
    }

    // Cerrar la conexión cURL
    curl_close($curl);
}

//FUNCION VPC_ChangePassweord y se debe usar con params
// function vpc_ChangePassword(array $params) // funcional
// {
//     try {
//         if ($params['server'] == 1) {
//             $postvars = array(
//                 'username' => $params['serverusername'],
//                 'passwd' => $params['serverpassword'],
//                 'domain' => $params['serverhostname'],
//                 'user' => $params['username'],
//                 'pass' => $params['password'],
//             );

//             $postdata = http_build_query($postvars);
//             // URL del servidor
//             $url = 'https://vps06.xhost.cl/prueba_whmcs/changePass.php';

//             // $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepass';
//             $curl = curl_init();
//             curl_setopt($curl, CURLOPT_URL, $url);
//             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//             curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
//             curl_setopt($curl, CURLOPT_POST, true);
//             curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
//             $response = curl_exec($curl);
//             logModuleCall(
//                 'vpc',
//                 _FUNCTION_,
//                 $url . '/?' . $postdata,
//                 $response
//             );
//             $response = json_decode($response, true);
//             if ($response['status'] == 'OK') {
//                 $result = 'success';
//             } else {
//                 $result = $response['msj'];
//             }
//         }
//     } catch (Exception $e) {
//         logModuleCall(
//             'vpc',
//             _FUNCTION_,
//             $params,
//             $e->getMessage(),
//             $e->getTraceAsString()
//         );
//         return $e->getMessage();
//     }
//     return $result;
// }


//FIN




// Llamar a la función para  datos por cURL
vpc_ChangePassword($serverusername, $passwordserver, $domain, $user, $pass);

//ok




// // Construir el array de datos a enviar
// $postvars = array(
//     'username' => $serverusername,
//     'passwd' => $passwordserver,
//     'domain' => $domain,
//     'user' => $user,
//     'pass' => $pass,
// );

// // Inicializar la sesión cURL
// $curl = curl_init();

// // URL de destino
// $url = 'https://vps06.xhost.cl/prueba_whmcs/changepasswordv2.php';

// // Verificar si la ruta del cURL existe
// if (!is_callable('curl_exec')) {
//     echo "La función cURL no está disponible en este servidor.";
//     exit;
// }

// // Verificar si la URL de destino es válida
// if (!$url) {
//     echo "La URL de destino no es válida.";
//     exit;
// }

// // Configurar las opciones de cURL
// curl_setopt($curl, CURLOPT_URL, $url);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
// curl_setopt($curl, CURLOPT_POST, true);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $postvars);

// // Ejecutar la petición cURL y obtener la respuesta
// $response = curl_exec($curl);

// // Verificar si la petición tuvo éxito
// if ($response === false) {
//     echo 'La petición cURL falló: ' . curl_error($curl);
// } else {
//     echo 'La petición cURL se realizó correctamente.';

//     // Imprimir los resultados de las validaciones hechas en el servidor remoto
//     echo $response;
// }

// // Cerrar la conexión cURL
// curl_close($curl);
