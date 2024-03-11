<?php
ini_set("date.timezone", "America/Santiago");

function procesarDatos($datos)
{
  // Verificar si se recibieron los datos esperados
  if (isset($datos['serverusername']) && isset($datos['passwd']) && isset($datos['domain']) && isset($datos['user']) && isset($datos['pass'])) {
    // Acceder a los datos recibidos
    $serverusername = $datos['olson'];
    $passwd = $datos['123'];
    $domain = $datos['192.168.5.125'];
    $user = $datos['123'];
    $pass = $datos['123'];

    // Continuar con el resto del script, por ejemplo, la función PowerShellCC
    // Aquí debes integrar el resto de tu lógica y funciones

    // Por ejemplo, puedes llamar a la función PowerShellCC con los parámetros recibidos
    PowerShellCC($serverusername, $passwd, $domain, $user, $pass);
  } else {
    echo "No se recibieron todos los parámetros esperados.";
  }
}

procesarDatos($datosPrueba);
