<?php
ini_set("date.timezone", "America/Santiago");

/*-------------------------------------------------------------- 
## VARIABLES 
----------------------------------------------------------------*/
// Recibir las variables enviadas por cURL
$usuario_control = $_POST['usuario_control'];
$password_control = $_POST['password_control'];
$ip_cliente = $_POST['ip_cliente'];
$usuario_cliente = $_POST['usuario_cliente'];
$password_cliente = $_POST['password_cliente'];

// Aquí puedes procesar las variables recibidas según tus necesidades
// Por ejemplo, podrías llamar a las funciones definidas en tu script original, como ping y PowerShellCC.

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
function PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente)
{
  // Construir el comando de PowerShell
  $command = "powershell -Command \"";
  $command .= "\$securePass = ConvertTo-SecureString -String $password_control -AsPlainText -Force; ";
  $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $usuario_control, \$securePass; ";
  $command .= "\$result = Invoke-Command -ComputerName $ip_cliente -Credential \$cred -ScriptBlock { ";
  $command .= "param(\$usuario_cliente, \$password_cliente); ";
  $command .= "if(Get-LocalUser -Name \$usuario_cliente -ErrorAction SilentlyContinue) { ";
  $command .= "Set-LocalUser -Name \$usuario_cliente -Password (ConvertTo-SecureString -AsPlainText \$password_cliente -Force); ";
  $command .= "return '0'; ";
  $command .= "} else { return '-1'; } ";
  $command .= "} -ArgumentList $usuario_cliente, '$password_cliente'; ";
  $command .= "echo \$result; ";
  $command .= "\"";

  // Ejecutar el comando de PowerShell y obtener la salida
  $output = shell_exec($command);

  // Crear el mensaje de registro
  $logMessage = date('Y-m-d H:i:s') . " - UsuarioControl: $usuario_control - UsuarioCliente: $usuario_cliente - Resultado: $output\n";

  // Guardar el mensaje en el archivo de registro debug_log.log
  file_put_contents('debug_log.log', $logMessage, FILE_APPEND);

  return $output;
}

// Verificar la disponibilidad de la dirección IP y el puerto
$pingStatus = ping($ip_cliente);

// Registro de resultados de depuración PHP
$logMessageDebug = date('Y-m-d H:i:s') . " - Resultado de ping a $ip_cliente: ";

if ($pingStatus === 0) {
  $logMessageDebug .= "Disponible";
  $echoMessage = "La dirección IP y el puerto están disponibles.\n";
  echo $echoMessage;
  // Guardar el mensaje de depuración en el archivo de registro debug_log.log
  file_put_contents('debug_log.log', $logMessageDebug . " - " . $echoMessage, FILE_APPEND);
  //Verificar si el usuario cliente existe en la máquina remota
  $resultado = PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente);

  // Registro de resultados de depuración PHP
  $logMessageDebug .= " - Resultado de PowerShellCC: $resultado\n";
  if (trim($resultado) === '0') {
    $echoMessage = "La contraseña del usuario ha sido cambiada con éxito.";
  } elseif (trim($resultado) === '-1') {
    $echoMessage = "El usuario $usuario_cliente no existe en la máquina remota.";
  } else {
    $echoMessage = "Error al ejecutar el script de PowerShell.";
  }
} elseif ($pingStatus === -1) {
  $logMessageDebug .= "No disponible";
  $echoMessage = "La dirección IP o el puerto no están disponibles.";
} else {
  $logMessageDebug .= "IP $ip_cliente existe.";
  $echoMessage = "La dirección IP $ip_cliente existe.";
}

// Mostrar los resultados de depuración
echo $echoMessage;
// Guardar el mensaje de depuración en el archivo de registro debug_log.log
file_put_contents('debug_log.log', $logMessageDebug . " - " . $echoMessage, FILE_APPEND);
