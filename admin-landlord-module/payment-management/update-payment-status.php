<?php

$conn=mysqli_connect(
"localhost",
"root",
"",
"property_rental_management"
);

$payment_id=$_POST['payment_id'];
$status=$_POST['payment_status'];

mysqli_query($conn,
"

UPDATE payment

SET payment_status='$status'

WHERE payment_id='$payment_id'

");

header("Location: admin-payment-list.php");

exit();

?>