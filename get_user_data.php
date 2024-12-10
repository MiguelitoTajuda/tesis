<?php
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "jk";
    $conn = new mysqli($servername, $username, $password, $dbname);
    require_once('config.php');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, name, email, role FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'User not found']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'No ID provided']);
}
?>
