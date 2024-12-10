<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Population, Household, and Family Report</title>
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
    <center>        
        <img src="./images/logo-x.png" alt="" height="100">
    </center>
    <h1 style="margin-top: 0px;margin-bottom: 0px;">Republic of the Philippines</h1>
    <h2 style="margin-top: 0px;margin-bottom: 0px;">Province of Misamis Oriental</h2>
    <h3 style="margin-top: 0px;margin-bottom: 0px;">Municipality of Jasaan</h3>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Office of the Municipal Population</h4>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Population, Household, and Family Report</h4>
    <h5 style="margin-top: 0px;margin-bottom: 0px;">As of Month of December 2023</h5>
    <table>
        <thead>
            <tr>
                <th>Barangay</th>
                <th colspan="2">Population</th>
                <th>Households</th>
                <th>Families</th>
            </tr>
            <tr>
                <th></th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
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

        // Query to get the population data by barangay
        $sql = "
            SELECT 
                barangay,
                SUM(CASE WHEN sex = 'Male' THEN 1 ELSE 0 END) AS male_population,
                SUM(CASE WHEN sex = 'Female' THEN 1 ELSE 0 END) AS female_population,
                COUNT(DISTINCT CASE WHEN is_leader = 1 THEN id ELSE NULL END) AS households
            FROM 
                rest
            GROUP BY 
                barangay
        ";

        $result = $conn->query($sql);

        // Initialize totals
        $total_male = $total_female = $total_households = $total_families = 0;

        // Check if results exist
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['barangay'] . "</td>";
                echo "<td>" . $row['male_population'] . "</td>";
                echo "<td>" . $row['female_population'] . "</td>";
                echo "<td>" . $row['households'] . "</td>";
                echo "<td>" . 0 . "</td>"; // Update this to calculate families if applicable
                echo "</tr>";

                // Add to totals
                $total_male += $row['male_population'];
                $total_female += $row['female_population'];
                $total_households += $row['households'];
                // $total_families += $row['families']; // Add logic for family totals if needed
            }
        } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
        }

        // Close connection
        $conn->close();
        ?>
        </tbody>
    </table>
    <h4 style="margin-top: 10px;margin-bottom: 10px;">Prepared by:</h4>
    <button class="print-btn" onclick="window.print()">Print Report</button>
</body>
</html>
