<?php
// Función para obtener la IP del cliente
function get_client_ip()
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        // IP desde una conexión compartida a Internet
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        // IP pasada por un proxy
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

// IP de confianza (puedes ajustarla según tus necesidades)
$ip_de_confianza = '186.10.5.69'; // Ejemplo

// Obtén la IP actual del cliente
$ip_cliente = get_client_ip();

// Mensaje a registrar en el log
$log_message = "[" . date('Y-m-d H:i:s') . "] ";

// Registra la IP del cliente que está accediendo al script
$log_message .= "Cliente con IP: $ip_cliente está accediendo al script.\n";

// Verifica si la IP coincide con la de confianza
if ($ip_cliente === $ip_de_confianza) {
    // Recibe los datos del formulario HTML
    $admin_user = $_POST["admin_user"];
    $admin_pass = $_POST["admin_pass"];
    $ip = $_POST["ip"];
    $target_user = $_POST["target_user"];
    $new_pass = $_POST["new_pass"];

    // Escapa los argumentos del shell
    $admin_user = escapeshellarg($admin_user);
    $admin_pass = escapeshellarg($admin_pass);
    $ip = escapeshellarg($ip);
    $target_user = escapeshellarg($target_user);
    $new_pass = escapeshellarg($new_pass);

    // Construye el comando de PowerShell
    $command = "powershell -Command \"";
    $command .= "\$securePass = ConvertTo-SecureString -String $admin_pass -AsPlainText -Force; ";
    $command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $admin_user, \$securePass; ";
    $command .= "Invoke-Command -ComputerName $ip -Credential \$cred -ScriptBlock { ";
    $command .= "param(\$targetUser, \$newPass); ";
    $command .= "Set-LocalUser -Name \$targetUser -Password (ConvertTo-SecureString -AsPlainText \$newPass -Force); ";
    $command .= "} -ArgumentList $target_user, $new_pass; ";
    $command .= "\"";

    // Ejecuta el comando de PowerShell y obtén la salida
    $output = shell_exec($command);

    // Genera una alerta en JavaScript para indicar éxito
    echo '<script type="text/javascript">';
    echo 'alert("Cambio de contraseña realizado con éxito");';
    echo '</script>';

    // Registra la acción realizada en el log
    $log_message .= "Cambio de contraseña realizado con éxito para el usuario $target_user en la IP $ip.\n";
} else {
    // Si la IP no coincide, muestra un mensaje de error
    echo 'Acceso no autorizado.';

    // Registra el intento de acceso no autorizado en el log
    $log_message .= "Intento de acceso no autorizado desde la IP $ip_cliente.\n";
}

// Abre o crea el archivo de log para escritura (modo append)
$log_file = fopen("_logs.txt", "a");

// Escribe el mensaje en el archivo de log
fwrite($log_file, $log_message);

// Cierra el archivo de log
fclose($log_file);
