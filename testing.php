<?php
// Datos de acceso a la máquina remota
$host = 'vps06.xhost.cl'; // Host de la máquina
$port = 8443; // Puerto (generalmente 8443 para HTTPS)

// Verificar si el host de destino es el correcto
if ($host === 'vps06.xhost.cl' && $port === 8443) {
    // El host y el puerto son correctos, permitir que el script continúe

    // Datos de acceso a la máquina remota
    $username = 'jrami'; // Usuario con acceso
    $password = '1234'; // Contraseña del usuario

    // Datos del usuario cuya contraseña deseas cambiar
    $targetUser = 'lala'; // Nombre del usuario
    $newPass = generateRandomPassword(); // Generar una contraseña aleatoria

    // Construir el comando para cambiar la contraseña
    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String '$newPass' -AsPlainText -Force; ";
    $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList '$username', \$securePass; ";
    $command .= "Invoke-Command -ComputerName '$host' -Credential \$cred -ScriptBlock { ";
    $command .= "param(\$targetUser, \$newPass); ";
    $command .= "if(Get-LocalUser -Name \$targetUser -ErrorAction SilentlyContinue) { ";
    $command .= "Set-LocalUser -Name \$targetUser -Password (ConvertTo-SecureString -AsPlainText \$newPass -Force); ";
    $command .= "echo 'true'; ";
    $command .= "} else { echo 'false'; } ";
    $command .= "} -ArgumentList '$targetUser', '$newPass'; ";
    $command .= "\"";

    // Ejecutar el comando de PowerShell y obtener la salida
    $output = shell_exec($command);

    // Registrar eventos en el archivo de registro
    $logMessage = date('Y-m-d H:i:s') . " - Cambio de contraseña para usuario '$targetUser'. Resultado: $output\n";
    file_put_contents('log.txt', $logMessage, FILE_APPEND);

    // Mostrar el resultado
    if (trim($output) === 'true') {
        echo "Contraseña cambiada correctamente. Nueva contraseña: $newPass";
    } else {
        echo "Error: No se pudo cambiar la contraseña.";
    }
} else {
    // El host o el puerto no son los esperados, mostrar un mensaje de error
    echo "Error: El host o el puerto no son válidos.";
}

// Función para generar una contraseña aleatoria
function generateRandomPassword($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
