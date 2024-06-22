<?php
require('fpdf/fpdf.php');

class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('logo.png', 25, 0, 30); // Ajusta la posición y el tamaño del logo
        $this->Ln(20); // Espacio después del logo
        
        // Título
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Factura', 0, 1, 'C');
        $this->Ln(5);

        // Información de la empresa
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Donuts World', 0, 1, 'C');
        $this->Cell(0, 5, 'Direccion: Calle Falsa 123', 0, 1, 'C');
        $this->Cell(0, 5, 'Telefono: 310 531 3941', 0, 1, 'C');
        $this->Cell(0, 5, 'Email: donutsworld@gmail.com', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-30); // Ajusta la posición del pie de página
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Siguenos en nuestras redes sociales:', 0, 1, 'C');
        $this->Cell(0, 5, 'Instagram: @donuts_world_cdb', 0, 1, 'C');
        $this->Cell(0, 5, 'Facebook: @DonutsWorld', 0, 1, 'C');
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donuts_world";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$venta_id = $_GET['id'];

$sql = "SELECT * FROM ventas WHERE id = $venta_id";
$result = $conn->query($sql);
$venta = $result->fetch_assoc();

$sql = "SELECT productos.nombre, detalles_ventas.cantidad, detalles_ventas.subtotal 
        FROM detalles_ventas 
        JOIN productos ON detalles_ventas.producto_id = productos.id 
        WHERE detalles_ventas.venta_id = $venta_id";
$result = $conn->query($sql);

$productCount = $result->num_rows;

// Calcular tamaño de fuente y espaciado basado en la cantidad de productos
$fontSize = 10;
$lineHeight = 5;

if ($productCount > 20) {
    $fontSize = 8;
    $lineHeight = 4;
} elseif ($productCount > 10) {
    $fontSize = 9;
    $lineHeight = 4.5;
}

$pdf = new PDF('P', 'mm', array(80, 200)); // Tamaño de papel ajustado
$pdf->SetMargins(5, 5, 5); // Ajuste de márgenes
$pdf->AddPage();

$pdf->SetFont('Arial', '', $fontSize);
$pdf->Cell(0, $lineHeight, 'Nombre del Cliente: ' . $venta['nombre_cliente'], 0, 1);
$pdf->Cell(0, $lineHeight, 'Fecha: ' . $venta['fecha'], 0, 1);

$pdf->Ln($lineHeight); // Salto de línea adicional

$pdf->SetFont('Arial', 'B', $fontSize);
$pdf->Cell(0, $lineHeight, 'Detalle de la compra:', 0, 1);
$pdf->Ln($lineHeight);

$pdf->SetFont('Arial', '', $fontSize);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(0, $lineHeight, $row['nombre'], 0, 1);
    $pdf->Cell(40, $lineHeight, 'Cantidad: ' . $row['cantidad'], 0, 0);
    $pdf->Cell(40, $lineHeight, 'Subtotal: $' . number_format($row['subtotal'], 2), 0, 1);
    $pdf->Ln($lineHeight);
}

$pdf->Cell(0, $lineHeight, '', 0, 1); // Espacio antes del total
$pdf->Cell(0, $lineHeight, 'Total: $' . number_format($venta['total'], 2), 0, 1, 'C'); // Total centrado

$pdf->Output();

$conn->close();
?>
