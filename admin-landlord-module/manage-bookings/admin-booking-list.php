<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../admin-landlord-module/admin-login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

$error = "";
$success = "";
$bookingsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $bookingsPerPage;

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'all';

$where = "WHERE 1=1";

if (in_array($status_filter, ['Pending', 'Approved', 'Rejected'])) {
    $where .= " AND booking.booking_status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

if ($date_range !== 'all') {
    $days = (int)$date_range;
    if ($days == 0) {
        $where .= " AND DATE(booking.created_at) = CURDATE()";
    } else {
        $where .= " AND booking.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)";
    }
}

$query = "SELECT booking.booking_id, booking.booking_date, booking.booking_status, booking.created_at,
                 renter.name AS renter_name, renter.email AS renter_email,
                 property.property_name, property.location, property.property_type, property.rental_price
          FROM booking
          INNER JOIN renter ON booking.renter_id = renter.renter_id
          INNER JOIN property ON booking.property_id = property.property_id
          $where
          ORDER BY booking.created_at DESC
          LIMIT $offset, $bookingsPerPage";

$result = mysqli_query($conn, $query);

$count_query = "SELECT COUNT(*) AS total
                FROM booking
                INNER JOIN renter ON booking.renter_id = renter.renter_id
                INNER JOIN property ON booking.property_id = property.property_id
                $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$totalBookings = $count_row['total'];
$totalPages = ceil($totalBookings / $bookingsPerPage);
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
    <title>Manage Bookings</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/icon/admin-booking-icon.png">

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
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row"><div class="col-md-12">
                            <h3 class="title-5 m-b-35">Booking Requests</h3>

                            <div class="table-data__tool">
                                <div class="table-data__tool-left">
                                    <div class="rs-select2--light rs-select2--md">
                                        <select class="js-select2" name="status" onchange="applyFilters()">
                                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Bookings</option>
                                            <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Approved" <?= $status_filter == 'Approved' ? 'selected' : '' ?>>Approved</option>
                                            <option value="Rejected" <?= $status_filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                    <div class="rs-select2--light rs-select2--sm">
                                        <select class="js-select2" name="date_range" onchange="applyFilters()">
                                            <option value="all" <?= $date_range == 'all' ? 'selected' : '' ?>>All Day</option>
                                            <option value="0" <?= $date_range == '0' ? 'selected' : '' ?>>Today</option>
                                            <option value="3" <?= $date_range == '3' ? 'selected' : '' ?>>3 Days</option>
                                            <option value="7" <?= $date_range == '7' ? 'selected' : '' ?>>1 Week</option>
                                            <option value="90" <?= $date_range == '90' ? 'selected' : '' ?>>3 Month</option>
                                        </select>
                                        <div class="dropDownSelect2"></div>
                                    </div>
                                </div>
                                <div class="table-data__tool-right">
                                    <!-- <button class="au-btn au-btn-icon au-btn--small" style="background-color: gray;">
                                            <i class="zmdi zmdi-plus"></i> Export to PDF</button> -->
                                </div>
                            </div>

                            <div class="table-responsive table-responsive-data2">
                                <table class="table table-data2">
                                    <thead>
                                        <tr>
                                            <th>booking id</th>
                                            <th>renter</th>
                                            <th>property</th>
                                            <th>booking date</th>
                                            <th>price (RM)</th>
                                            <th>status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $statusClass = strtolower($row['booking_status']);
                                            echo '<tr class="tr-shadow">';
                                            echo '<td>#' . h($row['booking_id']) . '</td>';
                                            echo '<td><span class="block-email">' . h($row['renter_name']) . '<br>' . h($row['renter_email']) . '</span></td>';
                                            echo '<td>' . h($row['property_name']) . '<br><small>' . h($row['location']) . ' | ' . h($row['property_type']) . '</small></td>';
                                            echo '<td>' . date("Y-m-d", strtotime($row['booking_date'])) . '</td>';
                                            echo '<td>' . number_format($row['rental_price'], 2) . '</td>';
                                            echo '<td><span class="status--' . h($statusClass) . '">' . h($row['booking_status']) . '</span></td>';
                                            echo '<td><div class="table-data-feature">';
                                            echo '<a href="../booking-approval-page/admin-booking-approval.php?booking_id=' . h($row['booking_id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Review"><i class="zmdi zmdi-eye"></i></a>';
                                            if ($row['booking_status'] == 'Pending') {
                                                echo '<a href="../booking-approval-page/admin-booking-approval.php?booking_id=' . h($row['booking_id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Approve / Reject"><i class="zmdi zmdi-check-circle"></i></a>';
                                            }
                                            echo '</div></td>';
                                            echo '</tr><tr class="spacer"></tr>';
                                        }
                                    } else {
                                        echo '<tr class="tr-shadow"><td colspan="7" class="text-center">No booking records found.</td></tr>';
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="pagination">
                                <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>&status=<?= h($status_filter) ?>&date_range=<?= h($date_range) ?>" class="prev">&laquo;</a><?php endif; ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?><a href="?page=<?= $i ?>&status=<?= h($status_filter) ?>&date_range=<?= h($date_range) ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a><?php endfor; ?>
                                <?php if ($page < $totalPages): ?><a href="?page=<?= $page + 1 ?>&status=<?= h($status_filter) ?>&date_range=<?= h($date_range) ?>" class="next">&raquo;</a><?php endif; ?>
                            </div>
                        </div></div>
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
        function applyFilters() {
            var status = document.getElementsByName('status')[0].value;
            var dateRange = document.getElementsByName('date_range')[0].value;
            window.location.href = "admin-booking-list.php?status=" + status + "&date_range=" + dateRange;
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
<!-- end document-->
