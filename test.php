<?php
// URL que deseas llamar
$url = 'https://vps06.xhost.cl/prueba_whmcs/prueba_ok.php'; // Reemplaza con la URL real


// Función para hacer la llamada a la URL y guardar en el log
function callUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Bucle infinito para llamar a la URL cada 1 minuto
while (true) {
    $result = callUrl($url);
    // Guardar el resultado en el archivo log_conect.txt
    file_put_contents('log_connect.txt', $result . "\n", FILE_APPEND);
    // Registrar la acción en el log
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents('log_connect.txt', "[$timestamp] Llamada a la URL realizada.\n", FILE_APPEND);
    sleep(30); // Esperar 1 minuto antes de la próxima llamada
}
