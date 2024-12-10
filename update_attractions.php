<?php
include 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $id = htmlspecialchars(trim($_POST['ID']));
    $name = htmlspecialchars(trim($_POST['Name']));
    $barangay = htmlspecialchars(trim($_POST['barangay']));
    $type = htmlspecialchars(trim($_POST['Type']));
    $description = htmlspecialchars(trim($_POST['Description']));

    // Update the attraction
    $sql = "UPDATE attractions SET Name=?, barangay=?, Type=?, Description=? WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $barangay, $type, $description, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();
    $conn->close();
}
?>
