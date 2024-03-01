<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}
function cwp8_MetaData()  // funcional
{
    return array(
        'DisplayName' => 'control-webpanel',
        'APIVersion' => '1.1', // Use API Version 1.1
        'RequiresServer' => true,
        'DefaultSSLPort' => '2304', // Default SSL Connection Port
        'ServiceSingleSignOnLabel' => 'Login to Panel as User',
        'AdminSingleSignOnLabel' => 'Login to Panel as Admin',
    );
}
function cwp8_ConfigOptions()  // funcional
{
    return array(
        'PACKAGE-NUMBER' => array(
            'Type' => 'text',
            'Loader' => 'cwp8_LoaderFunction',
            'SimpleMode' => true,
            'Description' => 'Package ID'
        ),
        'Inode' => array(
            'Type' => 'text',
            'Default' => '0',
            'SimpleMode' => true,
            'Description' => 'Limit Inodes, 0 for unlimited'
        ),
        'Nofile' => array(
            'Type' => 'text',
            'Default' => '100',
            'SimpleMode' => true,
            'Description' => 'Limit number of Open Files for account'
        ),
        'Nproc' => array(
            'Type' => 'text',
            'Default' => '40',
            'SimpleMode' => true,
            'Description' => 'Limit number of Processes for account, don\'t use 0 as it will not allow any processes'
        )
    );
}
function cwp8_CreateAccount(array $params)  // funcional
{
    try {

        if ($params['server'] == 1) {
            $postvars = array(
                'package' => $params['configoption1'],
                'domain' => $params['domain'],
                'key' => $params['serveraccesshash'],
                'action' => 'add',
                'username' => $params['username'],
                'user' => $params['username'],
                'pass' => $params['password'],
                'email' => $params['clientsdetails']['email'],
                'inode' => $params['configoption2'],
                'nofile' => $params['configoption3'],
                'nproc' => $params['configoption4'],
                'server_ips' => $params['serverip']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($curl);
            logModuleCall(
                'cwp8',
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
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    return $result;
}
function cwp8_SuspendAccount(array $params)  //  funcional
{
 try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'susp',
                'user' => $params['username']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($curl);
            logModuleCall(
                'cwp8',
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
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $result;
}
function cwp8_UnsuspendAccount(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'unsp',
                'user' => $params['username']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($curl);
            logModuleCall(
                'cwp8',
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
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $result;
}
function cwp8_TerminateAccount(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'del',
                'user' => $params['username'],
                'email' => $params['clientsdetails']['email']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/account';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
            $response = curl_exec($curl);
            $ini = strpos($response, "{");
			$fin = strpos($response, "}");
			$response=substr($response,$ini,$fin+1);
            $response = json_decode($response, true);
            if ($response['status'] == 'OK') {
                $result = 'success';
            } else {
                $result = $response['msj'];
            }
             logModuleCall(
                'cwp8',
                __FUNCTION__,
                $url . '/?' . $postdata,
                $response
            );
        }
    } catch (Exception $e) {
        logModuleCall(
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    return $result;
}
function cwp8_ChangePassword(array $params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'udp',
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
                'cwp8',
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
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $result;
}
function cwp8_ChangePackage(array $params)  //funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'udp',
                'user' => $params['username'],
                'package' => $params['configoption1']
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/changepack';
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
                'cwp8',
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
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $result;
}
function cwp8_ClientArea(array $params) //funcional
{
    try {
    $postvars = array('key' => $params["serveraccesshash"], 'action' => 'list', 'user' => $params["username"], 'timer' => 5);
    $postdata = http_build_query($postvars);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://' . $params["serverhostname"] . ':2304/v1/user_session');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    $answer = curl_exec($curl);
    $arry = (json_decode($answer, true)); //die;F
    $link = $arry['msj']['details'];
    $linkautologin = $link[0]['url'];
    logModuleCall('cwpwhmcs', 'cwp8_LoginLink', 'https://' . $params["serverhostname"] . ':2304/v1/user_session' . $postdata, $answer);
    return "<a href=\"{$linkautologin}\" target=\"_blank\">Login to Control Panel</a>";
    } catch (Exception $e) {
        logModuleCall(
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    return $code;
}
function cwp8_AdminLink($params) //funcional
{
    $code = ' <form action="https://'.$params['serverhostname'].':2087" method="post" target="_blank"><input type="submit" class="btn btn-sm btn-default" name="login" value="Login to CWP©"> </form>  ';
    return $code;
}
function cwp8_LoginLink($params) //funcional
{
   try {
    $postvars = array('key' => $params["serveraccesshash"], 'action' => 'list', 'user' => $params["username"], 'timer' => 5);
    $postdata = http_build_query($postvars);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://' . $params["serverhostname"] . ':2304/v1/user_session');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    $answer = curl_exec($curl);
    $arry = (json_decode($answer, true)); //die;F
    $link = $arry['msj']['details'];
    $linkautologin = $link[0]['url'];
    return "<a href=\"{$linkautologin}\" target=\"_blank\" >Login to Control Panel</a>";
    } catch (Exception $e) {
        logModuleCall(
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
    }
    return $code;
}
function cwp8_LoaderFunction($params) // funcional
{
    try {
        if ($params['server'] == 1) {
            $postvars = array(
                'key' => $params['serveraccesshash'],
                'action' => 'list'
            );
            $postdata = http_build_query($postvars);
            $url = 'https://' . $params['serverhostname'] . ':' . $params['serverport'] . '/v1/packages';
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
                'cwp8',
                __FUNCTION__,
                $url . '/?' . $postdata,
                $response
            );
        }
        if (curl_error($curl)) {
            throw new Exception('Unable to connect: ' . curl_errno($curl) . ' - ' . curl_error($curl));
        } elseif (empty($response)) {
            throw new Exception('Empty response');
        }
        curl_close($curl);
        $packages = json_decode($response, true);
        $packageNames = [];
        foreach ($packages['msj'] as $key => $package) {
            $packageNames[$package['id']] = ucfirst($package['package_name']);
        }
        if (is_null($packageNames)) {
            throw new Exception('Invalid response format');
        }
    } catch (Exception $e) {
        logModuleCall(
            'cwp8',
            __FUNCTION__,
            $params,
            $e->getMessage(),
            $e->getTraceAsString()
        );
        return $e->getMessage();
    }
    return $packageNames;
}
?>