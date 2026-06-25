<?php
include("includes/db.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="hero">

    <div class="hero-slide">

        <div
        class="img overlay"
        style="background-image:url('images/hero_bg_3.jpg');">
        </div>

        <div
        class="img overlay"
        style="background-image:url('images/hero_bg_2.jpg');">
        </div>

        <div
        class="img overlay"
        style="background-image:url('images/hero_bg_1.jpg');">
        </div>

    </div>

    <div class="container">

        <div class="row justify-content-center align-items-center">

            <div class="col-lg-9 text-center">

                <h1
                class="heading"
                data-aos="fade-up">

                Find Your Perfect Rental Property

                </h1>

                <p
                class="text-white mt-4"
                data-aos="fade-up"
                data-aos-delay="100">

                Browse available rental properties,
                submit booking requests,
                and manage your rental journey online.

                </p>

                <div
                class="mt-4"
                data-aos="fade-up"
                data-aos-delay="200">

                    <a
                    href="properties.php"
                    class="btn btn-primary me-2">

                        Browse Properties

                    </a>

                    <?php
                    if(!isset($_SESSION['renter_id']))
                    {
                    ?>

                    <a
                    href="register.php"
                    class="btn btn-light">

                        Register

                    </a>

                    <?php
                    }
                    ?>

                </div>

            </div>

        </div>

    </div>

</div>


<?php
include("includes/footer.php");
?>