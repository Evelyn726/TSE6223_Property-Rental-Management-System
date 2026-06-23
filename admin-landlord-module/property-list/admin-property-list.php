<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

$error = "";
$success = "";

$propertiesPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) { $page = 1; }
$offset = ($page - 1) * $propertiesPerPage;

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* Delete Property */
if (isset($_POST['delete_property'])) {
    $property_id = (int)$_POST['delete_property'];

    $check_query = "SELECT COUNT(*) AS total FROM booking WHERE property_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "i", $property_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $booking_count = mysqli_fetch_assoc($check_result)['total'];

    if ($booking_count > 0) {
        $error = "This property cannot be deleted because it has booking records. Please set it to Unavailable instead.";
    } else {
        $delete_query = "DELETE FROM property WHERE property_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $property_id);

        if (mysqli_stmt_execute($delete_stmt)) {
            $success = "Property deleted successfully.";
        } else {
            $error = "Error deleting property: " . mysqli_error($conn);
        }
    }
}

/* Update Property Status Only */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['availability_status']) && isset($_POST['property_id'])) {
    $availability_status = $_POST['availability_status'];
    $property_id = (int)$_POST['property_id'];

    if (in_array($availability_status, ['Available', 'Occupied', 'Unavailable'])) {
        $update_query = "UPDATE property SET availability_status = ? WHERE property_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "si", $availability_status, $property_id);

        if (mysqli_stmt_execute($update_stmt)) {
            $success = "Property status updated successfully.";
        } else {
            $error = "Error updating property status: " . mysqli_error($conn);
        }
    }
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'all';

$where = "WHERE 1=1";

if (in_array($status_filter, ['Available', 'Occupied', 'Unavailable'])) {
    $where .= " AND property.availability_status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}

if ($date_range !== 'all') {
    $days = (int)$date_range;
    if ($days == 0) {
        $where .= " AND DATE(property.created_at) = CURDATE()";
    } else {
        $where .= " AND property.created_at >= DATE_SUB(NOW(), INTERVAL $days DAY)";
    }
}

$query = "SELECT 
            property.property_id,
            property.property_name,
            property.location,
            property.property_type,
            property.rental_price,
            property.description,
            property.availability_status,
            property.created_at,
            landlord.name AS landlord_name,
            landlord.email AS landlord_email
          FROM property
          INNER JOIN landlord ON property.landlord_id = landlord.landlord_id
          $where
          ORDER BY property.created_at DESC
          LIMIT $offset, $propertiesPerPage";

$result = mysqli_query($conn, $query);

$count_query = "SELECT COUNT(*) AS total 
                FROM property 
                INNER JOIN landlord ON property.landlord_id = landlord.landlord_id 
                $where";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$totalProperties = $count_row['total'];
$totalPages = ceil($totalProperties / $propertiesPerPage);
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
    <title>Properties</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/icon/admin-property-icon.png">

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
                            <span class="hamburger-box"><span class="hamburger-inner"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

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
                            <h3 class="title-5 m-b-35">Properties</h3>

                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger" role="alert"><?= h($error) ?><button type="button" class="close" data-dismiss="alert"><span>&times;</span></button></div>
                            <?php } ?>

                            <?php if (!empty($success)) { ?>
                                <div class="alert alert-success" role="alert"><?= h($success) ?><button type="button" class="close" onclick="window.location.href='admin-property-list.php'"><span>&times;</span></button></div>
                            <?php } ?>

                            <div class="table-data__tool">
                                <div class="table-data__tool-left">
                                    <div class="rs-select2--light rs-select2--md">
                                        <select class="js-select2" name="status" onchange="applyFilters()">
                                            <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>All Properties</option>
                                            <option value="Available" <?= $status_filter == 'Available' ? 'selected' : '' ?>>Available</option>
                                            <option value="Occupied" <?= $status_filter == 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                                            <option value="Unavailable" <?= $status_filter == 'Unavailable' ? 'selected' : '' ?>>Unavailable</option>
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
                                    <a href="../add-property/admin-property-add.php">
                                        <button class="au-btn au-btn-icon au-btn--green au-btn--small"><i class="zmdi zmdi-plus"></i>add property</button>
                                    </a>
                                    <button class="au-btn au-btn-icon au-btn--small" style="background-color: gray;">
                                            <i class="zmdi zmdi-plus"></i> Export to PDF</button>
                                </div>
                            </div>

                            <div class="table-responsive table-responsive-data2">
                                <table class="table table-data2">
                                    <thead>
                                        <tr>
                                            <th>property name</th>
                                            <th>landlord</th>
                                            <th>date</th>
                                            <th>price (RM)</th>
                                            <th>description</th>
                                            <th>status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr class="tr-shadow">';
                                            echo '<td>' . h($row['property_name']) . '<br><small>' . h($row['location']) . ' | ' . h($row['property_type']) . '</small></td>';
                                            echo '<td><span class="block-email">' . h($row['landlord_name']) . '<br>' . h($row['landlord_email']) . '</span></td>';
                                            echo '<td>' . date("Y-m-d H:i", strtotime($row['created_at'])) . '</td>';
                                            echo '<td>' . number_format($row['rental_price'], 2) . '</td>';
                                            echo '<td class="desc">' . h($row['description']) . '</td>';
                                            echo '<td>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="property_id" value="' . h($row['property_id']) . '">
                                                        <select class="status-select" name="availability_status" onchange="this.form.submit()">
                                                            <option value="Available" ' . ($row['availability_status'] == 'Available' ? 'selected' : '') . '>Available</option>
                                                            <option value="Occupied" ' . ($row['availability_status'] == 'Occupied' ? 'selected' : '') . '>Occupied</option>
                                                            <option value="Unavailable" ' . ($row['availability_status'] == 'Unavailable' ? 'selected' : '') . '>Unavailable</option>
                                                        </select>
                                                    </form>
                                                  </td>';
                                            echo '<td><div class="table-data-feature">';
                                            echo '<a href="../edit-property/admin-property-edit.php?property_id=' . h($row['property_id']) . '" class="item" data-toggle="tooltip" data-placement="top" title="Edit"><i class="zmdi zmdi-edit"></i></a>';
                                            echo '<form action="" method="post" style="display:inline;"><input type="hidden" name="delete_property" value="' . h($row['property_id']) . '"><button class="item" type="submit" data-toggle="tooltip" data-placement="top" title="Delete" onclick="return confirmDelete()"><i class="zmdi zmdi-delete"></i></button></form>';
                                            echo '</div></td>';
                                            echo '</tr><tr class="spacer"></tr>';
                                        }
                                    } else {
                                        echo '<tr class="tr-shadow"><td colspan="7" class="text-center">No property records found.</td></tr>';
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright"><p>Property Rental Management System (2026).</p></div>
                            </div>
                        </div>
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
        function confirmDelete() {
            return confirm("Are you sure you want to delete this property?");
        }
        function applyFilters() {
            var status = document.getElementsByName('status')[0].value;
            var dateRange = document.getElementsByName('date_range')[0].value;
            window.location.href = "admin-property-list.php?status=" + status + "&date_range=" + dateRange;
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
