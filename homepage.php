<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
    <div style="text-align:center; padding:15%;">
      <p  style="font-size:50px; font-weight:bold;">
       Hello  
       <?php 
       session_start();
       if(isset($_SESSION['email'])){
        include("connect.php");
        $email = $_SESSION['email'];
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        while($row = mysqli_fetch_array($query)){
            echo $row['firstName'] . ' ' . $row['lastName'];
        }
       }
       ?>
       :)
      </p>
      <a href="index.php"><button style="padding:10px 20px; background:red; color:white; border:none; cursor:pointer;">Proceed to Shop</button></a>
      <br><br>
      <a href="logout.php">
        <button style="padding:10px 20px; background:red; color:white; border:none; cursor:pointer;">Logout</button>
      </a>
    </div>
</body>
</html>
