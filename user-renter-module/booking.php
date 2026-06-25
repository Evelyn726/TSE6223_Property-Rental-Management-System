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

if(!isset($_GET['id']))
{
    header("Location: properties.php");
    exit();
}

$renter_id = $_SESSION['renter_id'];
$property_id = (int)$_GET['id'];

$query = mysqli_query($conn,
"SELECT *
FROM property
WHERE property_id='$property_id'");

if(!$query)
{
    die("Database Error: " . mysqli_error($conn));
}

if(mysqli_num_rows($query)==0)
{
    header("Location: properties.php");
    exit();
}

$property = mysqli_fetch_assoc($query);

if($property['availability_status'] != 'Available')
{
    header("Location: properties.php");
    exit();
}

$message = "";

if(isset($_POST['book']))
{
    $booking_date = $_POST['booking_date'];

    if($booking_date < date('Y-m-d'))
    {
        $message = "<div class='alert alert-danger'>
                        Booking date cannot be in the past.
                    </div>";
    }
    elseif(empty($booking_date))
    {
        $message = "<div class='alert alert-danger'>
                        Please select booking date.
                    </div>";
    }
    else
    {
        $check = mysqli_query($conn,
        "SELECT *
        FROM booking
        WHERE renter_id='$renter_id'
        AND property_id='$property_id'
        AND booking_status='Pending'");

        if(!$check)
        {
            die("Database Error: " . mysqli_error($conn));
        }

        if(mysqli_num_rows($check) > 0)
        {
            $message = "<div class='alert alert-warning'>
                            You already have a pending booking for this property.
                        </div>";
        }
        else
        {
            $insert = mysqli_query($conn,
            "INSERT INTO booking
            (renter_id,property_id,booking_date)
            VALUES
            ('$renter_id',
            '$property_id',
            '$booking_date')");

            if($insert)
            {
                $message = "<div class='alert alert-success'>
                                Booking submitted successfully.
                            </div>";
            }
            else
            {
                $message = "<div class='alert alert-danger'>
                                Booking failed.
                            </div>";
            }
        }
    }
}
?>

<div class="hero page-inner overlay"
style="background-image: url('images/hero_bg_2.jpg');">

<div class="container">

<div class="row justify-content-center align-items-center">

<div class="col-lg-9 text-center mt-5">

<h1 class="heading">

Book Property

</h1>

</div>

</div>

</div>

</div>

<div class="section">

<div class="container">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card shadow border-0 p-5">

<h3 class="text-primary mb-4">

Booking Form

</h3>

<?php echo $message; ?>

<p>

<strong>Property:</strong>

<?php echo htmlspecialchars($property['property_name']); ?>

</p>

<p>

<strong>Location:</strong>

<?php echo htmlspecialchars($property['location']); ?>

</p>

<p>

<strong>Rental Price:</strong>

RM <?php echo number_format($property['rental_price'],2); ?>

</p>

<p>

<strong>Property Type:</strong>

<?php echo htmlspecialchars($property['property_type']); ?>

</p>

<form method="POST">

<div class="mb-4">

<label class="form-label">

Booking Date

</label>

<input
type="date"
name="booking_date"
class="form-control"
min="<?php echo date('Y-m-d');?>"
required>

</div>

<button
type="submit"
name="book"
class="btn btn-primary w-100">

Submit Booking

</button>

<div class="text-center mt-3">

<a
href="property-single.php?id=<?php echo $property_id; ?>"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php
include("includes/footer.php");
?>