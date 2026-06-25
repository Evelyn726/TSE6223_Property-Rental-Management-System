<?php
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "property_rental_management"
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
?>