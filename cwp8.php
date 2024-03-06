<?php
ini_set("date.timezone", "America/Santiago");

/*-------------------------------------------------------------- 
## VARIABLES 
----------------------------------------------------------------*/
$usuario_control = "olson";
$password_control = "123";
$ip_cliente = "192.168.5.125";
$usuario_cliente = "123";
$password_cliente = "1234567890";


/*-------------------------------------------------------------- 
## FUNCION PING
----------------------------------------------------------------*/
function ping($ip_cliente)
{
    $status = -2; //definimos código -2 sólo para inicializar variable, si la función lo entrega es que ni siquiera tiene habilitada la función fsockopen
    $puerto = 5985; // puerto de WinRM
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
function PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente)
{
    $status = -2; //definimos código -2 sólo para inicializar variable, si la función lo entrega es error crítico
    // Construir el comando de PowerShell
    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String $password_control -AsPlainText -Force; ";
    $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $usuario_control, \$securePass; ";
    $command .= "Invoke-Command -ComputerName $ip_cliente -Credential \$cred -ScriptBlock { ";
    $command .= "Get-LocalUser -Name $usuario_cliente -ErrorAction SilentlyContinue; ";
    $command .= "}\"";

    // Ejecutar el comando de PowerShell y obtener la salida
    $output = shell_exec($command);
    if (!$output) $status = -1;  // error en cambio de contraseña
    else {

        $status = 0; // retornamos código de ejecución 0, como todo software normalizado
    }
    return $status;
}

/*-------------------------------------------------------------- 
## EJECUCIÓN
----------------------------------------------------------------*/

// Validar conexión al equipo remoto
$ping_status = ping($ip_cliente);

if ($ping_status === 0) {

    // Validar usuario en el equipo remoto
    $powershell_status = PowerShellCC($usuario_control, $password_control, $ip_cliente, $usuario_cliente);

    if ($powershell_status === 0) {
        echo "El usuario $usuario_cliente existe en el equipo $ip_cliente.";
    } else {
        echo "El usuario $usuario_cliente no existe en el equipo $ip_cliente.";
    }
} else {
    echo "No se pudo conectar al equipo $ip_cliente.";
}
