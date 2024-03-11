<?php


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
function vpc_ChangePassword_MetaData()
{
    return array(
        'DisplayName' => 'vpc_ChangePassword Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true, // Set true if module requires a server to work
        'DefaultNonSSLPort' => '5985', // Default Non-SSL Connection Port
        'DefaultSSLPort' => '5986', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}


function vpc_ChangePassword(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'username' => $params['serverusername'],
                'passwd' => $params['serverpassword'],
                'domain' => $params['serverhostname'],
                'user' => $params['username'],
                'pass' => $params['password'],
            );

            $postdata = http_build_query($postvars);
            // URL del servidor
            $url = 'https://vps06.xhost.cl/prueba_whmcs/changePass.php';

            // $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepass';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($curl);
            logModuleCall(
                'vpc',
                __FUNCTION__,
                $url . '/?' . $postdata,
                $response
            );
            $response = json_decode($response, true);
            if ($response['status'] == 'OK') {
                $result = 'success';
            } else {
                $result = $response['msj'];
            }
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
    return $result;
}
