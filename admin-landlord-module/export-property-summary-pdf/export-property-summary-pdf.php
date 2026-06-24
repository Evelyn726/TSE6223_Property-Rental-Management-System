<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../admin-landlord-module/admin-login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

require('../../fpdf/fpdf.php');

$totalProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property"))['total'];
$availableProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property WHERE availability_status = 'Available'"))['total'];
$occupiedProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property WHERE availability_status = 'Occupied'"))['total'];
$unavailableProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property WHERE availability_status = 'Unavailable'"))['total'];

$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking"))['total'];
$pendingBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Pending'"))['total'];
$approvedBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Approved'"))['total'];
$rejectedBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Rejected'"))['total'];

$recentProperties = mysqli_query($conn, "
SELECT 
    property.property_name,
    property.location,
    property.rental_price,
    property.availability_status,
    landlord.name AS landlord_name
FROM property
INNER JOIN landlord
    ON property.landlord_id = landlord.landlord_id
ORDER BY property.created_at DESC
LIMIT 10
");

$recentBookings = mysqli_query($conn, "
SELECT 
    booking.booking_id,
    booking.booking_date,
    booking.booking_status,
    renter.name AS renter_name,
    property.property_name
FROM booking
INNER JOIN renter
    ON booking.renter_id = renter.renter_id
INNER JOIN property
    ON booking.property_id = property.property_id
ORDER BY booking.created_at DESC
LIMIT 10
");

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Property and Booking Summary Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Generated on: ' . date('Y-m-d H:i:s'), 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function SectionTitle($title)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(66, 114, 215);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, 8, $title, 0, 1, 'L', true);
        $this->SetTextColor(0, 0, 0);
        $this->Ln(3);
    }

    function SummaryRow($label, $value)
    {
        $this->SetFont('Arial', '', 11);
        $this->Cell(80, 8, $label, 1);
        $this->Cell(40, 8, $value, 1, 1, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 15);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, 'Admin/Landlord: ' . $_SESSION['landlord_name'], 0, 1);
$pdf->Cell(0, 8, 'Email: ' . $_SESSION['landlord_email'], 0, 1);
$pdf->Ln(5);

$pdf->SectionTitle('Overall Summary');
$pdf->SummaryRow('Total Properties', $totalProperties);
$pdf->SummaryRow('Available Properties', $availableProperties);
$pdf->SummaryRow('Occupied Properties', $occupiedProperties);
$pdf->SummaryRow('Unavailable Properties', $unavailableProperties);
$pdf->SummaryRow('Total Bookings', $totalBookings);
$pdf->SummaryRow('Pending Bookings', $pendingBookings);
$pdf->SummaryRow('Approved Bookings', $approvedBookings);
$pdf->SummaryRow('Rejected Bookings', $rejectedBookings);

$pdf->Ln(8);

$pdf->SectionTitle('Recent Properties');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 8, 'Property', 1);
$pdf->Cell(40, 8, 'Location', 1);
$pdf->Cell(35, 8, 'Landlord', 1);
$pdf->Cell(30, 8, 'Rent (RM)', 1);
$pdf->Cell(35, 8, 'Status', 1, 1);

$pdf->SetFont('Arial', '', 8);
if ($recentProperties && mysqli_num_rows($recentProperties) > 0) {
    while ($row = mysqli_fetch_assoc($recentProperties)) {
        $pdf->Cell(45, 8, substr($row['property_name'], 0, 22), 1);
        $pdf->Cell(40, 8, substr($row['location'], 0, 20), 1);
        $pdf->Cell(35, 8, substr($row['landlord_name'], 0, 18), 1);
        $pdf->Cell(30, 8, number_format($row['rental_price'], 2), 1, 0, 'R');
        $pdf->Cell(35, 8, $row['availability_status'], 1, 1);
    }
} else {
    $pdf->Cell(185, 8, 'No property records found.', 1, 1, 'C');
}

$pdf->Ln(8);

$pdf->SectionTitle('Recent Booking Requests');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(25, 8, 'Booking ID', 1);
$pdf->Cell(45, 8, 'Renter', 1);
$pdf->Cell(55, 8, 'Property', 1);
$pdf->Cell(35, 8, 'Date', 1);
$pdf->Cell(25, 8, 'Status', 1, 1);

$pdf->SetFont('Arial', '', 8);
if ($recentBookings && mysqli_num_rows($recentBookings) > 0) {
    while ($row = mysqli_fetch_assoc($recentBookings)) {
        $pdf->Cell(25, 8, '#' . $row['booking_id'], 1);
        $pdf->Cell(45, 8, substr($row['renter_name'], 0, 22), 1);
        $pdf->Cell(55, 8, substr($row['property_name'], 0, 28), 1);
        $pdf->Cell(35, 8, $row['booking_date'], 1);
        $pdf->Cell(25, 8, $row['booking_status'], 1, 1);
    }
} else {
    $pdf->Cell(185, 8, 'No booking records found.', 1, 1, 'C');
}

$pdf->Output('D', 'Property_Booking_Summary_Report.pdf');
exit();
?>
