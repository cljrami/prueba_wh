<?php
$ip = '192.168.5.125';
$puerto = 5985;
$timeout = 5; // Tiempo de espera en segundos

$estado_conexion = @fsockopen($ip, $puerto, $errno, $errstr, $timeout);

if ($estado_conexion) {
    echo "El puerto $puerto está abierto en la IP $ip\n";
    fclose($estado_conexion);
} else {
    echo "El puerto $puerto está cerrado o no se pudo conectar a la IP $ip: $errstr\n";
}
