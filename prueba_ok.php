<?php


// Obtener la IP remota del cliente a través del encabezado X-Forwarded-For
$remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// Lista de IPs permitidas (IPv4 e IPv6)
$allowed_ips = array("192.168.5.156", "::1");

// Abrir o crear un archivo de registro para escritura (modo append)
$log_file = fopen("log.txt", "a");

// Registrar la fecha y hora de la solicitud
$log_entry = "[" . date('Y-m-d H:i:s') . "] ";

// Verificar si la IP remota está en la lista blanca de IPs permitidas
if (in_array($remote_ip, $allowed_ips)) {
    // La IP del cliente está permitida, permitir que el resto del código se ejecute
    $log_entry .= "Acceso permitido para la IP: $remote_ip\n";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibir los datos del formulario HTML
        $admin_user = $_POST["admin_user"] ?? '';
        $admin_pass = $_POST["admin_pass"] ?? '';
        $ip = $_POST["ip"] ?? '';
        $target_user = $_POST["target_user"] ?? '';

        // Generar una contraseña aleatoria
        $new_pass = generateRandomPassword();

        // Registrar la acción en el archivo de registro incluyendo la contraseña asignada
        $log_entry .= "Se ha generado una contraseña aleatoria ($new_pass) para el usuario $target_user.\n";

        // Verificar si los campos requeridos no están vacíos
        if (!empty($admin_user) && !empty($admin_pass) && !empty($ip) && !empty($target_user)) {
            // Escapar los argumentos del shell
            $admin_user = escapeshellarg($admin_user);
            $admin_pass = escapeshellarg($admin_pass);
            $ip = escapeshellarg($ip);
            $target_user = escapeshellarg($target_user);
            $new_pass = escapeshellarg($new_pass);

            // Construir el comando de PowerShell
            $command = "powershell -Command \"";
            $command .= "\$securePass = ConvertTo-SecureString -String $admin_pass -AsPlainText -Force; ";
            $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $admin_user, \$securePass; ";
            $command .= "Invoke-Command -ComputerName $ip -Credential \$cred -ScriptBlock { ";
            $command .= "param(\$targetUser, \$newPass); ";
            $command .= "if(Get-LocalUser -Name \$targetUser -ErrorAction SilentlyContinue) { ";
            $command .= "Set-LocalUser -Name \$targetUser -Password (ConvertTo-SecureString -AsPlainText \$newPass -Force); ";
            $command .= "echo 'true'; ";
            $command .= "} else { echo 'false'; } ";
            $command .= "} -ArgumentList $target_user, $new_pass; ";
            $command .= "\"";

            // Ejecutar el comando de PowerShell y obtener la salida
            $output = shell_exec($command);

            // Verificar si el cambio de contraseña fue exitoso
            if (trim($output) === 'true') {
                $log_entry .= "Cambio de contraseña realizado con éxito para el usuario $target_user.\n";
                echo '<script type="text/javascript">';
                echo 'alert(Cambio de contraseña realizado con éxito. La nueva contraseña es: ' . $new_pass . ');';
                echo '</script>';
            } elseif (trim($output) === 'false') {
                $log_entry .= "El usuario especificado no existe en el equipo remoto.\n";
                echo "El usuario especificado no existe en el equipo remoto.";
            } else {
                $log_entry .= "Ocurrió un error al cambiar la contraseña.\n";
                echo "Ocurrió un error al cambiar la contraseña.";
            }
        } else {
            $log_entry .= "Todos los campos son obligatorios.\n";
            echo "Todos los campos son obligatorios.";
        }
    }
} else {
    // La IP del cliente no está en la lista blanca, negar el acceso
    $log_entry .= "Acceso no autorizado para la IP: $remote_ip\n";
    echo "Acceso no autorizado para la IP: $remote_ip";
}

// Escribir la entrada del log en el archivo
fwrite($log_file, $log_entry);
fclose($log_file);

// Función para generar una contraseña aleatoria
function generateRandomPassword($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $charactersLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, $charactersLength - 1)];
    }
    return $password;
}
