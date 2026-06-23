<?php
session_start();

if (!isset($_SESSION['landlord_id'])) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "property_rental_management");

$error = "";
$success = "";

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$landlord_result = mysqli_query($conn, "SELECT landlord_id, name FROM landlord ORDER BY name ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_property'])) {
    $landlord_id = (int)$_POST['landlord_id'];
    $property_name = trim($_POST['property_name']);
    $location = trim($_POST['location']);
    $property_type = trim($_POST['property_type']);
    $rental_price = $_POST['rental_price'];
    $description = trim($_POST['description']);
    $availability_status = $_POST['availability_status'];

    $insert_query = "INSERT INTO property (landlord_id, property_name, location, property_type, rental_price, description, availability_status)
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "isssdss", $landlord_id, $property_name, $location, $property_type, $rental_price, $description, $availability_status);

    if (mysqli_stmt_execute($insert_stmt)) {
        $success = "Property added successfully.";
    } else {
        $error = "Error adding property: " . mysqli_error($conn);
    }
}
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
    <title>Add Property</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../images/icon/admin-add-property-icon.jpg">

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
                        <div class="row"><div class="col-lg-8">
                            <h3 class="title-5 m-b-35">Add Property</h3>

                            <?php if (!empty($error)) { ?><div class="alert alert-danger"><?= h($error) ?></div><?php } ?>
                            <?php if (!empty($success)) { ?><div class="alert alert-success"><?= h($success) ?> <a href="../property-list/admin-property-list.php">Back to property list</a></div><?php } ?>

                            <div class="card">
                                <div class="card-header">Property Details</div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <input type="hidden" name="add_property" value="1">

                                        <div class="form-group">
                                            <label>Landlord</label>
                                            <select name="landlord_id" class="form-control" required>
                                                <option value="">Select Landlord</option>
                                                <?php while ($landlord = mysqli_fetch_assoc($landlord_result)) { ?>
                                                    <option value="<?= h($landlord['landlord_id']) ?>"><?= h($landlord['name']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Property Name</label>
                                            <input type="text" name="property_name" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Location</label>
                                            <input type="text" name="location" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Property Type</label>
                                            <input type="text" name="property_type" class="form-control">
                                        </div>

                                        <div class="form-group">
                                            <label>Rental Price (RM)</label>
                                            <input type="number" step="0.01" name="rental_price" class="form-control" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" class="form-control" rows="4"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Availability Status</label>
                                            <select name="availability_status" class="form-control">
                                                <option value="Available">Available</option>
                                                <option value="Occupied">Occupied</option>
                                                <option value="Unavailable">Unavailable</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="au-btn au-btn--blue au-btn--small">Save Property</button>
                                        <a href="../property-list/admin-property-list.php" class="au-btn au-btn--grey au-btn--small">Cancel</a>
                                    </form>
                                </div>
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
    
</body>
</html>
<!-- end document-->
