<?php
include("includes/db.php");
include("includes/header.php");
include("includes/navbar.php");

if(!isset($_GET['id']))
{
    header("Location: properties.php");
    exit();
}

$property_id = (int)$_GET['id'];

$query = mysqli_query($conn,
"SELECT property.*,
       landlord.name AS landlord_name,
       landlord.email,
       landlord.phone_number
 FROM property
 INNER JOIN landlord
 ON property.landlord_id = landlord.landlord_id
 WHERE property.property_id = $property_id");

if(!$query)
{
    die("Database Error: " . mysqli_error($conn));
}

if(mysqli_num_rows($query) == 0)
{
    header("Location: properties.php");
    exit();
}

$row = mysqli_fetch_assoc($query);
?>

<div
  class="hero page-inner overlay"
  style="background-image: url('images/hero_bg_3.jpg')"
>
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-9 text-center mt-5">
        <h1 class="heading" data-aos="fade-up">
          <?php echo htmlspecialchars($row['property_name']); ?>
        </h1>

        <nav
          aria-label="breadcrumb"
          data-aos="fade-up"
          data-aos-delay="200"
        >
          <ol class="breadcrumb text-center justify-content-center">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item">
              <a href="properties.php">Properties</a>
            </li>
            <li
              class="breadcrumb-item active text-white-50"
              aria-current="page"
            >
              <?php echo htmlspecialchars($row['property_name']); ?>
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<div class="section">
  <div class="container">
    <div class="row justify-content-between">
      <div class="col-lg-7">
        <div class="img-property-slide-wrap">
          <div class="img-property-slide">
            <img src="images/img_1.jpg" alt="Image" class="img-fluid" />
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <h2 class="heading text-primary"><?php echo htmlspecialchars($row['property_name']); ?></h2>
        <p class="meta"><?php echo htmlspecialchars($row['location']); ?></p>
        <p>
        <?php
        if(!empty($row['description']))
        {
            echo nl2br(htmlspecialchars($row['description']));
        }
        else
        {
            echo "No description available.";
        }
        ?>
        </p>


        <div class="mt-4">
            <p>
                <strong>Property Type:</strong>
                <?php echo htmlspecialchars($row['property_type']); ?>
            </p>
            <p>
                <strong>Rental Price:</strong>
                RM <?php echo number_format($row['rental_price'],2); ?>
            </p>
            <p>
                <strong>Status:</strong>
                <?php echo htmlspecialchars($row['availability_status']); ?>
            </p>
        </div>

        <div class="d-block agent-box p-5">
            <div class="text">
                <h3 class="mb-3">
                    Landlord
                </h3>
                <p>
                    <strong>Name:</strong>
                    <?php echo htmlspecialchars($row['landlord_name']); ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?php echo htmlspecialchars($row['email']); ?>
                </p>

                <p>
                    <strong>Phone:</strong>
                    <?php echo htmlspecialchars($row['phone_number']); ?>
                </p>
                <a
                href="booking.php?id=<?php echo $row['property_id']; ?>"
                class="btn btn-primary">
                Book Now
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