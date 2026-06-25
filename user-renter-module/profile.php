<?php
session_start();

include("includes/db.php");
include("includes/header.php");
include("includes/navbar.php");

if(!isset($_SESSION['renter_id']))
{
    header("Location: login.php");
    exit();
}

$renter_id = $_SESSION['renter_id'];

$query = mysqli_query($conn,
"SELECT *
 FROM renter
 WHERE renter_id='$renter_id'");

if(!$query)
{
    die("Database Error: " . mysqli_error($conn));
}

$renter = mysqli_fetch_assoc($query);

if(!$renter)
{
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<div class="hero page-inner overlay"
style="background-image:url('images/hero_bg_3.jpg');">

<div class="container">

<div class="row justify-content-center align-items-center">

<div class="col-lg-9 text-center mt-5">

<h1 class="heading">

My Profile

</h1>

<nav
aria-label="breadcrumb"
data-aos="fade-up"
data-aos-delay="200">

    <ol class="breadcrumb text-center justify-content-center">

        <li class="breadcrumb-item">
            <a href="index.php">Home</a>
        </li>

        <li class="breadcrumb-item active text-white-50">
            My Profile
        </li>

    </ol>

</nav>

</div>

</div>

</div>

</div>

<div class="section">

<div class="container">

<div class="card shadow border-0 p-5 mb-5">

<h3 class="text-primary">

Personal Information

</h3>

<hr>

<p>

<strong>Name:</strong>

<?php echo htmlspecialchars($renter['name']); ?>

</p>

<p>

<strong>Email:</strong>

<?php echo htmlspecialchars($renter['email']); ?>

</p>

<p>

<strong>Phone Number:</strong>

<?php echo htmlspecialchars($renter['phone_number']); ?>

</p>

<div class="text-center mt-4">

    <a
    href="logout.php"
    class="btn btn-danger px-4">

        Logout

    </a>

</div>

</div>

<div class="card shadow border-0 p-5">

<h3 class="text-primary mb-4">

My Booking History

</h3>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>

<th>Booking ID</th>

<th>Property</th>

<th>Booking Date</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$booking = mysqli_query($conn,

"SELECT booking.*,
property.property_name

FROM booking

INNER JOIN property

ON booking.property_id=property.property_id

WHERE booking.renter_id='$renter_id'

ORDER BY booking.created_at DESC");

if(!$booking)
{
    die("Database Error: " . mysqli_error($conn));
}

if(mysqli_num_rows($booking)>0)
{

while($row=mysqli_fetch_assoc($booking))
{

?>

<tr>

<td>

<?php echo $row['booking_id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['property_name']); ?>

</td>

<td>

<?php echo $row['booking_date']; ?>

</td>

<td>

<?php

$status=$row['booking_status'];

if($status=="Pending")
{

echo "<span class='badge bg-warning'>Pending</span>";

}
elseif($status=="Approved")
{

echo "<span class='badge bg-success'>Approved</span>";

}
else
{

echo "<span class='badge bg-danger'>Rejected</span>";

}

?>

</td>

</tr>

<?php

}

}
else
{

echo "<tr>

<td colspan='4' class='text-center'>

No booking records found.

</td>

</tr>";

}

?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<?php
include("includes/footer.php");
?>