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

        header {
            text-align: center;
            padding: 20px;
            font-size: 24px;
        }

        img {
            width: 200px;
            height: 300px;
            object-fit: cover;
            text-align: center;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: center; /* Centers the links horizontally */
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
        
        label {
            display: inline-block; 
            width: 100%;
            text-align:center; 
            padding: 7px; 
        }

        form {
            background: white;
            padding: 20px; 
            max-width: 300px; 
            margin: 20px auto; 
            box-shadow: 0px 0px 10px 0px #aaa;
            border-radius: 5px;
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
        <a href="search.php">Search</a>
    </nav>  
        <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            // Include your database connection
            // Database connection
            
                $db_server = "db";
                $db_user = "root";
                $db_pass = "hunter2";
                $db_name = "mysql";

                $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

            // Default SQL query to get all data
            $sql = "SELECT * FROM `WDS Customers`";

            // If the form is submitted, adjust the SQL query based on the search criteria
            if (isset($_POST['submit'])) {
                $search = mysqli_real_escape_string($conn, $_POST['search']);
                
                // Build dynamic SQL query based on user input (search in both first_name and last_name)
                $sql = "SELECT * FROM `WDS Customers` WHERE 1=1";
                
                if (!empty($search)) {
                    $sql .= " AND (first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
                }
            }
            ?>

            <!-- HTML form for the search -->
            <form method="POST" action="">
                <label for="search">Search by First or Last Name</label>
                <input type="text" name="search" id="search" value="">
                <br><br>

                <input type="submit" name="submit" value="Search">
            </form>

            <?php
                // Run the query and display results
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    // Table headers
                echo "<table border='1'>";
                echo "<tr><th>First Name</th><th>Last Name</th><th>Address</th><th>City</th><th>State</th><th>Zip</th><th>Phone 1</th><th>Email</th></tr>";
                    
                    // Output results for each row
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
                    
                    echo "</table>";
                } else {
                    echo "No users found.";
                }

                // Close the connection
                mysqli_close($conn);
                ?>
</body>
</html>