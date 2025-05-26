<?php
ob_start();
require_once 'vendor/autoload.php';
require_once 'connect.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

$month = date('m');
$year = date('Y');

$sql_total = "SELECT SUM(quantity * price) AS total_income FROM sales WHERE MONTH(sale_date) = ? AND YEAR(sale_date) = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ii", $month, $year);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();
$total_income = $row_total['total_income'] ?? 0;

$sql_sales = "
    SELECT sales.sale_date, products.name AS product_name, sales.quantity, sales.price, 
           (sales.quantity * sales.price) AS total 
    FROM sales 
    INNER JOIN products ON sales.product_id = products.id
    WHERE MONTH(sales.sale_date) = ? AND YEAR(sales.sale_date) = ?
    ORDER BY sales.sale_date ASC
";
$stmt_sales = $conn->prepare($sql_sales);
$stmt_sales->bind_param("ii", $month, $year);
$stmt_sales->execute();
$result_sales = $stmt_sales->get_result();

class MYPDF extends TCPDF {
    public function Header() {
        $image_file = __DIR__ . '/images/imag2.png';
        if (file_exists($image_file)) {
            $imgWidth = 40;
            $x = 85;        
            $y = 10;        
        
            $this->Image($image_file, $x, $y, $imgWidth, 0, 'PNG');
        }
        

        $this->SetY(42);
        $this->SetFont('thsarabunnewb', '', 18);
        $this->Cell(0, 10, 'บริษัท ตัวอย่าง จำกัด', 0, 1, 'C');

        $this->SetFont('thsarabunnewb', '', 24);
        $this->Cell(0, 12, 'รายงานรายได้การขาย', 0, 1, 'C');

        $this->SetFont('thsarabunnew', '', 16);
        $this->Cell(0, 10, 'เดือน: ' . $GLOBALS['month'] . ' / ปี: ' . $GLOBALS['year'], 0, 1, 'C');

        $this->Ln(2);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('thsarabunnew', 'I', 10);
        $this->Cell(0, 10, 'สร้างโดยระบบขายสินค้า - หน้า ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('ระบบขายสินค้า');
$pdf->SetFont('thsarabunnew', '', 16);
$pdf->SetMargins(20, 60, 20); // ซ้าย-บน-ขวา
$pdf->SetAutoPageBreak(TRUE, 30);
$pdf->AddPage();

// วันที่สร้างรายงาน
$pdf->SetFont('thsarabunnew', '', 14);
$pdf->Cell(0, 10, 'วันที่สร้างรายงาน: ' . date('d/m/Y H:i:s'), 0, 1, 'R');
$pdf->Ln(6);

// ขนาดหน้ากระดาษ A4 = 210 mm, margin 20 ทั้งสองข้าง => เหลือ 170 mm
// กำหนดความกว้างรวม Cell ให้ได้รวม 170
$pdf->SetFont('thsarabunnewb', '', 15);
$pdf->SetFillColor(220, 235, 255);
$pdf->Cell(30, 14, 'วันที่ขาย', 1, 0, 'C', 1);
$pdf->Cell(65, 14, 'ชื่อสินค้า', 1, 0, 'L', 1);
$pdf->Cell(20, 14, 'จำนวน', 1, 0, 'R', 1);
$pdf->Cell(25, 14, 'ราคา/หน่วย', 1, 0, 'R', 1);
$pdf->Cell(30, 14, 'รวม', 1, 1, 'R', 1);

// รายการ
$pdf->SetFont('thsarabunnew', '', 14);
while ($row = $result_sales->fetch_assoc()) {
    $pdf->Cell(30, 14, date('d/m/Y', strtotime($row['sale_date'])), 1, 0, 'C');
    $pdf->Cell(65, 14, $row['product_name'], 1, 0, 'L');
    $pdf->Cell(20, 14, number_format($row['quantity']), 1, 0, 'R');
    $pdf->Cell(25, 14, number_format($row['price'], 2), 1, 0, 'R');
    $pdf->Cell(30, 14, number_format($row['total'], 2), 1, 1, 'R');
}

// รายได้รวม ด้านล่างสุด
$pdf->Ln(8);
$pdf->SetFont('thsarabunnewb', '', 16);
$pdf->Cell(0, 10, 'รายได้รวม: ' . number_format($total_income, 2) . ' บาท', 0, 1, 'R');

ob_end_clean();
$pdf->Output('รายงานรายได้.pdf', 'I');
exit;
?>
