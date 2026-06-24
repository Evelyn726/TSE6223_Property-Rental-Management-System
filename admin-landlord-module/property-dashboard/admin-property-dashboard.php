<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../admin-landlord-module/admin-login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$totalProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property"))['total'];
$availableProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property WHERE availability_status = 'Available'"))['total'];
$occupiedProperties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM property WHERE availability_status = 'Occupied'"))['total'];
$pendingBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Pending'"))['total'];
$approvedBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Approved'"))['total'];
$rejectedBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE booking_status = 'Rejected'"))['total'];
$totalBookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking"))['total'];

$availablePercent = ($totalProperties > 0) ? round(($availableProperties / $totalProperties) * 100) : 0;
$occupiedPercent = ($totalProperties > 0) ? round(($occupiedProperties / $totalProperties) * 100) : 0;
$pendingPercent = ($totalBookings > 0) ? round(($pendingBookings / $totalBookings) * 100) : 0;
$approvedPercent = ($totalBookings > 0) ? round(($approvedBookings / $totalBookings) * 100) : 0;

$recentBookings = mysqli_query($conn, "
SELECT booking.booking_id, booking.booking_date, booking.booking_status,
       renter.name AS renter_name, renter.email AS renter_email,
       property.property_name
FROM booking
INNER JOIN renter ON booking.renter_id = renter.renter_id
INNER JOIN property ON booking.property_id = property.property_id
ORDER BY booking.created_at DESC
LIMIT 5
");

$recentProperties = mysqli_query($conn, "
SELECT property.property_id, property.property_name, property.location,
       property.rental_price, property.availability_status,
       landlord.name AS landlord_name, landlord.email AS landlord_email
FROM property
INNER JOIN landlord ON property.landlord_id = landlord.landlord_id
ORDER BY property.created_at DESC
LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Property Dashboard</title>
    <link rel="icon" type="image/png" href="../images/icon/admin-dashboard-icon.png">
    <link href="../css/font-face.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="../vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="../vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <link href="../vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="../vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="../vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="../vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="../vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="../vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="../vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="../css/theme.css" rel="stylesheet" media="all">
    <style>
        
    </style>
</head>
<body class="animsition">
    <div class="page-wrapper">
        <header class="header-mobile d-block d-lg-none">
            <div class="header-mobile__bar">
                <div class="container-fluid">
                    <div class="header-mobile-inner">
                        <a class="logo" href="../index.html"><img src="../images/icon/property-logo.png" alt="CoolAdmin" /></a>
                        <button class="hamburger hamburger--slider" type="button"><span class="hamburger-box"><span class="hamburger-inner"></span></span></button>
                    </div>
                </div>
            </div>
        </header>

        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#"><img src="../images/icon/property-logo.png" alt="Cool Admin" /></a>
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
                        <li><a href="../manage-bookings/admin-booking-list.php"><i class="fas fa-list-alt"></i> Manage Bookings</a></li>
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
                                    <i class="zmdi zmdi-search"></i>
                                </button> -->
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
                <section class="statistic">
                    <div class="section__content section__content--p30">
                        <div class="container-fluid">
                            <div class="table-data__tool">
                                <div class="table-data__tool-left"></div>
                                <div class="table-data__tool-right">
                                    <a href="../export-property-summary-pdf/export-property-summary-pdf.php" target="_blank">
                                        <button class="au-btn au-btn-icon au-btn--small" style="background-color: gray;">
                                                <i class="zmdi zmdi-plus"></i> Export to PDF</button>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-lg-3">
                                    <div class="statistic__item">
                                        <h2 class="number"><?= h($totalProperties) ?></h2>
                                        <span class="desc">total properties</span>
                                        <div class="icon"><i class="zmdi zmdi-city"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="statistic__item">
                                        <h2 class="number"><?= h($availableProperties) ?></h2>
                                        <span class="desc">available properties</span>
                                        <div class="icon"><i class="zmdi zmdi-home"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="statistic__item">
                                        <h2 class="number"><?= h($pendingBookings) ?></h2>
                                        <span class="desc">pending bookings</span>
                                        <div class="icon"><i class="zmdi zmdi-calendar-note"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="statistic__item">
                                        <h2 class="number"><?= h($approvedBookings) ?></h2>
                                        <span class="desc">approved bookings</span>
                                        <div class="icon"><i class="zmdi zmdi-check-circle"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="section__content section__content--p30">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xl-8">
                                    <div class="recent-report2">
                                        <h3 class="title-3">property and booking summary</h3>
                                        <div class="chart-info">
                                            <div class="chart-info__left">
                                                <div class="chart-note"><span class="dot dot--blue"></span><span>property status</span></div>
                                                <div class="chart-note"><span class="dot dot--green"></span><span>booking status</span></div>
                                            </div>
                                            <div class="chart-info-right">
                                                <a href="../property-list/admin-property-list.php" class="au-btn au-btn--blue au-btn--small dashboard-action">View Properties</a>
                                                <a href="../manage-bookings/admin-booking-list.php" class="au-btn au-btn--green au-btn--small dashboard-action">Manage Bookings</a>
                                            </div>
                                        </div>
                                        <div class="recent-report__chart"><canvas id="property-summary-chart"></canvas></div>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div class="task-progress">
                                        <h3 class="title-3">module progress</h3>
                                        <div class="au-skill-container">
                                            <div class="au-progress">
                                                <span class="au-progress__title">Available Properties</span>
                                                <div class="au-progress__bar" style="margin-top: 15px;">
                                                    <div class="au-progress__inner js-progressbar-simple" role="progressbar" data-transitiongoal="<?= h($availablePercent) ?>">
                                                        <span class="au-progress__value js-value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-progress">
                                                <span class="au-progress__title">Occupied Properties</span>
                                                <div class="au-progress__bar" style="margin-top: 15px;">
                                                    <div class="au-progress__inner js-progressbar-simple" role="progressbar" data-transitiongoal="<?= h($occupiedPercent) ?>">
                                                        <span class="au-progress__value js-value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-progress">
                                                <span class="au-progress__title">Pending Bookings</span>
                                                <div class="au-progress__bar" style="margin-top: 15px;">
                                                    <div class="au-progress__inner js-progressbar-simple" role="progressbar" data-transitiongoal="<?= h($pendingPercent) ?>">
                                                        <span class="au-progress__value js-value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="au-progress">
                                                <span class="au-progress__title">Approved Bookings</span>
                                                <div class="au-progress__bar" style="margin-top: 15px;">
                                                    <div class="au-progress__inner js-progressbar-simple" role="progressbar" data-transitiongoal="<?= h($approvedPercent) ?>">
                                                        <span class="au-progress__value js-value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <a href="../add-property/admin-property-add.php" class="au-btn au-btn--blue au-btn--block">Add Property</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="section__content section__content--p30">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="user-data m-b-40">
                                        <h3 class="title-3 m-b-30"><i class="zmdi zmdi-account-calendar"></i>recent booking requests</h3>
                                        <div class="table-responsive table-data">
                                            <table class="table">
                                                <thead><tr><td>renter</td><td>property</td><td>status</td><td></td></tr></thead>
                                                <tbody>
                                                    <?php if ($recentBookings && mysqli_num_rows($recentBookings) > 0) { ?>
                                                        <?php while ($row = mysqli_fetch_assoc($recentBookings)) { ?>
                                                            <tr>
                                                                <td><div class="table-data__info"><h6><?= h($row['renter_name']) ?></h6><span><a href="#"><?= h($row['renter_email']) ?></a></span></div></td>
                                                                <td><?= h($row['property_name']) ?><br><small><?= h($row['booking_date']) ?></small></td>
                                                                <td><span class="status--<?= strtolower(h($row['booking_status'])) ?>"><?= h($row['booking_status']) ?></span></td>
                                                                <td><a href="../booking-approval-page/admin-booking-approval.php?booking_id=<?= h($row['booking_id']) ?>"><span class="more"><i class="zmdi zmdi-more"></i></span></a></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr><td colspan="4">No booking records found.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="user-data__footer"><a href="../manage-bookings/admin-booking-list.php" class="au-btn au-btn-load">view all bookings</a></div>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div class="user-data m-b-40">
                                        <h3 class="title-3 m-b-30"><i class="zmdi zmdi-home"></i>recent properties</h3>
                                        <div class="table-responsive table-data">
                                            <table class="table">
                                                <thead><tr><td>property</td><td>landlord</td><td>status</td><td>rent</td></tr></thead>
                                                <tbody>
                                                    <?php if ($recentProperties && mysqli_num_rows($recentProperties) > 0) { ?>
                                                        <?php while ($row = mysqli_fetch_assoc($recentProperties)) { ?>
                                                            <tr>
                                                                <td><div class="table-data__info"><h6><?= h($row['property_name']) ?></h6><span><a href="#"><?= h($row['location']) ?></a></span></div></td>
                                                                <td><?= h($row['landlord_name']) ?><br><small><?= h($row['landlord_email']) ?></small></td>
                                                                <td><span class="status--<?= strtolower(h($row['availability_status'])) ?>"><?= h($row['availability_status']) ?></span></td>
                                                                <td>RM <?= number_format($row['rental_price'], 2) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr><td colspan="4">No property records found.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="user-data__footer"><a href="../property-list/admin-property-list.php" class="au-btn au-btn-load">view all properties</a></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row"><div class="col-md-12"><div class="copyright"><p>Property Rental Management System (2026).</p></div></div></div>
                        </div>
                    </div>
                </section>
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
    <script src="../vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="../vendor/counter-up/jquery.counterup.min.js"></script>
    <script src="../vendor/circle-progress/circle-progress.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="../vendor/select2/select2.min.js"></script>
    <script src="../js/main.js"></script>

    <script>
        var ctx = document.getElementById("property-summary-chart");
        if (ctx) {
            ctx.height = 150;
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["Available", "Occupied", "Pending", "Approved", "Rejected"],
                    datasets: [{
                        label: "Count",
                        data: [
                            <?= (int)$availableProperties ?>,
                            <?= (int)$occupiedProperties ?>,
                            <?= (int)$pendingBookings ?>,
                            <?= (int)$approvedBookings ?>,
                            <?= (int)$rejectedBookings ?>
                        ],
                        backgroundColor: [
                            "rgba(0, 181, 233, 0.8)",   // Available - blue
                            "rgba(0, 181, 233, 0.8)",   // Occupied - blue
                            "rgba(0, 173, 95, 0.8)",   // Pending - green
                            "rgba(0, 173, 95, 0.8)",   // Approved - green
                            "rgba(0, 173, 95, 0.8)"    // Rejected - green
                        ],
                        borderColor: [
                            "rgba(0, 181, 233, 1)",
                            "rgba(0, 181, 233, 1)",
                            "rgba(0, 173, 95, 1)",
                            "rgba(0, 173, 95, 1)",
                            "rgba(0, 173, 95, 1)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: { display: false },
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, precision: 0 }
                        }]
                    }
                }
            });
        }

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
