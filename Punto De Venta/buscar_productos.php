<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donuts_world";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $_GET['q'];
$sql = "SELECT * FROM productos WHERE nombre LIKE '%$query%' OR codigo_barras LIKE '%$query%'";
$result = $conn->query($sql);

$productos = array();
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);

$conn->close();
?>
