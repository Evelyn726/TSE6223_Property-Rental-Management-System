<?php

$conn=mysqli_connect(
"localhost",
"root",
"",
"property_rental_management"
);

$maintenance_id=$_POST['maintenance_id'];
$status=$_POST['status'];

mysqli_query($conn,
"

UPDATE maintenance

SET status='$status'

WHERE maintenance_id='$maintenance_id'

");

header("Location: admin-maintenance-list.php");

exit();

?>