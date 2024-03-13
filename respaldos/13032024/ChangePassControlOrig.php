//// INICIO >
<?php
function vpc_ChangePassword(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                //'key' => $params['serveraccesshash'],
                'action' => 'udp', //update
                'user' => $params['username'],
                'pass' => $params['password']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepass';
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
///FIN