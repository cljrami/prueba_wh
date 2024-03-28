<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
//use WHMCS\ClientArea;
use WHMCS\Database\Capsule;


function vpc_MetaData() // Función que define los datos del módulo almacenado en las opciones generales
{
    return array(
        'DisplayName' => 'VPC',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'DefaultNonSSLPort' => '5985',
        'DefaultSSLPort' => '5986',
    );
}

// Función para configurar las opciones del módulo
function vpc_ConfigOptions()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'vpc_Module',
        ),
    );
}


//FUNCION GETDEDICATED_IP 26032024

////////////////////

////////////////////
$command = 'GetClientsProducts';
$postData = [
    'clientid' => '47', // Asegúrate de usar un ID de cliente válido
];
//$adminUsername = 'jramirez'; // Opcional para WHMCS versión 7.2 y posterior

$results = localAPI($command, $postData,);

if ($results['result'] == 'success') {
    // Recorre los productos del cliente
    foreach ($results['products']['product'] as $product) {
        // Verifica si existe un valor para dedicatedip
        if (!empty($product['dedicatedip'])) {
            echo "La dirección IP dedicada del producto " . $product['name'] . " es: " . $product['dedicatedip'] . "<br>";
        }
    }
} else {
    echo "Hubo un error al obtener los productos: " . $results['message'];
}

//


// Función para realizar la solicitud cURL
function vpc_ChangePassword($params)
{
    try {

        $postvars = array(
            'username' => $params['serverusername'],
            'passwd' => $params['serverpassword'],
            //'domain' => $ips[0]['ip'], 
            //'domain' => $params['dedicatedip'],
            'domain' => $params['serverip'],
            'user' => $params['username'],
            'pass' => $params['password']
        );

        $postdata = http_build_query($postvars);
        //$url = 'https://vps06.xhost.cl/prueba_whmcs/vpc_ChangePassword_bk.php';
        $url = 'https://vps06.xhost.cl/prueba_whmcs/post.php';

        $curl = curl_init();

        if (!$curl) {
            throw new Exception('No se pudo inicializar cURL');
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $response = curl_exec($curl);


        if ($response === false) {
            throw new Exception('La acción cURL falló: ' . curl_error($curl));
        }

        curl_close($curl);

        // Guardar el resultado en el archivo de registro
        // $logMessage = date('Y-m-d H:i:s') . " - Resultado: $response\n";
        // file_put_contents('log.txt', $logMessage, FILE_APPEND);
        //echo 'La acción cURL se realizó correctamente.';
        //  echo $response;

        //   echo '<script>';
        //   echo 'console.log("Parámetros enviados por cURL:", ' . json_encode($params) . ');';
        //   echo '</script>';
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        return "Error";

        //   echo '<script>';
        //   echo 'console.log("Error en cURL:", ' . json_encode($e->getMessage()) . ');';
        //   echo '</script>';
    }

    return $response;
}

// Obtener los parámetros de WHMCS


// Crear un log para resultados en log.txt
//$logMessageInit = date('Y-m-d H:i:s') . " - Inicio de la solicitud\n";
//file_put_contents('log.txt', $logMessageInit, FILE_APPEND);

// Verificar si se obtienen datos en $params
//var_dump($params);
//$params = $_REQUEST;
// Llamar a la función para realizar la solicitud cURL
//vpc($params);
