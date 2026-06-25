<?php
if(session_status() == PHP_SESSION_NONE)
{
    session_start();
}
?>

<nav class="site-nav">
    <div class="container">
        <div class="menu-bg-wrap">
            <div class="site-navigation">

                <a href="index.php" class="logo m-0 float-start">
                    Property Rental
                </a>

                <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu float-end">

                    <li><a href="index.php">Home</a></li>

                    <li><a href="properties.php">Properties</a></li>

                    <?php
                    if(isset($_SESSION['renter_id']))
                    {
                    ?>

                        <li><a href="rental.php">Rental</a></li>

                        <li><a href="payment.php">Payment</a></li>

                        <li><a href="maintenance.php">Maintenance</a></li>

                        <li><a href="profile.php">Profile</a></li>

                        <li><a href="logout.php">Logout</a></li>

                    <?php
                    }
                    else
                    {
                    ?>

                        <li><a href="login.php">Login</a></li>

                        <li><a href="register.php">Register</a></li>

                    <?php
                    }
                    ?>

                </ul>

                <a href="#"
                   class="burger light me-auto float-end mt-1 site-menu-toggle js-menu-toggle d-inline-block d-lg-none">
                    <span></span>
                </a>

            </div>
        </div>
    </div>
</nav>