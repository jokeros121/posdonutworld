<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donuts_world";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error al conectar a la base de datos: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_FILES['imagen'];
    $codigo_barras = $_POST['codigo_barras'];

    if ($imagen['error'] === UPLOAD_ERR_OK) {
        $nombre_imagen = $imagen['name'];
        $ruta_temporal = $imagen['tmp_name'];
        $ruta_destino = 'imagenes/' . $nombre_imagen;

        if (move_uploaded_file($ruta_temporal, $ruta_destino)) {
            $sql = "INSERT INTO productos (nombre, precio, descripcion, imagen, codigo_barras) 
                    VALUES ('$nombre', '$precio', '$descripcion', '$nombre_imagen', '$codigo_barras')";
            if ($conn->query($sql) === TRUE) {
                echo "Producto agregado exitosamente.";
            } else {
                echo "Error al agregar el producto: " . $conn->error;
            }
        } else {
            echo "Hubo un error al mover el archivo.";
        }
    } else {
        echo "Error al subir la imagen: " . $imagen['error'];
    }
} else {
    echo "Acceso denegado.";
}

$conn->close();
?>
