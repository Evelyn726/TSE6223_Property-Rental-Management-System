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

$message = "";

if(isset($_POST['submit']))
{
    $rental_id = (int)$_POST['rental_id'];
    $issue = mysqli_real_escape_string($conn, trim($_POST['issue_description']));

    if(empty($issue))
    {
        $message = "<div class='alert alert-danger'>
                        Please enter the issue description.
                    </div>";
    }
    else
    {
        $check = mysqli_query($conn,
        "SELECT *
         FROM rental
         WHERE rental_id='$rental_id'
         AND renter_id='$renter_id'");

        if(mysqli_num_rows($check)==0)
        {
            $message = "<div class='alert alert-danger'>
                            Invalid rental selected.
                        </div>";
        }
        else
        {
            $today = date("Y-m-d");

            $insert = mysqli_query($conn,
            "INSERT INTO maintenance
            (rental_id,request_date,issue_description)
            VALUES
            ('$rental_id',
             '$today',
             '$issue')");

            if($insert)
            {
                $message = "<div class='alert alert-success'>
                                Maintenance request submitted successfully.
                            </div>";
            }
            else
            {
                $message = "<div class='alert alert-danger'>
                                Failed to submit maintenance request.
                            </div>";
            }
        }
    }
}
?>

<div class="hero page-inner overlay"
style="background-image:url('images/hero_bg_2.jpg');">

<div class="container">

<div class="row justify-content-center align-items-center">

<div class="col-lg-9 text-center mt-5">

<h1 class="heading">

Maintenance Request

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
Maintenance
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

<h3 class="text-primary mb-4">

Submit Maintenance Request

</h3>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">

Rental Property

</label>

<select
name="rental_id"
class="form-control"
required>

<option value="">

Select Rental

</option>

<?php

$rental = mysqli_query($conn,

"SELECT rental.rental_id,
property.property_name

FROM rental

INNER JOIN property

ON rental.property_id = property.property_id

WHERE rental.renter_id='$renter_id'
AND rental.rental_status='Active'

ORDER BY property.property_name");

while($r = mysqli_fetch_assoc($rental))
{

?>

<option value="<?php echo $r['rental_id']; ?>">

<?php echo htmlspecialchars($r['property_name']); ?>

</option>

<?php

}

?>

</select>

</div>

<div class="mb-4">

<label class="form-label">

Issue Description

</label>

<textarea
name="issue_description"
class="form-control"
rows="5"
placeholder="Describe the maintenance issue..."
required></textarea>

</div>

<button
type="submit"
name="submit"
class="btn btn-primary">

Submit Request

</button>

</form>

</div>

<div class="card shadow border-0 p-5">

<h3 class="text-primary mb-4">

My Maintenance Requests

</h3>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead class="table-light">

<tr>

<th>Request ID</th>

<th>Property</th>

<th>Request Date</th>

<th>Issue</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$query = mysqli_query($conn,

"SELECT maintenance.*,
property.property_name

FROM maintenance

INNER JOIN rental

ON maintenance.rental_id = rental.rental_id

INNER JOIN property

ON rental.property_id = property.property_id

WHERE rental.renter_id='$renter_id'

ORDER BY maintenance.created_at DESC");

if(!$query)
{
    die("Database Error: ".mysqli_error($conn));
}

if(mysqli_num_rows($query)>0)
{
    while($row=mysqli_fetch_assoc($query))
    {

?>

<tr>

<td>

<?php echo $row['maintenance_id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['property_name']); ?>

</td>

<td>

<?php echo $row['request_date']; ?>

</td>

<td>

<?php echo nl2br(htmlspecialchars($row['issue_description'])); ?>

</td>

<td>

<?php

$status = $row['status'];

if($status=="Pending")
{
    echo "<span class='badge bg-warning'>Pending</span>";
}
elseif($status=="In Progress")
{
    echo "<span class='badge bg-info text-dark'>In Progress</span>";
}
else
{
    echo "<span class='badge bg-success'>Completed</span>";
}

?>

</td>

</tr>

<?php

    }
}
else
{

?>

<tr>

<td colspan="5" class="text-center">

No maintenance requests found.

</td>

</tr>

<?php

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