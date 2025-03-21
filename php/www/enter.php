<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center; 
            margin: 20px;
        }

        header {
            text-align: center;
            padding: 20px;
            font-size: 24px;
        }

        img {
            width: 200px;
            height: 300px;
            object-fit: cover;
            display: block;
            margin: auto;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center;
            padding: 10px 0;
        }

        nav a {
            color: white;
            padding: 14px 20px;
            text-decoration: none;
            text-align: center;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        form {
            background: white;
            padding: 20px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0px 0px 10px 0px #aaa;
            border-radius: 5px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: grey white;
            color: black;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .message {
            margin: 10px 0;
            font-weight: bold;
        }
    
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<header>
    <img src="cleaners.webp" alt="Dry Cleaners Logo">
    WDS Data Inc.
</header>

<!-- Navigation Bar -->
<nav>
    <a href="index.html">Home</a>
    <a href="enter.php">Enter Data</a>
    <a href="data.php">All Data</a>
    <a href="search.html">Search</a>
</nav>
<h2>Enter Customer Data</h2>

    <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Database connection

            include 'connect.php';

            $success_message = "";
            $error_message = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $first_name = mysqli_real_escape_string($connect, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($connect, $_POST['last_name']);
                $address = mysqli_real_escape_string($connect, $_POST['address']);
                $city = mysqli_real_escape_string($connect, $_POST['city']);
                $state = mysqli_real_escape_string($connect, $_POST['state']);
                $zip = mysqli_real_escape_string($connect, $_POST['zip']);
                $phone1 = mysqli_real_escape_string($connect, $_POST['phone1']);
                $email = mysqli_real_escape_string($connect, $_POST['email']);

                if (!empty($first_name) && !empty($last_name) && !empty($email)) {
                    $sql = "INSERT INTO `customers` (first_name, last_name, address, city, state, zip, phone1, email) 
                            VALUES ('$first_name', '$last_name', '$address', '$city', '$state', '$zip', '$phone1', '$email')";

                    if (mysqli_query($connect, $sql)) {
                        $success_message = "Data successfully entered!";
                    } else {
                        $error_message = "Error: " . mysqli_error($conn);
                    }
                } else {
                    $error_message = "First Name, Last Name, and Email are required.";
                }
            }
        mysqli_close($connect);
    ?>

<!-- Form -->
<form method="POST" action="">
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name" required>
    <input type="text" name="address" placeholder="Address">
    <input type="text" name="city" placeholder="City">
    <input type="text" name="state" placeholder="State">
    <input type="text" name="zip" placeholder="Zip Code">
    <input type="text" name="phone1" placeholder="Phone Number">
    <input type="email" name="email" placeholder="Email" required>
    <input type="submit" value="Submit">
</form>

</body>
</html>