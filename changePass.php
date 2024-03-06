<?php
ini_set("date.timezone", "America/Santiago");

/*-------------------------------------------------------------- 
## VARIABLES 
----------------------------------------------------------------*/
$usuario_control = "olson";
$password_control = "123";
$ip_cliente = "192.168.5.125";
$usuario_cliente = "123";
$password_cliente = "1111111111";

/*-------------------------------------------------------------- 
## FUNCION PING
----------------------------------------------------------------*/
function ping($ip_cliente)

{

  $status    = -2; // definimos código -2 sólo para inicializar variable, si la función lo entrega es que ni siquiera tiene habilitada la función fsockopen
  $puerto    = 5985; // puerto de WinRM
  $file      = fsockopen($ip_cliente, $puerto, $errno, $errstr, 10);

  if (!$file) $status = -1;  // si fsockopen falla retornamos error -1
  else {
    fclose($file);
    $status = 0; // si la máquina existe, hace ping al puerto y está disponible retornamos código de ejecución 0, como todo software normalizado
  }
  return $status;
}

/*-------------------------------------------------------------- 
## FUNCION POWERSHEL
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
  $logMessage = date('Y-m-d H:i:s') . " - Usuario: $usuario_control - Usuario Cambiado: $usuario_cliente - Resultado: $output\n";

  // Guardar el mensaje en el archivo de registro changepass.txt
  file_put_contents('changepass.txt', $logMessage, FILE_APPEND);

  return $output;
}

// Ejemplo de uso de la función PowerShellCC
$resultado = PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente);
echo "Resultado de PowerShellCC: $resultado";
