<!-- MODULO VPC PARA SER USANDO EN WHCMS-->
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


// Obtiene el orderid /Producto/Servicios Via Session
function vpc_Obtener_Datos()
{
    //$userid = $_GET['userid']; // Asume que los valores vienen de la URL
    //$productselect = $_GET['productselect'];
    $command = 'GetClientsProducts';
    $postData = array(
        'clientid' => $userid,
    );

    $results = localAPI($command, $postData);
    if ($results['result'] == 'success') {
        foreach ($results['products']['product'] as $producto) {
            if ($producto['id'] == $productselect) {
                //$nombreProducto = $producto['name'];
                $dedicatedip = $producto['dedicatedip'];
                //$numeroPedido = $producto['orderid'];
                //$hostname = $producto['serverhostname'];
                //$user = $producto['username'];
                //$pass =  $producto['password'];
                //$ordernumber = $producto['ordernumber'];
                //$regdate =  $producto['regdate'];
                //echo "Número de pedido: $numeroPedido\n";
                //echo "Dirección IP dedicada: $dedicatedip\n";
                //echo "Usuario :$user\n";
                //echo "Password :$pass\n";
                //echo "Server :$hostname\n";
                //echo "Nombre Producto:$nombreProducto\n";
                //echo "Fecha Inicio: $regdate\n";
                //echo "Numero de orden: $ordernumber";
                //global $dedicatedip;
                //  break; // Salir del bucle una vez encontrado el producto

            }
        }
    } else {
        echo "Error al consultar los productos del cliente: " . $results['message'] . "\n";
    }
}
//////Enbvio de Datos a Control
function vpc_ChangePassword($params)
{
    try {
        //$dedicatedip = vpc_Obtener_Datos();
        global $dedicatedip;
        $postvars = array(
            'username' => $params['serverusername'],
            'passwd' => $params['serverpassword'],
            'domain' => $dedicatedip,
            //          'domain' => $dedicated['dedicatedip'],
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
