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
?>

<div class="hero page-inner overlay"
style="background-image:url('images/hero_bg_2.jpg');">

    <div class="container">

        <div class="row justify-content-center align-items-center">

            <div class="col-lg-9 text-center mt-5">

                <h1 class="heading">
                    Payment Status
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
                            Payment Status
                        </li>

                    </ol>

                </nav>

            </div>

        </div>

    </div>

</div>

<div class="section">

    <div class="container">

        <div class="card shadow border-0 p-5">

            <h3 class="text-primary mb-4">

                My Payments

            </h3>

            <?php

            $query = mysqli_query($conn,

            "SELECT payment.*,
                    property.property_name

            FROM payment

            INNER JOIN rental
            ON payment.rental_id = rental.rental_id

            INNER JOIN property
            ON rental.property_id = property.property_id

            WHERE rental.renter_id = '$renter_id'

            ORDER BY payment.payment_date DESC");

            if(!$query)
            {
                die("Database Error: " . mysqli_error($conn));
            }

            ?>

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-light">

                        <tr>

                            <th>Payment ID</th>
                            <th>Property</th>
                            <th>Payment Date</th>
                            <th>Amount (RM)</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php

                    if(mysqli_num_rows($query) > 0)
                    {
                        while($row = mysqli_fetch_assoc($query))
                        {
                    ?>

                        <tr>

                            <td>

                                <?php echo $row['payment_id']; ?>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($row['property_name']); ?>

                            </td>

                            <td>

                                <?php echo $row['payment_date']; ?>

                            </td>

                            <td>

                                <?php echo number_format($row['amount'],2); ?>

                            </td>

                            <td>

                                <?php

                                if($row['payment_status'] == "Paid")
                                {
                                    echo "<span class='badge bg-success'>Paid</span>";
                                }
                                else
                                {
                                    echo "<span class='badge bg-danger'>Unpaid</span>";
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

                                No payment records found.

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