<?php
ini_set("date.timezone", "America/Santiago");

// Clase para verificar dispositivos y realizar ping
class CheckDevice
{
    // Función para determinar el sistema operativo
    public function myOS()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === (chr(87) . chr(73) . chr(78)))
            return true;

        return false;
    }

    // Función para realizar ping a una dirección IP
    public function ping($ip_addr)
    {
        if ($this->myOS()) {
            if (!exec("ping -n 1 -w 1 " . $ip_addr . " 2>NUL > NUL && (echo 0) || (echo 1)"))
                return true;
        } else {
            if (!exec("ping -q -c1 " . $ip_addr . " >/dev/null 2>&1 ; echo $?"))
                return true;
        }

        return false;
    }
}

// Función para obtener la IP remota del cliente
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

// Mostrar si la IP remota está permitida
if (in_array($remote_ip, $allowed_ips)) {
    echo "La IP: $remote_ip está permitida.\n";
} else {
    echo "Acceso no autorizado para la IP: $remote_ip\n";
    exit; // Salir del script si la IP remota no está permitida
}

// Crear una instancia de la clase CheckDevice
$checkDevice = new CheckDevice();

// Validar la IP del equipo remoto
if (!$checkDevice->ping($remote_ip)) {
    echo "Error: La IP del equipo remoto no es válida o no está conectado.\n";
    exit; // Salir del script si la IP del equipo remoto no es válida o no está conectado
}

// Abrir o crear un archivo de registro para escritura (modo append)
$log_file = fopen("access.txt", "a");

// Registrar la fecha y hora de la solicitud
$log_entry = "------INICIO ACCION [" . date('Y-m-d H:i:s') . "]------\n ";
$log_entry .= "IP Remota del Cliente: $remote_ip\n";

// Registrar si la IP del equipo remoto existe
if ($checkDevice->ping($remote_ip)) {
    $log_entry .= "La IP del equipo remoto existe.\n";
} else {
    $log_entry .= "Error: La IP del equipo remoto no responde al ping.\n";
}

// Verificar si la IP remota está en la lista blanca de IPs permitidas
if (in_array($remote_ip, $allowed_ips)) {
    // La IP del cliente está permitida, permitir que el resto del código se ejecute

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recibir los datos del formulario HTML
        $admin_user = $_POST["admin_user"] ?? '';
        $admin_pass = $_POST["admin_pass"] ?? '';
        $ip = $_POST["ip_destino"] ?? '';
        $target_user = $_POST["target_user"] ?? '';
        $user_pass = $_POST["user_pass"] ?? ''; // Nueva variable para la contraseña del usuario

        // Detalles del formulario recibido
        $log_entry .= "  Usuario Administrador: $admin_user\n";
        $log_entry .= "  Contraseña Administrador: $admin_pass\n";
        $log_entry .= "  IP del Equipo Remoto: $ip\n";
        $log_entry .= "  Usuario Objetivo: $target_user\n";
        $log_entry .= "  Nueva Contraseña Usuario: $user_pass\n";

        // Verificar si los campos requeridos no están vacíos
        if (!empty($admin_user) && !empty($admin_pass) && !empty($ip) && !empty($target_user) && !empty($user_pass)) {
            // Escapar los argumentos del shell
            $admin_user = escapeshellarg($admin_user);
            $admin_pass = escapeshellarg($admin_pass);
            $ip = escapeshellarg($ip);
            $target_user = escapeshellarg($target_user);
            $user_pass = escapeshellarg($user_pass); // Escapar la contraseña del usuario

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
            $command .= "} -ArgumentList $target_user, $user_pass; "; // Usar $user_pass en lugar de $new_pass
            $command .= "\"";

            // Ejecutar el comando de PowerShell y obtener la salida
            $output = shell_exec($command);

            // Registrar la acción de PowerShell en el archivo de registro
            $log_entry .= "Comando PowerShell ejecutado: $command\n";
            $log_entry .= "Salida de PowerShell: $output\n";

            // Verificar si el cambio de contraseña fue exitoso
            if (trim($output) === 'true') {
                $log_entry .= "Cambio de contraseña realizado con éxito para el usuario $target_user.\n";
                echo '<script type="text/javascript">';
                echo 'alert("Cambio de contraseña realizado con éxito. La nueva contraseña es: ' . $user_pass . '");'; // Usar $user_pass en lugar de $new_pass
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
}

// Registrar toda la acción en el archivo de registro
$log_entry .= "---------- FIN ACCION ----------\n\n";
fwrite($log_file, $log_entry);

// Cerrar el archivo de registro
fclose($log_file);
