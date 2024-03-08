<?php
ini_set("date.timezone", "America/Santiago");
/*-------------------------------------------------------------- 
## VARIABLES 
----------------------------------------------------------------*/
// Recibir las variables enviadas por cURL
//ADMIN
$username = $_POST['serverusername'];
$pass = $_POST['hash'];
//PVC
$domain = $_POST['serverhostname'];
$user  = $_POST['username'];
$password = $_POST['password'];

// GET IP17-03-2024
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

// Obtener la IP remota del cliente utilizando la función getIp()
$remote_ip = getIp();

// Lista de IPs permitidas (IPv4 e IPv6)
$allowed_ips = array("186.10.5.69", "192.168.5.70", "192.168.5.1",);

// Verificar si la IP remota está permitida
if (!in_array($remote_ip, $allowed_ips)) {
  die("Acceso no autorizado para la IP: $remote_ip\n");
}

/*-------------------------------------------------------------- 
## FUNCION PING
----------------------------------------------------------------*/
function ping($ip_cliente)
{
  $status = -2; // Definimos código -2 solo para inicializar variable. Si la función lo entrega es que ni siquiera tiene habilitada la función fsockopen
  $puerto = 5985; // Puerto de WinRM

  // Intentar realizar un ping a la dirección IP
  $file = @fsockopen($ip_cliente, $puerto, $errno, $errstr, 10);

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

/*-------------------------------------------------------------- 
## FUNCION POWERSHELL
----------------------------------------------------------------*/
function PowerShellCC($user, $pass, $domain, $username, $pass_cliente)
{
  // Construir el comando de PowerShell
  $command = "powershell -Command \"";
  $command .= "\$securePass = ConvertTo-SecureString -String $pass -AsPlainText -Force; ";
  $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $user, \$securePass; ";
  $command .= "\$result = Invoke-Command -ComputerName $domain -Credential \$cred -ScriptBlock { ";
  $command .= "param(\$username, \$password); ";
  $command .= "if(Get-LocalUser -Name \$username -ErrorAction SilentlyContinue) { ";
  $command .= "Set-LocalUser -Name \$username -Password (ConvertTo-SecureString -AsPlainText \$pass_cliente -Force); ";
  $command .= "return '0'; ";
  $command .= "} else { return '-1'; } ";
  $command .= "} -ArgumentList $username, '$pass_cliente'; ";
  $command .= "echo \$result; ";
  $command .= "\"";

  // Ejecutar el comando de PowerShell y obtener la salida
  $output = shell_exec($command);

  return $output;
}

// Verificar la disponibilidad de la dirección IP y el puerto
$pingStatus = ping($ip_cliente);

if ($pingStatus === 0) {
  //Verificar si el usuario cliente existe en la máquina remota
  $resultado = PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente);

  if (trim($resultado) === '0') {
    $echoMessage = "La contraseña del usuario $usuario_cliente en la IP $ip_cliente ha sido cambiada con éxito. La nueva contraseña es $password_cliente ";
  } elseif (trim($resultado) === '-1') {
    $echoMessage = "El usuario $usuario_cliente no existe en la máquina remota.";
  } else {
    $echoMessage = "Error al ejecutar el script de PowerShell.";
  }
} elseif ($pingStatus === -1) {
  $echoMessage = "La dirección IP o el puerto no están disponibles.";
} else {
  $echoMessage = "La dirección IP $ip_cliente existe.";
}

// Guardar los resultados en el archivo log.log
$logContent = date('Y-m-d H:i:s') . " - UsuarioControl: $user - UsuarioCliente: $username - Resultado: $echoMessage\n";
file_put_contents('log.log', $logContent, FILE_APPEND);

// Mostrar los resultados
echo $echoMessage;
