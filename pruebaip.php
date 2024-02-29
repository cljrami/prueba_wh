<?php
function getIp(): string
{
    // Verificar si se está utilizando Cloudflare
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        // Obtener la dirección IP remota
        $ip = $_SERVER['REMOTE_ADDR'];

        // Si la IP es una dirección local (127.0.0.1 o 10.x.x.x), intentar obtener la IP real
        if (preg_match('/^(?:127|10)\.0\.0\.[12]?\d{1,2}$/', $ip)) {
            if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                $ip = $_SERVER['HTTP_X_REAL_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // Si hay un proxy, obtener la última IP de la lista
                $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip = trim(end($ip_list));
            }
        }
    } else {
        // Si no se puede determinar la IP, usar una IP local por defecto
        $ip = '127.0.0.1';
    }

    // Validar que la IP sea válida (IPv4)
    $filter = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    if ($filter === false) {
        $ip = '127.0.0.1';
    }

    return $ip;
}

// Lista de direcciones IP permitidas (puedes modificarla según tus necesidades)
$allowed_ips = array(
    '186.10.5.69', // Ejemplo: IP local permitida

    // Agrega más direcciones IP permitidas aquí
);

// Obtén la IP del cliente
$client_ip = getIp();

// Comprueba si la IP está en la lista de permitidas
if (in_array($client_ip, $allowed_ips)) {
    // Continuar con el proceso de cambio de clave en PowerShell
    $admin_user = "nombre_de_usuario"; // Reemplaza con el nombre de usuario
    $admin_pass = "nueva_contraseña"; // Reemplaza con la nueva contraseña
    $ip = "dirección_ip_remota"; // Reemplaza con la IP de la máquina remota

    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String $admin_pass -AsPlainText -Force; ";
    $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $admin_user, \$securePass; ";
    $command .= "Invoke-Command -ComputerName $ip -Credential \$cred -ScriptBlock { ";
    $command .= "param(\$targetUser, \$newPass); ";
    $command .= "if(Get-LocalUser -Name \$targetUser -ErrorAction SilentlyContinue) { ";
    $command .= "Set-LocalUser -Name \$targetUser -Password (ConvertTo-SecureString -AsPlainText \$newPass -Force); ";
    $command .= "echo 'true'; ";
    $command .= "} else { echo 'false'; } ";
    $command .= "} -ArgumentList $admin_user, $admin_pass; ";
    $command .= "\"";

    // Ejecutar el comando de PowerShell y obtener la salida
    $output = shell_exec($command);

    echo "Resultado del cambio de clave: $output";
} else {
    echo "Acceso denegado desde la IP: $client_ip";
}
