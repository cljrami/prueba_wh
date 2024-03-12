<?php
ini_set("date.timezone", "America/Santiago");

/*-------------------------------------------------------------- 
## VARIABLES 
----------------------------------------------------------------*/
// Recibir las variables enviadas por cURL
$serverusername = "olson";
$passwordserver = "123";
$domain = "192.168.5.125";
$user = "123";
$pass = "123456789";

// if (isset($_POST['serverusername']) && isset($_POST['passwd']) && isset($_POST['domain']) && isset($_POST['user']) && isset($_POST['pass'])) {
//   //   // Acceder a los datos recibidos
//   $serverusername = $_POST['serverusername'];
//   $passwd = $_POST['passwd'];
//   $domain = $_POST['domain'];
//   $user = $_POST['user'];
//   $pass = $_POST['pass'];

//   // Continuar con el resto del script, por ejemplo, la función PowerShellCC
//   // Aquí debes integrar el resto de tu lógica y funciones
// } else {
//   echo "No se recibieron todos los parámetros esperados.";
// }
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
$allowed_ips = array("186.10.5.69", "192.168.5.70", "192.168.5.1");

// Mostrar si la IP remota está permitida
if (in_array($remote_ip, $allowed_ips)) {
  echo "La IP: $remote_ip está permitida.\n";
} else {
  echo "Acceso no autorizado para la IP: $remote_ip\n";
}
//FIN GET IP17-03-2024

/*-------------------------------------------------------------- 
## FUNCION PING
----------------------------------------------------------------*/
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

/*-------------------------------------------------------------- 
## FUNCION POWERSHELL
----------------------------------------------------------------*/
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

// Verificar la disponibilidad de la dirección IP y el puerto
$pingStatus = ping($domain);

// Registro de resultados de depuración PHP
$logMessageDebug = date('Y-m-d H:i:s') . " - Resultado de ping a $domain: ";

if ($pingStatus === 0) {
  $logMessageDebug .= "Disponible";
  $echoMessage = "La dirección IP y el puerto están disponibles.\n";
  echo $echoMessage;
  // Guardar el mensaje de depuración en el archivo de registro debug_log.log
  file_put_contents('debug_log.log', $logMessageDebug . " - " . $echoMessage, FILE_APPEND);
  //Verificar si el usuario cliente existe en la máquina remota
  $resultado = PowerShellCC($serverusername, $passwordserver, $domain, $user, $pass);




  // Registro de resultados de depuración PHP
  $logMessageDebug .= " - Resultado de PowerShellCC: $resultado\n";
  if (trim($resultado) === '0') {
    $echoMessage = "La contraseña del usuario $user en la ip  $domain ha sido cambiada con éxito. La nueva constraseña es $pass ";
  } elseif (trim($resultado) === '-1') {
    $echoMessage = "El usuario $user no existe en la máquina remota.";
  } else {
    $echoMessage = "Error al ejecutar el script de PowerShell.";
  }
} elseif ($pingStatus === -1) {
  $logMessageDebug .= "No disponible";
  $echoMessage = "La dirección IP o el puerto no están disponibles.";
} else {
  $logMessageDebug .= "IP $domain existe.";
  $echoMessage = "La dirección IP $domain existe.";
}

// Mostrar los resultados de depuración
echo $echoMessage;
// Guardar el mensaje de depuración en el archivo de registro debug_log.log
file_put_contents('debug_log.log', $logMessageDebug . " - " . $echoMessage, FILE_APPEND);
