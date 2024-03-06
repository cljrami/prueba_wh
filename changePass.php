
<?php

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
// Funcion
function PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente)
{
  // Construir el comando de PowerShell
  $command = "powershell -Command \"";
  $command .= "\$securePass = ConvertTo-SecureString -String $password_control -AsPlainText -Force; ";
  $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $usuario_control, \$securePass; ";
  $command .= "Invoke-Command -ComputerName $ip_cliente -Credential \$cred -ScriptBlock { ";
  $command .= "param(\$usuario_cliente, \$password_cliente); ";
  $command .= "if(Get-LocalUser -Name \$usuario_cliente -ErrorAction SilentlyContinue) { ";
  $command .= "Set-LocalUser -Name \$usuario_cliente -Password (ConvertTo-SecureString -AsPlainText \$password_cliente -Force); ";
  $command .= "echo '0'; ";
  $command .= "} else { echo '-1'; } ";
  $command .= "} -ArgumentList $usuario_cliente, $password_cliente; ";
  $command .= "\"";

  // Ejecutar el comando de PowerShell y obtener la salida
  $output = shell_exec($command);
  return $output;
  //$output = shell_exec($command);
  //if (!$output) $status = -1;  // error en cambio de contraseña
  //else {

  //$status = 0; // retornamos código de ejecución 0, como todo software normalizado
  //}
  // return $status;
}
// Ejemplo de uso de la función PowerShellCC
$usuario_control = "olson";
$password_control = "123";
$ip_cliente = "192.168.5.125";
$usuario_cliente = "123";
$password_cliente = "1111111111";

$resultado = PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente, $password_cliente);
echo "Resultado de PowerShellCC: $resultado";
