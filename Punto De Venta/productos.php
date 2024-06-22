<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donuts_world";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, nombre, precio, imagen FROM productos";
$result = $conn->query($sql);

$productos = array();
while($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

$conn->close();

echo json_encode($productos);
?>
