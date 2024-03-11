<?php
function pcv_CambioPassword(array $params) // funcional
{
  try {
    if ($params['server'] == 1) {
      //parametros Servidor

      // Definir los datos a enviar a maquina control
      //$ip_cliente = "ip pvc";
      //$usuario_control = "admin pvc";
      //$password_control = "pass admin pvc";
      //$usuario_cliente = "usuario pvc";
      //$password_cliente = "pass usuario pvs";

      $postvars = array(
        ///MODIFICACIONES
        //--PARAMS que se obtienen de WHM--//
        'action' => 'udp',
        //'key' => $params['serveraccesshash'],
        //ADMIN PCV
        'username' => $params['serverusername'],
        'pass' => $params['por_definir'],
        //IP VPC
        'domain' => $params['serverhostname'],
        //CLIENTE PCV
        'user' => $params['username'],
        'pass' => $params['password'],
        //--FIN PARAMS--//
        //FIN MODIFICACIONES
      );
      $postdata = http_build_query($postvars);

      // Construir la URL del servidor
      //original
      //$url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepass';
      //propuesta rapida "Verificar"
      $url = 'https://vps06.xhost.cl/prueba_whmcs/changePass_back.php'; //cual ser la ruta para la url de script en mauina de control
      //$url = 'https://server05.xhost.cl/ruta-a-script';

      // Inicializar cURL
      $curl = curl_init();
      // Configurar la solicitud cURL
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
      // Ejecutar la solicitud y obtener la respuesta
      $response = curl_exec($curl);
      // Verificar si hubo algún error
      if ($response === false) {
        $errorMessage = 'Error en la solicitud cURL: ' . curl_error($curl);
        error_log($errorMessage, 0);
        return $errorMessage;
      } else {
        $debugMessage = 'Respuesta recibida: ' . $response;
        error_log($debugMessage, 0);
        return $response;
      }
      // Cerrar la sesión cURL
      curl_close($curl);
    }
  } catch (Exception $e) {
    $exceptionMessage = 'Error: ' . $e->getMessage();
    error_log($exceptionMessage, 0);
    return $e->getMessage();
  }
}
