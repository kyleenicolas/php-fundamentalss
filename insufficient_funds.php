<?php
// Check if 'message' parameter is set, otherwise redirect to the homepage
if (!isset($_GET['message'])) {
    header('Location: index.php');
    exit();
}

// Sanitize the 'message' parameter to prevent XSS attacks
$message = htmlspecialchars($_GET['message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insufficient Funds</title>
</head>
<body>
    <h1>Insufficient Funds</h1>
    
    <!-- Display the message -->
    <p><?php echo $message; ?></p>
    
    <!-- Link to go back to the home page -->
    <a href="index.php">Go Back</a>
</body>
</html>
