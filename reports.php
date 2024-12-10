<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Population and Age Bracket Report</title>
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
        .tittle {
            display: none;
        }
    }
    </style>

    
</head>
<body>
    <center>        
    <img src="./images/logo-x.png" alt="" height="100">
    </center>
    <h1  style="margin-top: 0px;margin-bottom: 0px;">Republic of the Philippines</h1>
    <h2 style="margin-top: 0px;margin-bottom: 0px;">Province of Misamis Oriental</h2>
    <h3 style="margin-top: 0px;margin-bottom: 0px;">Municipality of Jasaan</h3>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Office of the Municipal Population</h4>
    <h4 style="margin-top: 0px;margin-bottom: 0px;">Population Distribution by Barangay</h4>
    <h5 style="margin-top: 0px;margin-bottom: 0px;">Age Bracket December 2023</h5>

    <table>
        <thead>
            <tr>
                <th>Barangay</th>
                <th colspan="3">Under 1</th>
                <th colspan="3">1-4</th>
                <th colspan="3">5-9</th>
                <th colspan="3">10-14</th>
                <th colspan="3">15-19</th>
                <th colspan="3">20-30</th>
                <th colspan="3">31-49</th>
                <th colspan="3">50-60</th>
                <th colspan="3">60+</th>
            </tr>
            <tr>
                <th></th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
                <th>Total</th>
                <th>M</th>
                <th>F</th>
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

            // Query to calculate population distribution by barangay and age brackets
            $sql = "
SELECT 
    barangay,
    SUM(CASE WHEN sex = 'Male' AND age < 1 THEN 1 ELSE 0 END) AS under1_male,
    SUM(CASE WHEN sex = 'Female' AND age < 1 THEN 1 ELSE 0 END) AS under1_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 1 AND 4 THEN 1 ELSE 0 END) AS group1_4_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 1 AND 4 THEN 1 ELSE 0 END) AS group1_4_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 5 AND 9 THEN 1 ELSE 0 END) AS group5_9_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 5 AND 9 THEN 1 ELSE 0 END) AS group5_9_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 10 AND 14 THEN 1 ELSE 0 END) AS group10_14_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 10 AND 14 THEN 1 ELSE 0 END) AS group10_14_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 15 AND 19 THEN 1 ELSE 0 END) AS group15_19_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 15 AND 19 THEN 1 ELSE 0 END) AS group15_19_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 20 AND 30 THEN 1 ELSE 0 END) AS group20_30_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 20 AND 30 THEN 1 ELSE 0 END) AS group20_30_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 31 AND 49 THEN 1 ELSE 0 END) AS group31_49_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 31 AND 49 THEN 1 ELSE 0 END) AS group31_49_female,
    
    SUM(CASE WHEN sex = 'Male' AND age BETWEEN 50 AND 60 THEN 1 ELSE 0 END) AS group50_60_male,
    SUM(CASE WHEN sex = 'Female' AND age BETWEEN 50 AND 60 THEN 1 ELSE 0 END) AS group50_60_female,
    
    SUM(CASE WHEN sex = 'Male' AND age > 60 THEN 1 ELSE 0 END) AS group60plus_male,
    SUM(CASE WHEN sex = 'Female' AND age > 60 THEN 1 ELSE 0 END) AS group60plus_female
FROM 
    rest
GROUP BY 
    barangay
";

$result = $conn->query($sql);

// Initialize totals including new age brackets
$total_under1_male = $total_under1_female = $total_group1_4_male = $total_group1_4_female = 0;
$total_group5_9_male = $total_group5_9_female = $total_group10_14_male = $total_group10_14_female = 0;
$total_group15_19_male = $total_group15_19_female = 0;
$total_group20_30_male = $total_group20_30_female = 0;
$total_group31_49_male = $total_group31_49_female = 0;
$total_group50_60_male = $total_group50_60_female = 0;
$total_group60plus_male = $total_group60plus_female = 0;

// Check if results exist
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Accumulate totals including new age brackets
        $total_under1_male += $row['under1_male'];
        $total_under1_female += $row['under1_female'];
        $total_group1_4_male += $row['group1_4_male'];
        $total_group1_4_female += $row['group1_4_female'];
        $total_group5_9_male += $row['group5_9_male'];
        $total_group5_9_female += $row['group5_9_female'];
        $total_group10_14_male += $row['group10_14_male'];
        $total_group10_14_female += $row['group10_14_female'];
        $total_group15_19_male += $row['group15_19_male'];
        $total_group15_19_female += $row['group15_19_female'];
        $total_group20_30_male += $row['group20_30_male'];
        $total_group20_30_female += $row['group20_30_female'];
        $total_group31_49_male += $row['group31_49_male'];
        $total_group31_49_female += $row['group31_49_female'];
        $total_group50_60_male += $row['group50_60_male'];
        $total_group50_60_female += $row['group50_60_female'];
        $total_group60plus_male += $row['group60plus_male'];
        $total_group60plus_female += $row['group60plus_female'];

        echo "<tr>";
        echo "<td>" . $row['barangay'] . "</td>";
        // Under 1
        echo "<td>" . $row['under1_male'] . "</td>";
        echo "<td>" . $row['under1_female'] . "</td>";
        echo "<td>" . ($row['under1_male'] + $row['under1_female']) . "</td>";

        // 1-4
        echo "<td>" . $row['group1_4_male'] . "</td>";
        echo "<td>" . $row['group1_4_female'] . "</td>";
        echo "<td>" . ($row['group1_4_male'] + $row['group1_4_female']) . "</td>";

        // 5-9
        echo "<td>" . $row['group5_9_male'] . "</td>";
        echo "<td>" . $row['group5_9_female'] . "</td>";
        echo "<td>" . ($row['group5_9_male'] + $row['group5_9_female']) . "</td>";

        // 10-14
        echo "<td>" . $row['group10_14_male'] . "</td>";
        echo "<td>" . $row['group10_14_female'] . "</td>";
        echo "<td>" . ($row['group10_14_male'] + $row['group10_14_female']) . "</td>";

        // 15-19
        echo "<td>" . $row['group15_19_male'] . "</td>";
        echo "<td>" . $row['group15_19_female'] . "</td>";
        echo "<td>" . ($row['group15_19_male'] + $row['group15_19_female']) . "</td>";

        // 20-30
        echo "<td>" . $row['group20_30_male'] . "</td>";
        echo "<td>" . $row['group20_30_female'] . "</td>";
        echo "<td>" . ($row['group20_30_male'] + $row['group20_30_female']) . "</td>";

        // 31-49
        echo "<td>" . $row['group31_49_male'] . "</td>";
        echo "<td>" . $row['group31_49_female'] . "</td>";
        echo "<td>" . ($row['group31_49_male'] + $row['group31_49_female']) . "</td>";

        // 50-60
        echo "<td>" . $row['group50_60_male'] . "</td>";
        echo "<td>" . $row['group50_60_female'] . "</td>";
        echo "<td>" . ($row['group50_60_male'] + $row['group50_60_female']) . "</td>";

        // 60+
        echo "<td>" . $row['group60plus_male'] . "</td>";
        echo "<td>" . $row['group60plus_female'] . "</td>";
        echo "<td>" . ($row['group60plus_male'] + $row['group60plus_female']) . "</td>";

        echo "</tr>";
    }
}
$conn->close();
?>
</tbody>
<tfoot>
    <tr>
        <th>Total</th>
        <th><?php echo $total_under1_male; ?></th>
        <th><?php echo $total_under1_female; ?></th>
        <th><?php echo $total_under1_male + $total_under1_female; ?></th>
        <th><?php echo $total_group1_4_male; ?></th>
        <th><?php echo $total_group1_4_female; ?></th>
        <th><?php echo $total_group1_4_male + $total_group1_4_female; ?></th>
        <th><?php echo $total_group5_9_male; ?></th>
        <th><?php echo $total_group5_9_female; ?></th>
        <th><?php echo $total_group5_9_male + $total_group5_9_female; ?></th>
        <th><?php echo $total_group10_14_male; ?></th>
        <th><?php echo $total_group10_14_female; ?></th>
        <th><?php echo $total_group10_14_male + $total_group10_14_female; ?></th>
        <th><?php echo $total_group15_19_male; ?></th>
        <th><?php echo $total_group15_19_female; ?></th>
        <th><?php echo $total_group15_19_male + $total_group15_19_female; ?></th>
        <th><?php echo $total_group20_30_male; ?></th>
        <th><?php echo $total_group20_30_female; ?></th>
        <th><?php echo $total_group20_30_male + $total_group20_30_female; ?></th>
        <th><?php echo $total_group31_49_male; ?></th>
        <th><?php echo $total_group31_49_female; ?></th>
        <th><?php echo $total_group31_49_male + $total_group31_49_female; ?></th>
        <th><?php echo $total_group50_60_male; ?></th>
        <th><?php echo $total_group50_60_female; ?></th>
        <th><?php echo $total_group50_60_male + $total_group50_60_female; ?></th>
        <th><?php echo $total_group60plus_male; ?></th>
        <th><?php echo $total_group60plus_female; ?></th>
        <th><?php echo $total_group60plus_male + $total_group60plus_female; ?></th>
    </tr>
</tfoot>
</table>
<h4 style="margin-top: 10px;margin-bottom: 10px;">Prepared by:</h4>
<button class="print-btn" onclick="window.print()">Print Report</button>
</body>
</html>