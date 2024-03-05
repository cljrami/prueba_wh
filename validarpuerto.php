<?php
$ip = '192.168.5.125';
$puerto = 5985;
$timeout = 5; // Tiempo de espera en segundos

$conexion = @fsockopen($ip, $puerto, $errno, $errstr, $timeout);

if ($conexion) {
    echo "El puerto $puerto está abierto en la IP $ip\n";
    fclose($conexion);
} else {
    echo "El puerto $puerto está cerrado o no se pudo conectar a la IP $ip: $errstr\n";
}
