<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../admin-landlord-module/admin-login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

$error = "";
$success = "";

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id']) && isset($_POST['booking_action'])) {
    $booking_id = (int)$_POST['booking_id'];
    $booking_action = $_POST['booking_action'];

    if (in_array($booking_action, ['Approved', 'Rejected'])) {
        $get_booking = "SELECT property_id FROM booking WHERE booking_id = ?";
        $stmt = mysqli_prepare($conn, $get_booking);
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        $booking_result = mysqli_stmt_get_result($stmt);
        $booking_data = mysqli_fetch_assoc($booking_result);

        if ($booking_data) {
            mysqli_begin_transaction($conn);
            try {
                $update_booking = "UPDATE booking SET booking_status = ? WHERE booking_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_booking);
                mysqli_stmt_bind_param($update_stmt, "si", $booking_action, $booking_id);
                mysqli_stmt_execute($update_stmt);

                if ($booking_action == 'Approved') {
                    $property_status = 'Occupied';
                    $property_id = (int)$booking_data['property_id'];
                    $update_property = "UPDATE property SET availability_status = ? WHERE property_id = ?";
                    $property_stmt = mysqli_prepare($conn, $update_property);
                    mysqli_stmt_bind_param($property_stmt, "si", $property_status, $property_id);
                    mysqli_stmt_execute($property_stmt);
                }

                mysqli_commit($conn);
                $success = "Booking has been " . $booking_action . " successfully.";
            } catch (Exception $e) {
                mysqli_rollback($conn);
                $error = "Error updating booking.";
            }
        } else {
            $error = "Booking record not found.";
        }
    }
}

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$booking = null;

if ($booking_id > 0) {
    $query = "SELECT booking.booking_id, booking.booking_date, booking.booking_status, booking.created_at,
                     renter.name AS renter_name, renter.email AS renter_email, renter.phone_number AS renter_phone,
                     property.property_id, property.property_name, property.location, property.property_type,
                     property.rental_price, property.description, property.availability_status
              FROM booking
              INNER JOIN renter ON booking.renter_id = renter.renter_id
              INNER JOIN property ON booking.property_id = property.property_id
              WHERE booking.booking_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $booking_id);
    mysqli_stmt_execute($stmt);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$pending_query = "SELECT booking.booking_id, renter.name AS renter_name, property.property_name
                  FROM booking
                  INNER JOIN renter ON booking.renter_id = renter.renter_id
                  INNER JOIN property ON booking.property_id = property.property_id
                  WHERE booking.booking_status = 'Pending'
                  ORDER BY booking.created_at DESC";
$pending_result = mysqli_query($conn, $pending_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Booking Approval</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/icon/admin-booking-approval-icon.png">

    <!-- Fontfaces CSS-->
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="../vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="../vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="../vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="../css/theme.css" rel="stylesheet" media="all">

</head>
<body class="animsition">
    <div class="page-wrapper">
        
        <!-- HEADER MOBILE-->
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="../index.html">
                            <img src="../images/icon/property-logo.png" alt="CoolAdmin" />
                        </a>
                        <button class="hamburger hamburger--slider" type="button">
                            <span class="hamburger-box">
                                <span class="hamburger-inner"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </header>
        <!-- END HEADER MOBILE-->

        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="../images/icon/property-logo.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <!-- <li class="has-sub">
                            <a class="js-arrow" href="#"><i class="fas fa-copy"></i> Login</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li><a href="../login.html">Login</a></li>
                                <li><a href="../register.html">Register</a></li>
                                <li><a href="../forget-pass.html">Forget Password</a></li>
                            </ul>
                        </li> -->
                        <li><a href="../property-dashboard/admin-property-dashboard.php"><i class="fas fa-tachometer-alt"></i> Property Dashboard</a></li>
                        <li><a href="../property-list/admin-property-list.php"><i class="fas fa-building"></i> Property List</a></li>
                        <!-- <li><a href="../add-property/admin-property-add.php"><i class="fas fa-plus-circle"></i> Add Property</a></li>
                        <li><a href="../edit-property/admin-property-edit.php"><i class="fas fa-edit"></i> Edit Property</a></li> -->
                        <li><a href="../manage-bookings/admin-booking-list.php"><i class="fas fa-list-alt"></i> Manage Bookings</a></li>
                        <!-- <li><a href="../booking-approval-page/admin-booking-approval.php"><i class="fas fa-check-circle"></i> Booking Approval</a></li> -->
                        <li><a href="../rental-management/admin-rental-list.php"><i class="fas fa-home fa-fw"></i> Rental</a></li>
                        <li><a href="../payment-management/admin-payment-list.php"><i class="fas fa-money-bill-alt fa-fw"></i> Payment Record</a></li>
                        <li><a href="../maintenance-management/admin-maintenance-list.php"><i class="fas fa-clipboard-list fa-fw"></i> Maintenance</a></li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="page-container">
            
            <!-- HEADER DESKTOP-->
            <header class="header-desktop">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap">
                            <form class="form-header" action="" method="POST">
                                <!-- <input class="au-input au-input--xl" type="text" name="search" placeholder="Search for datas &amp; reports..." />
                                <button class="au-btn--submit" type="submit">
                                    <i class="zmdi zmdi-search"></i> -->
                                </button>
                            </form>
                            <div class="header-button">
                                <div class="account-wrap">
                                    <div class="account-item clearfix js-item-menu">
                                        <div class="image">
                                            <img src="../images/icon/landlord-avatar.png" alt="Admin" />
                                        </div>
                                        <div class="content">
                                            <a class="js-acc-btn" href="#"><?php echo $_SESSION['landlord_name']; ?></a>
                                        </div>
                                        <div class="account-dropdown js-dropdown">
                                            <div class="info clearfix">
                                                <div class="image">
                                                    <a href="#">
                                                        <img src="../images/icon/landlord-avatar.png" alt="Admin" />
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h5 class="name">
                                                        <a href="#"><?php echo $_SESSION['landlord_name']; ?></a>
                                                    </h5>
                                                    <span class="email"><?php echo $_SESSION['landlord_email']; ?></span>
                                                </div>
                                            </div>
                                            <!-- <div class="account-dropdown__body">
                                                <div class="account-dropdown__item">
                                                    <a href="#">
                                                        <i class="zmdi zmdi-account"></i>Account</a>
                                                </div>
                                            </div> -->
                                            <div class="account-dropdown__footer">
                                                <a href="../admin-logout.php">
                                                    <i class="zmdi zmdi-power"></i>Logout</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <h3 class="title-5 m-b-35">Booking Approval</h3>

                        <?php if (!empty($error)) { ?><div class="alert alert-danger"><?= h($error) ?></div><?php } ?>
                        <?php if (!empty($success)) { ?><div class="alert alert-success"><?= h($success) ?></div><?php } ?>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">Pending Booking Requests</div>
                                    <div class="card-body">
                                        <?php if ($pending_result && mysqli_num_rows($pending_result) > 0) { ?>
                                            <?php while ($row = mysqli_fetch_assoc($pending_result)) { ?>
                                                <div class="pending-item">
                                                    <a href="admin-booking-approval.php?booking_id=<?= h($row['booking_id']) ?>">
                                                        #<?= h($row['booking_id']) ?> - <?= h($row['property_name']) ?>
                                                    </a><br>
                                                    <small><?= h($row['renter_name']) ?></small>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p>No pending booking requests.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">Review Booking Request</div>
                                    <div class="card-body">
                                        <?php if ($booking) { ?>
                                            <table class="detail-table" style="width:100%;">
                                                <tr><td class="detail-label">Booking ID</td><td>#<?= h($booking['booking_id']) ?></td></tr>
                                                <tr><td class="detail-label">Renter Name</td><td><?= h($booking['renter_name']) ?></td></tr>
                                                <tr><td class="detail-label">Renter Email</td><td><?= h($booking['renter_email']) ?></td></tr>
                                                <tr><td class="detail-label">Phone Number</td><td><?= h($booking['renter_phone']) ?></td></tr>
                                                <tr><td class="detail-label">Property Name</td><td><?= h($booking['property_name']) ?></td></tr>
                                                <tr><td class="detail-label">Location</td><td><?= h($booking['location']) ?></td></tr>
                                                <tr><td class="detail-label">Property Type</td><td><?= h($booking['property_type']) ?></td></tr>
                                                <tr><td class="detail-label">Rental Price</td><td>RM <?= number_format($booking['rental_price'], 2) ?></td></tr>
                                                <tr><td class="detail-label">Booking Date</td><td><?= h($booking['booking_date']) ?></td></tr>
                                                <tr><td class="detail-label">Property Status</td><td><?= h($booking['availability_status']) ?></td></tr>
                                                <tr><td class="detail-label">Booking Status</td><td><?= h($booking['booking_status']) ?></td></tr>
                                            </table>

                                            <br>

                                            <?php if ($booking['booking_status'] == 'Pending') { ?>
                                                <form method="post" action="" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?= h($booking['booking_id']) ?>">
                                                    <input type="hidden" name="booking_action" value="Approved">
                                                    <button type="submit" class="au-btn au-btn--green au-btn--small" onclick="return confirm('Approve this booking request?')">Approve</button>
                                                </form>

                                                <form method="post" action="" style="display:inline;">
                                                    <input type="hidden" name="booking_id" value="<?= h($booking['booking_id']) ?>">
                                                    <input type="hidden" name="booking_action" value="Rejected">
                                                    <button type="submit" class="au-btn au-btn--red au-btn--small" style="background:#dc2626;color:white;" onclick="return confirm('Reject this booking request?')">Reject</button>
                                                </form>
                                            <?php } else { ?>
                                                <div class="alert alert-info">This booking request has already been processed.</div>
                                            <?php } ?>

                                            <a href="../manage-bookings/admin-booking-list.php" class="au-btn au-btn--grey au-btn--small">Back to Booking List</a>
                                        <?php } else { ?>
                                            <div class="alert alert-warning">Please select a pending booking request from the left panel or from the booking list.</div>
                                            <a href="../manage-bookings/admin-booking-list.php" class="au-btn au-btn--blue au-btn--small">View Booking List</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row"><div class="col-md-12"><div class="copyright"><p>Property Rental Management System (2026).</p></div></div></div>
                    </div>
                </div>
            </div>
        </div>
   </div>
    
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <script src="../vendor/slick/slick.min.js"></script>
    <script src="../vendor/wow/wow.min.js"></script>
    <script src="../vendor/animsition/animsition.min.js"></script>
    <script src="../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../vendor/select2/select2.min.js"></script>
    <script src="../js/main.js"></script>

    <script>
        $(document).ready(function() {
            var currentUrl = window.location.pathname;
            
            $('li a').each(function() {
                var href = $(this).attr('href').replace('../', '');
                if (currentUrl.indexOf(href) !== -1) {
                    $(this).parent('li').addClass('active');
                }
            });
        });
    </script>
</body>
</html>
<!-- end document-->
