<?php
session_start();

// Database connection details
$host = 'localhost';
$db = 'orders';
$user = 'root';
$pass = '';

try {
    // Establish a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $order = htmlspecialchars(trim($_POST['order']));
    $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
    $cash = filter_var($_POST['cash'], FILTER_VALIDATE_FLOAT);

    // Validate input
    if ($quantity === false || $cash === false || $quantity <= 0 || $cash <= 0) {
        echo "Invalid input.";
        exit();
    }

    // Fetch the price for the selected order
    $stmt = $pdo->prepare("SELECT price FROM menu WHERE item_name = :item_name");
    $stmt->execute(['item_name' => $order]);
    $price = $stmt->fetchColumn();

    if ($price === false) {
        echo "Item not found.";
        exit();
    }

    // Calculate total
    $total = $price * $quantity;

    // Check if cash is sufficient
    if ($cash < $total) {
        $message = "balance is not enough.";
        header("Location: insufficient_funds.php?message=" . urlencode($message));
        exit();
    } else {
        // Calculate change
        $change = $cash - $total;

        // Log order details in session
        $timestamp = date("Y-m-d H:i:s");
        $orderDetails = "Order: $order\nQuantity: $quantity\nTotal: $total\nCash: $cash\nChange: $change\nTimestamp: $timestamp";
        $_SESSION['order_details'] = $orderDetails;

        // Redirect to order confirmation
        header('Location: order_confirmation.php');
        exit();
    }
}

// Fetch menu items from the database
$stmt = $pdo->query("SELECT item_name, price FROM menu");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>
<body>
    <h1>Menu</h1>
    <table border="1">
        <tr>
            <th>Order</th>
            <th>Price</th>
        </tr>
        <?php foreach ($menuItems as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                <td><?php echo htmlspecialchars($item['price']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <form method="post">
        <label for="order">Select an order:</label>
        <select name="order" id="order" required>
            <?php foreach ($menuItems as $item): ?>
                <option value="<?php echo htmlspecialchars($item['item_name']); ?>">
                    <?php echo htmlspecialchars($item['item_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required>
        <br><br>

        <label for="cash">Cash:</label>
        <input type="text" name="cash" id="cash" required>
        <br><br>

        <button type="submit">Submit</button>
    </form>

    <?php if (isset($_SESSION['order_details'])): ?>
        <h2>Order Details</h2>
        <p><?php echo nl2br(htmlspecialchars($_SESSION['order_details'])); ?></p>
    <?php endif; ?>
</body>
</html>
