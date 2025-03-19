<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <header>
        <img src="cleaners.webp" alt="Dry cleaners logo" style="display: block; margin: auto;">
        WDS Data Inc.
    </header>
    <title>Navigation Bar</title>
    <style>
        /* Basic styling for the navigation bar */
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        img {
            width: 200px;
            height: 300px;
            object-fit: cover;
            text-align: center;
        }

        header {
            text-align: center;
            padding: 20px;
            font-size: 24px;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center; 
            padding: 10px 0;
        }

        nav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        table {
            margin: 20px auto; 
            border-collapse: collapse;
            width: 80%; 
            text-align: center; 
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Responsive styling for smaller screens */
        @media screen and (max-width: 600px) {
            nav a {
                float: none;
                width: 100%;
                text-align: left;
            }
        }
    </style>
</head>
<body>
   <nav>
        <a href="index.html">Home</a>
        <a href="enter.php">Enter Data</a>
        <a href="data.php">All Data</a>
        <a href="search.html">Search</a>
    </nav>
        <?php

            include 'connect.php';

            // Correct SQL syntax (assuming "data_info_user" is a table)
            $sql = "SELECT * FROM `data_info_user`";
            $result = mysqli_query($connect, $sql);

            if (mysqli_num_rows($result) > 0) {
                    // Start table and add headers
                    echo "<table border='1'>";
                    echo "<tr>";
                    echo "<th>First Name</th>";
                    echo "<th>Last Name</th>";
                    echo "<th>Address</th>";
                    echo "<th>City</th>";
                    echo "<th>State</th>";
                    echo "<th>Zip</th>";
                    echo "<th>Phone 1</th>";
                    echo "<th>Email</th>";
                    echo "</tr>";
                    
                    // Loop through each row and output data in one row per entry
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["address"] . "</td>";
                    echo "<td>" . $row["city"] . "</td>";
                    echo "<td>" . $row["state"] . "</td>";
                    echo "<td>" . $row["zip"] . "</td>";
                    echo "<td>" . $row["phone1"] . "</td>";
                    echo "<td>" . $row["email"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "No user found";
            }
            // Close connection
            mysqli_close($connect);
        ?>
</body>
</html>