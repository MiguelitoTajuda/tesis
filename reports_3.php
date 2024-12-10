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

    // Start table for all barangays
    echo "<table>";
    echo "<thead>";
    echo "<tr><th>Barangay</th><th>Total Voters</th><th>Total Non-Voters</th><th>Total Population</th></tr>";
    echo "</thead>";
    echo "<tbody>";

    // Check if there are barangays
    if ($barangay_result->num_rows > 0) {
        while ($barangay_row = $barangay_result->fetch_assoc()) {
            $barangay_name = $barangay_row['barangay'];

            // Query to get population, voter count, and non-voter count for this barangay
            $sql = "
                SELECT 
                    COUNT(*) AS total_residents,
                    SUM(CASE WHEN voter_status = 'yes' THEN 1 ELSE 0 END) AS total_voters,
                    SUM(CASE WHEN voter_status = 'no' THEN 1 ELSE 0 END) AS total_non_voters
                FROM 
                    rest
                WHERE 
                    barangay = '$barangay_name'
            ";

            $result = $conn->query($sql);

            // Check if results exist for this barangay
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $barangay_name . "</td>"; // Barangay name
                    echo "<td>" . strtolower($row['total_voters']) . "</td>"; // Total voters (lowercase)
                    echo "<td>" . strtolower($row['total_non_voters']) . "</td>"; // Total non-voters (lowercase)
                    echo "<td>" . $row['total_residents'] . "</td>"; // Total population
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found</td></tr>";
            }
        }
    } else {
        echo "<tr><td colspan='4'>No barangays found.</td></tr>";
    }

    // Close the table and connection
    echo "</tbody>";
    echo "</table>";

    // Close connection
    $conn->close();
    ?>
     <h4 style="margin-top: 10px;margin-bottom: 10px;">Prepared by:</h4>
    <button class="print-btn" onclick="window.print()">Print Report</button>
</body>
</html>
