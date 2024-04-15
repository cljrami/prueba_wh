$results = localAPI($command, $postData);
if ($results['result'] == 'success' && !empty($results['products']['product'])) {
foreach ($results['products']['product'] as $producto) {
if ($producto['id'] == $productselect) {
return array(
'dedicatedip' => $producto['dedicatedip'],
'nombreProducto' => $producto['name'],
'numeroPedido' => $producto['orderid'],
);
}
}
return array('error' => 'Producto no encontrado o sin IP dedicada');
} else {
return array('error' => "Error al consultar los productos del cliente: " . $results['message']);
}