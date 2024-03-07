<?php
function vpc_ChangePassword(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            // Definir los datos a enviars
            //$usuario_control = "olson";
            //$password_control = "123";
            //$ip_cliente = "192.168.5.125";
            //$usuario_cliente = "123";
            //$password_cliente = "123456789";

            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'udp',
                // 'user' => $params['username'],
                // 'pass' => $params['password'],
                'usuario_control' => $params['usuario_control'],
                'password_control' => $params['password_control'],
                'ip_cliente' => $params['ip_cliente'],
                'usuario_cliente' => $params['usuario_cliente'],
                'password_cliente' => $params['password_cliente'],
            );
            $postdata = http_build_query($postvars);

            // Construir la URL del servidor
            $url = 'https://vps06.xhost.cl/prueba_whmcs/changePass.php';
            // $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepass';

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
                logModuleCall(
                    'vpc',
                    __FUNCTION__,
                    $url . '/?' . $postdata,
                    'Error en la solicitud cURL: ' . curl_error($curl),
                    ''
                );
                return 'Error en la solicitud cURL: ' . curl_error($curl);
            } else {
                logModuleCall(
                    'vpc',
                    __FUNCTION__,
                    $url . '/?' . $postdata,
                    $response,
                    ''
                );
                return $response;
            }

            // Cerrar la sesión cURL
            curl_close($curl);
        }
    } catch (Exception $e) {
        logModuleCall(
            'vpc',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
}
