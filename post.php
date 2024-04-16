<!-- MODULO CHANGEPASSWORD  PARA SER USANDO EN CONTROL-->

<?php
ini_set("date.timezone", "America/Santiago");
// Función para obtener la IP remota del cliente
function getIp(): string
{
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { // Soporte de Cloudflare
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (preg_match('/^(?:127|10)\.0\.0\.[12]?\d{1,2}$/', $ip)) {
            if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
    } else {
        $ip = '127.0.0.1';
    }
    if (in_array($ip, ['::1', '0.0.0.0', 'localhost'], true)) {
        $ip = '127.0.0.1';
    }
    $filter = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    if ($filter === false) {
        $ip = '127.0.0.1';
    }

    return $ip;
}

// Función para realizar ping a una dirección IP
function ping($domain)
{
    $status = -2; // Definimos código -2 solo para inicializar variable. Si la función lo entrega es que ni siquiera tiene habilitada la función fsockopen
    $puerto = 5985; // Puerto de WinRM

    // Intentar realizar un ping a la dirección IP
    $file = @fsockopen($domain, $puerto, $errno, $errstr, 10);

    // Verificar si se estableció la conexión
    if (!$file) {
        // Si no se pudo establecer la conexión, retornar código -1
        $status = -1; // Si fsockopen falla, retornamos error -1
    } else {
        // Si se estableció la conexión, cerrarla y retornar código 0
        fclose($file);
        $status = 0; // Si la máquina existe, hace ping al puerto y está disponible, retornamos código de ejecución 0.
    }

    return $status;
}

// Función para ejecutar comandos de PowerShell
function PowerShellCC($serverusername, $serverpassword, $domain, $user, $pass)
{
    // Construir el comando de PowerShell
    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String $serverpassword -AsPlainText -Force; ";
    $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $serverusername, \$securePass; ";
    $command .= "\$result = Invoke-Command -ComputerName $domain -Credential \$cred -ScriptBlock { ";
    $command .= "param(\$user, \$pass); ";
    $command .= "if(Get-LocalUser -Name \$user -ErrorAction SilentlyContinue) { ";
    $command .= "Set-LocalUser -Name \$user -Password (ConvertTo-SecureString -AsPlainText \$pass -Force); ";
    $command .= "return '0'; ";
    $command .= "} else { return '-1'; } ";
    $command .= "} -ArgumentList $user, '$pass'; ";
    $command .= "echo \$result; ";
    $command .= "\"";

    // Ejecutar el comando de PowerShell y obtener la salida
    $output = shell_exec($command);

    // Crear el mensaje de registro
    //$logMessage = date('Y-m-d H:i:s') . " - UsuarioControl: $serverusername - UsuarioCliente: $user - Resultado: $output\n";

    // Guardar el mensaje en el archivo de registro debug_log.log
    //file_put_contents('debug_log.log', $logMessage, FILE_APPEND);

    return $output;
}

// Recibir los datos enviados por cURL
function procesar()
{
    // Recibir los datos enviados por cURL
    $serverusername = isset($_REQUEST['username']) ? $_REQUEST['username'] : '';
    $serverpassword = isset($_REQUEST['passwd']) ? $_REQUEST['passwd'] : '';
    $domain = isset($_REQUEST['domain']) ? $_REQUEST['domain'] : '';
    $user = isset($_REQUEST['user']) ? $_REQUEST['user'] : '';
    $pass = isset($_REQUEST['pass']) ? $_REQUEST['pass'] : '';

    // Verificar si se recibieron todos los parámetros esperados
    if (!empty($serverusername) && !empty($serverpassword) && !empty($domain) && !empty($user) && !empty($pass)) {
        // Verificar la disponibilidad de la dirección IP y el puerto
        $pingStatus = ping($domain);

        // Si el ping se realizó correctamente
        if ($pingStatus === 0) {
            // Ejecutar comandos de PowerShell para cambiar la contraseña
            $resultado = PowerShellCC($serverusername, $serverpassword, $domain, $user, $pass);

            // Verificar el resultado y devolver una respuesta adecuada
            if (trim($resultado) === '0') {
                // Contraseña cambiada con éxito
                // echo "La contraseña del usuario $user en la dirección IP $domain ha sido cambiada con éxito. La nueva contraseña es $pass ";
                echo json_encode('success');
            } elseif (trim($resultado) === '-1') {
                // Usuario no encontrado en la máquina remota
                echo json_encode('El usuario no existe');
            } else {
                // Error al ejecutar el script de PowerShell
                echo json_encode('Error al ejecutar el script de PowerShell');
            }
        } elseif ($pingStatus === -1) {
            // IP o puerto no disponibles
            echo json_encode('La IP o el puerto no se encuentra disponible');
        }
    } else {
        // Si no se recibieron todos los parámetros esperados, devolver un mensaje de error
        echo json_encode('Faltan parámetros en la llamada a la api...');
    }
    //TEST 22032024
    // Crear una cadena con el volcado de los datos utilizando var_export
    $logMessage = date('Y-m-d H:i:s') . " - Datos recibidos :\n";
    $logMessage .= "serverusername: " . var_export($serverusername, true) . "\n";
    $logMessage .= "serverpassword: " . var_export($serverpassword, true) . "\n";
    $logMessage .= "domain: " . var_export($domain, true) . "\n";
    $logMessage .= "user: " . var_export($user, true) . "\n";
    $logMessage .= "pass: " . var_export($pass, true) . "\n";

    //
    // Guardar el mensaje en el archivo de registro debug_log.log
    file_put_contents('debug_log.log', $logMessage, FILE_APPEND);
    //TEST 22032024
}
// Llamar a la función para procesar los datos recibidos por cURL
procesar();
