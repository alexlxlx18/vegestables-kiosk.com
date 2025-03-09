<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "checkout_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prices per kg
$prices = [
    "Apple" => 100, 
    "Strawberry" => 450, 
    "Broccoli" => 200, 
    "Onions" => 300, 
    "Tomato" => 200, 
    "Talong" => 150
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    
    $total_price = 0;
    $order_summary = "";

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO orders (name, email, address, product, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($_POST['product'] as $index => $product) {
        $quantity = (int) $_POST['quantity'][$index];

        if ($quantity > 0 && isset($prices[$product])) {
            $subtotal = $prices[$product] * $quantity;
            $total_price += $subtotal;
            $order_summary .= "<li class='list-group-item'><strong>$quantity kg of $product</strong> - Php $subtotal</li>";

            // Insert into the database
            $stmt->bind_param("ssssid", $name, $email, $address, $product, $quantity, $subtotal);
            $stmt->execute();
        }
    }
    
    $stmt->close();

    if ($total_price > 0) {
        $_SESSION['purchased'] = true;
        $_SESSION['order_summary'] = $order_summary;
        $_SESSION['total_price'] = $total_price;
        $_SESSION['name'] = $name;
    }
}

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit;
}

// Order Again handler
if (isset($_GET['orderAgain'])) {
    unset($_SESSION['purchased']);
    header("Location: checkout.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Order Form</h2>

        <?php if (isset($_SESSION['purchased'])): ?>
            <div class="card shadow p-4">
                <h3 class="text-success">Order Confirmed!</h3>
                <p>Thank you, <strong><?= $_SESSION['name'] ?></strong>. Your order has been placed.</p>
                <h4>Order Summary:</h4>
                <ul class="list-group mb-3">
                    <?= $_SESSION['order_summary'] ?>
                </ul>
                <h4>Total Amount: <strong>Php <?= $_SESSION['total_price'] ?></strong></h4>
                <div class="mt-3">
                    <a href="?orderAgain=true" class="btn btn-warning">Order Again</a>
                    <a href="index.php" class="btn btn-primary">Go Back Home</a>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <form action="" method="POST" class="card p-4 shadow">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address:</label>
                    <input type="text" name="address" class="form-control" required>
                </div>

                <h4 class="mt-4">Choose Products:</h4>
                <div class="row">
                    <?php foreach ($prices as $product => $price): ?>
                        <div class="col-md-4 d-flex align-items-center mb-3">
                            <img src="<?= strtolower($product) ?>.png" alt="<?= $product ?>" width="50" height="50" class="me-3 rounded">
                            <div>
                                <div class="form-check">
                                    <input type="checkbox" name="product[]" value="<?= $product ?>" class="form-check-input">
                                    <label class="form-check-label"><?= $product ?> (Php <?= $price ?>/kg)</label>
                                </div>
                                <input type="number" name="quantity[]" min="1" class="form-control mt-2" placeholder="kg">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary mt-4">Order Now</button>
                <form action="" method="POST" class="card p-4 shadow">
    <!-- Other form fields remain the same -->

    <a href="index.php" class="btn btn-secondary mt-4">Go Back Home</a>
</form>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>
