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
function PowerShellCC($serverusername, $passwordserver, $domain, $user, $pass)
{
    // Construir el comando de PowerShell
    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String $passwordserver -AsPlainText -Force; ";
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
    $logMessage = date('Y-m-d H:i:s') . " - UsuarioControl: $serverusername - UsuarioCliente: $user - Resultado: $output\n";

    // Guardar el mensaje en el archivo de registro debug_log.log
    file_put_contents('debug_log.log', $logMessage, FILE_APPEND);

    return $output;
}

// Recibir los datos enviados por cURL
function recibirDatosCURL()
{
    // Recibir los datos enviados por cURL
    $serverusername = isset($_POST['username']) ? $_POST['username'] : '';
    $passwordserver = isset($_POST['passwd']) ? $_POST['passwd'] : '';
    $domain = isset($_POST['domain']) ? $_POST['domain'] : '';
    $user = isset($_POST['user']) ? $_POST['user'] : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

    // Devolver los datos recibidos
    return array(
        'serverusername' => $serverusername,
        'passwordserver' => $passwordserver,
        'domain' => $domain,
        'user' => $user,
        'pass' => $pass,
    );
}
function verificar_ip()
{
    // Obtener la IP remota del cliente utilizando la función getIp()
    $remote_ip = getIp();

    // Lista de IPs permitidas (IPv4 e IPv6)
    $allowed_ips = array("186.10.5.69", "192.168.5.70", "192.168.5.1");

    // Mostrar si la IP remota está permitida
    if (in_array($remote_ip, $allowed_ips)) {
        echo "La IP: $remote_ip está permitida.\n";
    } else {
        echo "Acceso no autorizado para la IP: $remote_ip\n";
    }
}
// // Obtener la IP remota del cliente utilizando la función getIp()
// $remote_ip = getIp();

// // Lista de IPs permitidas (IPv4 e IPv6)
// $allowed_ips = array("186.10.5.69", "192.168.5.70", "192.168.5.1");

// // Mostrar si la IP remota está permitida
// if (in_array($remote_ip, $allowed_ips)) {
//     echo "La IP: $remote_ip está permitida.\n";
// } else {
//     echo "Acceso no autorizado para la IP: $remote_ip\n";
// }
// //13032024
// Recibir y verificar los datos enviados por cURL
function procesarDatosCURL($datosCURL)
{
    // Extraer los datos del arreglo asociativo
    $serverusername = $datosCURL['serverusername'];
    $passwordserver = $datosCURL['passwordserver'];
    $domain = $datosCURL['domain'];
    $user = $datosCURL['user'];
    $pass = $datosCURL['pass'];

    // Verificar si se recibieron todos los parámetros esperados
    if (!empty($serverusername) && !empty($passwordserver) && !empty($domain) && !empty($user) && !empty($pass)) {
        // Verificar la disponibilidad de la dirección IP y el puerto
        $pingStatus = ping($domain);

        // Si el ping se realizó correctamente
        if ($pingStatus === 0) {
            // Ejecutar comandos de PowerShell para cambiar la contraseña
            $resultado = PowerShellCC($serverusername, $passwordserver, $domain, $user, $pass);

            // Verificar el resultado y devolver una respuesta adecuada
            if (trim($resultado) === '0') {
                // Contraseña cambiada con éxito
                echo "La contraseña del usuario $user en la dirección IP $domain ha sido cambiada con éxito. La nueva contraseña es $pass ";
            } elseif (trim($resultado) === '-1') {
                // Usuario no encontrado en la máquina remota
                echo "El usuario $user no existe en la máquina remota.";
            } else {
                // Error al ejecutar el script de PowerShell
                echo "Error al ejecutar el script de PowerShell.";
            }
        } elseif ($pingStatus === -1) {
            // IP o puerto no disponibles
            echo "La dirección IP o el puerto no están disponibles.";
        } else {
            // Dirección IP encontrada
            echo "La dirección IP $domain existe.";
        }
    } else {
        // Si no se recibieron todos los parámetros esperados, devolver un mensaje de error
        echo "No se recibieron todos los parámetros esperados.";
    }
}

// Llamar a la función para procesar los datos recibidos por cURL
$datosCURL = recibirDatosCURL();
procesarDatosCURL($datosCURL);
