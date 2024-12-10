<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Population Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        h1, h2, h3, h4, h5 {
            text-align: center;
        }
        .print-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .print-btn:hover {
            background-color: #45a049;
        }
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <center>        
        <img src="./images/logo-x.png" alt="" height="100">
    </center>
    <h1 style="margin-top: 0px;margin-bottom: 0px;">Republic of the Philippines</h1>
    <h2 style="margin-top: 0px;margin-bottom: 0px;">Province of Misamis Oriental</h2>
    <h3 style="margin-top: 0px;margin-bottom: 0px;">Municipality of Jasaan</h3>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Office of the Municipal Population</h4>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Population Report</h4>
    <h5 style="margin-top: 0px;margin-bottom: 0px;">As of Month of December 2023</h5>
    
    <?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "jk"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to get the distinct barangays
    $barangay_sql = "SELECT DISTINCT barangay FROM rest";
    $barangay_result = $conn->query($barangay_sql);

    // Check if there are barangays
    if ($barangay_result->num_rows > 0) {
        // Loop through each barangay
        while ($barangay_row = $barangay_result->fetch_assoc()) {
            $barangay_name = $barangay_row['barangay'];

            // Query to get all residents' details for this barangay
            $resident_sql = "
                SELECT 
                    lastname,
                    firstname,
                    middlename
                FROM 
                    rest
                WHERE 
                    barangay = '$barangay_name'
            ";

            $resident_result = $conn->query($resident_sql);

            // Check if results exist for this barangay
            if ($resident_result->num_rows > 0) {
                echo "<h3>Barangay: " . $barangay_name . "</h3>";
                echo "<table>";
                echo "<thead>";
                echo "<tr><th>LASTNAME</th><th>FIRSTNAME</th><th>MIDDLENAME</th></tr>";
                echo "</thead>";
                echo "<tbody>";
                
                // Loop through residents and display their names
                while ($row = $resident_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['lastname'] . "</td>"; // Last name
                    echo "<td>" . $row['firstname'] . "</td>"; // First name
                    echo "<td>" . $row['middlename'] . "</td>"; // Middle name
                    echo "</tr>";
                }

                echo "</tbody>";
                echo "</table>";
            } else {
                echo "<h3>Barangay: " . $barangay_name . "</h3>";
                echo "<p>No residents found in this barangay.</p>";
            }
        }
    } else {
        echo "<p>No barangays found.</p>";
    }

    // Close connection
    $conn->close();
    ?>
    
    <h4 style="margin-top: 10px;margin-bottom: 10px;">Prepared by:</h4>
    <button class="print-btn" onclick="window.print()">Print Report</button>
</body>
</html>
