<?php

$conn=mysqli_connect(
"localhost",
"root",
"",
"property_rental_management"
);

$rental_id=$_POST['rental_id'];
$status=$_POST['rental_status'];

// update rental status
mysqli_query($conn,
"
UPDATE rental

SET rental_status='$status'

WHERE rental_id='$rental_id'

");

header("Location: admin-rental-list.php");

exit();

?>