<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "checkout_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $product = htmlspecialchars($_POST['product']);
    $quantity = (int) $_POST['quantity'];

    // Product prices
    $prices = [
        "Apple" => 200, 
        "Strawberry" => 450, 
        "Onion" => 200, 
        "Orange" => 250, 
        "Broccoli" => 15, 
        "Talong" => 150
    ];

    // Calculate total price
    if ($quantity > 0 && isset($prices[$product])) {
        $total_price = $prices[$product] * $quantity;

        // Insert order into the database
        $stmt = $conn->prepare("INSERT INTO orders (name, email, address, product, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisd", $name, $email, $address, $product, $quantity, $total_price);
        $stmt->execute();
        $stmt->close();

        // Display confirmation
        echo "<div class='container mt-5 p-4 border rounded shadow'>";
        echo "<h3 class='text-success'>Order Confirmed!</h3>";
        echo "<p>Thank you, <strong>$name</strong>. Your order has been placed.</p>";
        echo "<h4>Order Summary:</h4>";
        echo "<p><strong>$quantity kg of $product</strong> - $$total_price</p>";
        echo "<h4>Total Amount: <strong>$$total_price</strong></h4>";
        echo "<a href='checkout.php' class='btn btn-primary'>Back to Shop</a>";
        echo "</div>";
    } else {
        echo "<p class='text-danger'>Error: Invalid quantity or product.</p>";
    }
}

$conn->close();
?>
