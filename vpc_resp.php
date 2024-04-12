<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

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

//Obtener DatosMOD
function vpc_Obtener_Datos()
{
    ////Se obtiene ClientID de Manera Automatica
    $clientID = $_SESSION['uid'];
    ////
    $command = 'GetClientsProducts';
    $postData = array(
        'clientid' => $clientID,
    );
    $results = localAPI($command, $postData);
    if ($results['result'] == 'success') {
        $productos = $results['products']['product'];
        foreach ($productos as $producto) {
            if ($producto['orderid'] == '2399') { // Filtrar por número de pedido 2399
                $nombreProducto = $producto['name'];
                $dedicatedip = $producto['dedicatedip'];
                $numeroPedido = $producto['orderid']; // Se usa orderid
                return $dedicatedip;
            }
        }
    } else {
        echo "Error al obtener la información del producto: " . $results['message'] . "\n";
    }
}
// ////////////////////////////////////////////////////////////////////////////////
// $command = 'GetClientsProducts';
// $postData = array(
//     'clientid' => $clientID, 
//   // ID del cliente consultado
// );

// $results = localAPI($command, $postData,);

// if ($results['result'] == 'success') {
//     $products = $results['products']['product'];
//     foreach ($products as $product) {
//         $orderid = $product['orderid']; // Obtener el orderid del producto
//         $dedicatedid = $product['dedicatedip']; // Obtener el dedicatedid del producto
//       	$name = $product['name'];
//         echo "Producto: $name  $orderid ($dedicatedid)\n\n\n";
//     }
// } else {
//     echo "Error al consultar los productos del cliente: " . $results['message'] . "\n";
// }
// ////////////////////////////////////////////////////////////////////////////








//function vpc_Obtener_Datos()
//{
////Se obtiene ClientID de Manera Automatica
//   $clientID = $_SESSION['uid'];
////
//  $command = 'GetClientsProducts';
//    $postData = array(
//      'clientid' => $clientID,
//    );
//    $results = localAPI($command, $postData);
//    if ($results['result'] == 'success') {
//        $productos = $results['products']['product'];
//        foreach ($productos as $producto) {
//            if ($producto['orderid'] == '2399') { // Filtrar por número de pedido 2399
//                $nombreProducto = $producto['name'];
//                $dedicatedip = $producto['dedicatedip'];
//                $numeroPedido = $producto['orderid']; // Se usa orderid
//                return $dedicatedip;              
//      }
//  }
// 	} else {
//      echo "Error al obtener la información del producto: " . $results['message'] . "\n";
// }
//
// Función para realizar la solicitud cURL

function vpc_ChangePassword($params)
{

    try {

        $dedicatedip = vpc_Obtener_Datos();
        $postvars = array(
            'username' => $params['serverusername'],
            'passwd' => $params['serverpassword'],
            'domain' => $dedicatedip,
            'user' => $params['username'],
            'pass' => $params['password']
        );
        $postdata = http_build_query($postvars);
        $url = 'https://vps06.xhost.cl/prueba_whmcs/post.php';
        $curl = curl_init();
        if (!$curl) {
            throw new Exception('No se pudo inicializar cURL');
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //true/false
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); //true/false
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $response = curl_exec($curl);
        $response = json_decode($response);
        if (curl_error($curl)) {
            throw new Exception('Unable to connect to the server:' . $url . ' - ' . curl_errno($curl) . ' - ' . curl_error($curl));
        }
        curl_close($curl);

        return $response;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}
function vpc_CreateAccount($params)
{
    return true;
}
function vpc_SuspendAccount($params)
{
    return true;
}
function vpc_UnsuspendAccount($params)
{
    return true;
}
function vpc_TerminateAccount($params)
{
    return true;
}
