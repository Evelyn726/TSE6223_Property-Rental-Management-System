<?php
session_start();
include("includes/db.php");

$message = "";

if(isset($_GET['registered']))
{
    $message = "<div class='alert alert-success'>
                    Registration Successful. Please login.
                </div>";
}

if(isset($_POST['login']))
{
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    if(empty($email) || empty($password))
    {
        $message = "<div class='alert alert-danger'>
                        Please enter your email and password.
                    </div>";
    }
    else
    {
        $query = mysqli_query($conn,
            "SELECT * FROM renter
             WHERE email='$email'");

        if(!$query)
        {
            die("Database Error : " . mysqli_error($conn));
        }

        if(mysqli_num_rows($query) == 1)
        {
            $row = mysqli_fetch_assoc($query);

            if(password_verify($password, $row['password']))
            {
                $_SESSION['renter_id'] = $row['renter_id'];
                $_SESSION['renter_name'] = $row['name'];
                $_SESSION['renter_email'] = $row['email'];

                header("Location: index.php");
                exit();
            }
            else
            {
                $message = "<div class='alert alert-danger'>
                                Incorrect password.
                            </div>";
            }
        }
        else
        {
            $message = "<div class='alert alert-danger'>
                            Email does not exist.
                        </div>";
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
                    Login
                </h1>

                <nav aria-label="breadcrumb"
                     data-aos="fade-up"
                     data-aos-delay="200">

                    <ol class="breadcrumb text-center justify-content-center">

                        <li class="breadcrumb-item">
                            <a href="index.php">Home</a>
                        </li>

                        <li class="breadcrumb-item active text-white-50">
                            Login
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

<div class="card shadow border-0 p-5">

<h3 class="text-center text-primary mb-4">
Welcome Back
</h3>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
placeholder="Enter your email"
value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
required>

</div>

<div class="mb-4">

<label>Password</label>

<input
type="password"
name="password"
class="form-control"
placeholder="Enter your password"
required>

</div>

<button
type="submit"
name="login"
class="btn btn-primary w-100">

Login

</button>

</form>

<div class="text-center mt-4">

Don't have an account?

<a href="register.php">

Register Here

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

