<?php
// Include the config.php file for database connection
require_once('config.php');

// Retrieve the id from the URL
$household_head_id = isset($_GET['id']) ? $_GET['id'] : null;

// Initialize variables
$household_head_name = null;
$result = null;

if ($household_head_id) {
    // Query to fetch the household head's full name
    $sql_head = "
        SELECT firstname, middlename, lastname 
        FROM rest 
        WHERE id = ?";
    
    $stmt_head = $conn->prepare($sql_head);
    $stmt_head->bind_param("i", $household_head_id);
    $stmt_head->execute();
    $result_head = $stmt_head->get_result();

    if ($result_head && $result_head->num_rows > 0) {
        $head_row = $result_head->fetch_assoc();
        $household_head_name = $head_row['firstname'] . ' ' . $head_row['middlename'] . ' ' . $head_row['lastname'];
    }

// Query to fetch members of the household where household_leader_id matches the household_head_id
$sql = "
    SELECT 
        firstname, 
        middlename, 
        lastname, 
        birthdate, 
        CONCAT(municipality, ', ', barangay, ', ', zone) AS address
    FROM 
        rest 
    WHERE 
        household_leader_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $household_head_id);
$stmt->execute();
$result = $stmt->get_result();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Household Members</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .content {
            padding: 20px;
            background-color: white;
            margin: 30px auto;
            border-radius: 8px;
            max-width: 1000px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-left: 50px;
        }

        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            position: relative;
        }

        h1 {
            color: #333;
            font-size: 24px;
        }

        h2 {
            color: #555;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .close-btn {
            font-size: 24px;
            color: #ff0000;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px;
            margin: 0 auto;
        }

        table, th, td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th, td {
            padding: 12px 15px;
        }

        th {
            background-color: #0056b3; 
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e9f7e9;
        }

        td {
            color: #555;
        }

        .no-members {
            text-align: center;
            color: #888;
            font-size: 16px;
        }

        /* Style the close button (X icon) */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 30px; /* Larger size for the icon */
            color: #333;
            text-decoration: none;
        }

        .close-btn i {
            font-size: 40px; /* Make the icon bigger */
        }

        .close-btn:hover {
            color: #f44336; /* Red color on hover */
        }
    </style>

</head>
<body>

<a href="household.php" class="close-btn">
            <i class="fas fa-times"></i> <!-- Close icon -->
        </a>

    
    <div class="content">
        <h1>Household Members</h1>
        <?php if ($household_head_name): ?>
            <h2>Household Head: <?= htmlspecialchars($household_head_name) ?></h2>
        <?php endif; ?>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $full_name = $row["firstname"] . " " . $row["middlename"] . " " . $row["lastname"];
                        $age = date_diff(date_create($row["birthdate"]), date_create('today'))->y;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($full_name) ?></td>
                            <td><?= htmlspecialchars($age) ?></td>
                            <td><?= htmlspecialchars($row["address"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No members found for this household.</p>
        <?php endif; ?>
    </div>
    
</body>
</html>
