<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donuts_world";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$total = $data['total'];
$productos = $data['productos'];
$nombre_cliente = $data['nombre_cliente'];
$reseña_cliente = isset($data['reseña_cliente']) ? $data['reseña_cliente'] : '';

$sql = "INSERT INTO ventas (total, nombre_cliente, reseña) VALUES ('$total', '$nombre_cliente', '$reseña_cliente')";
if ($conn->query($sql) === TRUE) {
    $venta_id = $conn->insert_id;
    foreach ($productos as $producto) {
        $producto_id = $producto['id'];
        $cantidad = $producto['cantidad'];
        $subtotal = $producto['precio'] * $cantidad;
        $sql = "INSERT INTO detalles_ventas (venta_id, producto_id, cantidad, subtotal) VALUES ('$venta_id', '$producto_id', '$cantidad', '$subtotal')";
        $conn->query($sql);
    }
    echo json_encode(['success' => true, 'venta_id' => $venta_id]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>

