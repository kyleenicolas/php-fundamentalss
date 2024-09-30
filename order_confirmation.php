<?php
session_start();

// Redirect to index.php if there are no order details in the session
if (!isset($_SESSION['order_details'])) {
    header('Location: index.php'); 
    exit();
}

// Retrieve the order details from the session
$orderDetails = $_SESSION['order_details'];

// Clear the order details from the session
unset($_SESSION['order_details']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>
    
    <!-- Display the order details securely with nl2br to preserve line breaks -->
    <p><?php echo nl2br(htmlspecialchars($orderDetails)); ?></p>
    
    <!-- Link to go back to the home page -->
    <a href="index.php">Go Back</a>
</body>
</html>
