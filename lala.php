session_start();

// Verificar si los parámetros necesarios están presentes en la URL
if (!isset($_GET['userid']) || !isset($_GET['productselect'])) {
echo "Faltan parámetros necesarios.";
exit;
}

$userid = $_GET['userid'];
$productselect = $_GET['productselect'];

// Aquí deberías validar que el usuario tiene permisos para consultar estos datos
// Esto es importante para la seguridad de tu aplicación

$command = 'GetClientsProducts';
$postData = array(
'clientid' => $userid,
);
$results = localAPI($command, $postData);

// Verificar si la llamada a la API fue exitosa
if ($results['result'] == 'success') {
if (isset($results['products']['product'])) {
$productos = $results['products']['product'];
$productoEncontrado = false;

foreach ($productos as $producto) {
// Buscar el producto específico por ID
if ($producto['id'] == $productselect) {
$nombreProducto = $producto['name'];
$dedicatedip = $producto['dedicatedip'];
$numeroPedido = $producto['orderid'];

// Mostrar los detalles del producto seleccionado
echo "Número de pedido: $numeroPedido\n";
echo "Dirección IP dedicada: $dedicatedip\n";
echo "Nombre del producto: $nombreProducto\n";
$productoEncontrado = true;
break;
}
}

if (!$productoEncontrado) {
echo "El producto especificado no pertenece a este cliente o no existe.";
}
} else {
echo "No se encontraron productos para este cliente.";
}
} else {
echo "Error al consultar los productos del cliente: " . $results['message'] . "\n";
}