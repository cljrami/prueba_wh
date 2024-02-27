<?php
// Parámetros
$admin_user = "tu_usuario_administrador";
$admin_pass = "tu_contraseña_administrador";
$host = "https://vps06.xhost.cl:8443"; // Reemplaza con el nombre de host
$target_user = "lala";

// Generar una contraseña aleatoria y segura
$new_pass = generateRandomPassword();

// Construir el comando de PowerShell
$command = "powershell -Command \"";
$command .= "\$securePass = ConvertTo-SecureString -String $admin_pass -AsPlainText -Force; ";
$command .= "\$cred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $admin_user, \$securePass; ";
$command .= "if(Get-LocalUser -Name $target_user -ErrorAction SilentlyContinue) { ";
$command .= "Set-LocalUser -Name $target_user -Password (ConvertTo-SecureString -AsPlainText $new_pass -Force); ";
$command .= "echo 'true'; ";
$command .= "} else { echo 'false'; } ";
$command .= "\"";

// Ejecutar el comando en el servidor
$result = shell_exec($command);

// Mostrar el resultado del cambio
if (trim($result) === "true") {
    echo "La contraseña del usuario $target_user se ha cambiado correctamente. La nueva contraseña es: $new_pass";
} else {
    echo "El usuario $target_user no existe en la máquina remota.";
}

// Función para generar una contraseña aleatoria y segura
function generateRandomPassword($length = 12)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $password;
}
