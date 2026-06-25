<?php
include("includes/db.php");
include("includes/header.php");
include("includes/navbar.php");
?>

<div class="hero page-inner overlay"
  style="background-image: url('images/hero_bg_1.jpg')"
>
  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-lg-9 text-center mt-5">
        <h1 class="heading" data-aos="fade-up">Properties</h1>

        <nav
          aria-label="breadcrumb"
          data-aos="fade-up"
          data-aos-delay="200"
        >
          <ol class="breadcrumb text-center justify-content-center">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li
              class="breadcrumb-item active text-white-50"
              aria-current="page"
            >
              Properties
            </li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col text-center">
            <h2 class="text-primary">Available Properties</h2>
            <p>Browse all available rental properties.</p>
        </div>
    </div>
</div>

<div class="container mb-5">

<div class="row justify-content-center">

<div class="col-lg-10 mx-auto">

<form method="GET">

<div class="row">

<div class="col-md mb-3">

<input
type="text"
name="property_name"
class="form-control"
placeholder="Property Name"
value="<?php echo isset($_GET['property_name']) ? htmlspecialchars($_GET['property_name']) : ''; ?>">

</div>

<div class="col-md mb-3">

<input
type="text"
name="location"
class="form-control"
placeholder="Location"
value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

</div>

<div class="col-md mb-3">

<select
name="property_type"
class="form-control">

<option value="">All Types</option>

<option value="Apartment"
<?php if(isset($_GET['property_type']) && $_GET['property_type']=="Apartment") echo "selected"; ?>>
Apartment
</option>

<option value="Condominium"
<?php if(isset($_GET['property_type']) && $_GET['property_type']=="Condominium") echo "selected"; ?>>
Condominium
</option>

<option value="Terrace"
<?php if(isset($_GET['property_type']) && $_GET['property_type']=="Terrace") echo "selected"; ?>>
Terrace
</option>

<option value="House"
<?php if(isset($_GET['property_type']) && $_GET['property_type']=="House") echo "selected"; ?>>
House
</option>

</select>

</div>

<div class="col-md mb-3">

<input
type="number"
name="min_price"
class="form-control"
placeholder="Min Price"
value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">

</div>

<div class="col-md mb-3">

<input
type="number"
name="max_price"
class="form-control"
placeholder="Max Price"
value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">

</div>

</div>

<div class="row mt-3 justify-content-center">

    <div class="col-md-2">

        <button
        type="submit"
        class="btn btn-primary w-100">

            Search

        </button>

    </div>

    <div class="col-md-2">

        <a
        href="properties.php"
        class="btn btn-secondary w-100">

            Reset

        </a>

    </div>

</div>

</div>

</form>

</div>

</div>

</div>

</div>

<div class="section section-properties">
    <div class="container">

        <div class="row">

        <?php
        $sql = "SELECT *
                FROM property";

        if(!empty($_GET['property_name']))
        {
            $name = mysqli_real_escape_string($conn,$_GET['property_name']);
            $sql .= " AND property_name LIKE '%$name%'";
        }

        if(!empty($_GET['location']))
        {
            $location = mysqli_real_escape_string($conn,$_GET['location']);
            $sql .= " AND location LIKE '%$location%'";
        }

        if(!empty($_GET['property_type']))
        {
            $type = mysqli_real_escape_string($conn,$_GET['property_type']);
            $sql .= " AND property_type='$type'";
        }

        if(!empty($_GET['min_price']))
        {
            $min_price = (float)$_GET['min_price'];
            $sql .= " AND rental_price >= '$min_price'";
        }

        if(!empty($_GET['max_price']))
        {
            $price = (float)$_GET['max_price'];
            $sql .= " AND rental_price <= '$price'";
        }

        $sql .= " ORDER BY created_at DESC";

        $result = mysqli_query($conn,$sql);

        if(!$result)
        {
            die("Database Error: " . mysqli_error($conn));
        }

        if(mysqli_num_rows($result) > 0)
        {
            ?>
            
            <p class="text-muted mb-4">
                <?php echo mysqli_num_rows($result); ?> property(s) found.
            </p>

            <?php
            while($row = mysqli_fetch_assoc($result))
            {
        ?>

            <div class="col-md-6 col-lg-4 mb-4">

                <div class="property-item">

                    <a href="property-single.php?id=<?php echo $row['property_id']; ?>" class="img">

                        <img src="images/img_1.jpg"
                            class="img-fluid"
                            alt="Property">

                    </a>

                    <div class="property-content">

                        <div class="price mb-2">

                            <span>

                                RM <?php echo number_format($row['rental_price'],2); ?>

                            </span>

                        </div>

                        <div>

                            <h4 class="mb-2">
                                <?php echo htmlspecialchars($row['property_name']); ?>
                            </h4>

                            <span class="city d-block mb-3">

                                <?php echo htmlspecialchars($row['location']); ?>

                            </span>

                            <span class="badge bg-primary mb-3">

                                <?php echo htmlspecialchars($row['property_type']); ?>

                            </span>

                            <p class="mb-2">
                                <strong>Status:</strong>
                                <?php echo htmlspecialchars($row['availability_status']); ?>
                            </p>

                            <p>
                            <?php
                            if(!empty($row['description']))
                            {
                                if(strlen($row['description']) > 80)
                                {
                                    echo htmlspecialchars(substr($row['description'],0,80)) . "...";
                                }
                                else
                                {
                                    echo htmlspecialchars($row['description']);
                                }
                            }
                            else
                            {
                                echo "No description available.";
                            }
                            ?>
                            </p>

                            <a
                            href="property-single.php?id=<?php echo $row['property_id']; ?>"
                            class="btn btn-primary py-2 px-3">

                            View Details

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php

            }
        }
        else
        {
            echo "<div class='col-12'>
                    <div class='alert alert-warning'>
                        No properties match your search criteria.
                    </div>
                  </div>";
        }

        ?>

        </div>

    </div>
</div>

<?php
include("includes/footer.php");
?>