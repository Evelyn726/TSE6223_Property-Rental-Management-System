<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "property_rental_management"
);

$sql = "
SELECT 
    m.maintenance_id,
    m.rental_id,
    r.name AS renter_name,
    m.request_date,
    m.issue_description,
    m.status
FROM maintenance m
JOIN rental rt ON m.rental_id = rt.rental_id
JOIN renter r ON rt.renter_id = r.renter_id
";

$result = mysqli_query($conn, $sql);
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
    <title>Maintenance</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/icon/admin-maintenance-icon.png">

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

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="#">
                    <img src="../images/icon/property-logo.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <!-- Página con submenú -->
                        <!-- <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-copy"></i> Login</a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="../login.html">Login</a>
                                </li>
                                <li>
                                    <a href="../register.html">Register</a>
                                </li>
                                <li>
                                    <a href="../forget-pass.html">Forget Password</a>
                                </li>
                            </ul>
                        </li> -->
                        <!-- Dashboard -->
                        <li>
                            <a href="../property-dashboard/admin-property-dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Property Dashboard</a>
                        </li>
                        <!-- Property List -->
                        <li>
                            <a href="../property-list/admin-property-list.php">
                                <i class="fas fa-building"></i> Property List</a>
                        </li>
                        <!-- Booking List -->
                        <li>
                            <a href="../manage-bookings/admin-booking-list.php">
                                <i class="fas fa-list-alt"></i> Manage Bookings</a>
                        </li>                       
                        <!-- Rental -->
                        <li>
                            <a href="../rental-management/admin-rental-list.php">
                                <i class="fas fa-home fa-fw"></i> Rental</a>
                        </li>
                        <!-- Payment -->
                        <li>
                            <a href="../payment-management/admin-payment-list.php">
                               <i class="fas fa-money-bill-alt fa-fw"></i> Payment Record</a>
                        </li>
                        <!-- Maintenance -->
                        <li>
                            <a href="../maintenance-management/admin-maintenance-list.php">
                               <i class="fas fa-clipboard-list fa-fw"></i> Maintenance</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
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

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="title-5 m-b-35">Maintenance Request List</h3>
                                <div class="table-data__tool">
                                    <div class="table-data__tool-left">
                                        <div class="rs-select2--light rs-select2--md">
                                            <select class="js-select2" name="property">
                                                <option selected="selected">All Requests</option>
                                                <option value="">By Date</option>
                                                <option value="">By Status</option>
                                            </select>
                                            <div class="dropDownSelect2"></div>
                                        </div>
                                        <button class="au-btn-filter">
                                            <i class="zmdi zmdi-filter-list"></i>filters</button>
                                    </div>
                                    <div class="table-data__tool-right">
                                        <button class="au-btn au-btn-icon au-btn--small" style="background-color: gray;">
                                            <i class="zmdi zmdi-plus"></i> Export to PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="col-lg-13">
                                <div class="table-responsive table--no-card m-b-30">
                                    <table class="table table-borderless table-striped table-earning">
                                        <thead>
                                            <tr>
                                                <th>Maintenance ID</th>
                                                <th>Rental ID</th>
                                                <th>Renter Name</th>
                                                <th>Request Date</th>
                                                <th>Issue Description</th>
                                                <th>Maintenance Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if ($result && mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>";
                                                    echo "<td>{$row['maintenance_id']}</td>";
                                                    echo "<td>{$row['rental_id']}</td>";
                                                    echo "<td>{$row['renter_name']}</td>";
                                                    echo "<td>{$row['request_date']}</td>";
                                                    echo "<td>{$row['issue_description']}</td>";
                                                    echo "<td>{$row['status']}</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No maintenance records found</td></tr>";
                                            }
                                            ?>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Property Rental Management System (2026).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Jquery JS-->
    <script src="../vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="../vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="../vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="../vendor/slick/slick.min.js">
    </script>
    <script src="../vendor/wow/wow.min.js"></script>
    <script src="../vendor/animsition/animsition.min.js"></script>
    <script src="../vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="../vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="../vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="../vendor/circle-progress/circle-progress.min.js"></script>
    <script src="../vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="../vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
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