<?php
include("includes/db.php");

$message = "";

if(isset($_POST['register']))
{
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if(empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirmPassword))
    {
        $message = "<div class='alert alert-danger'>Please fill in all fields.</div>";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $message = "<div class='alert alert-danger'>Invalid email address.</div>";
    }
    elseif(!preg_match('/^[0-9]{10,15}$/', $phone))
    {
        $message = "<div class='alert alert-danger'>Invalid phone number.</div>";
    }
    elseif(strlen($password) < 8)
    {
        $message = "<div class='alert alert-danger'>Password must be at least 8 characters.</div>";
    }
    elseif($password !== $confirmPassword)
    {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
    else
    {
        $checkEmail = mysqli_query($conn,
            "SELECT * FROM renter WHERE email='$email'");

        if(!$checkEmail)
        {
            die("Database Error : ".mysqli_error($conn));
        }

        if(mysqli_num_rows($checkEmail) > 0)
        {
            $message = "<div class='alert alert-danger'>Email already exists.</div>";
        }
        else
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insert = mysqli_query($conn,
            "INSERT INTO renter(name,email,phone_number,password)
             VALUES('$name','$email','$phone','$hashedPassword')");

            if($insert)
            {
                header("Location: login.php?registered=1");
                exit();
            }
            else
            {
                $message = "<div class='alert alert-danger'>Registration Failed.</div>";
            }
        }
    }
}

include("includes/header.php");
include("includes/navbar.php");
?>
<div class="hero page-inner overlay"
     style="background-image: url('images/hero_bg_3.jpg')">

    <div class="container">

        <div class="row justify-content-center align-items-center">

            <div class="col-lg-9 text-center mt-5">

                <h1 class="heading" data-aos="fade-up">
                    Register
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
                            Register
                        </li>

                    </ol>

                </nav>

            </div>

        </div>

    </div>

</div>
<div class="section">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-6">

                <div class="card shadow p-4">

                    <h3 class="text-center text-primary mb-4">
                        Create Your Account
                    </h3>

                    <?php echo $message; ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Full Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                placeholder="Enter your full name"
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                placeholder="Enter your email"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                placeholder="e.g. 0123456789"
                                maxlength="15"
                                value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Minimum 8 characters"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                placeholder="Re-enter your password"
                                required>

                        </div>

                        <button
                            type="submit"
                            name="register"
                            class="btn btn-primary w-100">

                            Register

                        </button>

                    </form>

                    <div class="text-center mt-4">

                        Already have an account?

                        <a href="login.php">

                            Login Here

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<?php
include("includes/footer.php");
?>