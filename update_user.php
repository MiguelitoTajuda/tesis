<?php
include 'config.php';

// Check if ID parameter is set and not empty
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the resident data for editing
    $sql = "SELECT * FROM user WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if form is submitted to update the data
        if (isset($_POST['submit'])) {
            if ($_POST['submit'] == "Update") {
                // Fetch updated data from the form
                $name = $_POST['name'];  // Form field for name
                $role = $_POST['role'];  // Form field for role
                $email = $_POST['email'];  // Form field for email

                // Update the user data in the database
                $update_sql = "UPDATE user SET name=?, role=?, email=? WHERE id=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sssi", $name, $role, $email, $id);

                if ($update_stmt->execute()) {
                    // Redirect to users.php after successful update
                    header("Location: users.php");
                    exit();
                } else {
                    echo "<p style='color: red;'>Error updating user: " . $conn->error . "</p>";
                }
            } elseif ($_POST['submit'] == "Back") {
                // Redirect to users.php if "Back" button is clicked
                header("Location: users.php");
                exit();
            }
        }
        ?>

        <!-- Update Form -->
        <form action="update_user.php?id=<?php echo $id; ?>" method="POST">
            <h2>Edit User</h2>
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>
            <div>
                <label for="role">Role:</label>
                <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($row['role']); ?>" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
            </div>
            <div>
                <button type="submit" name="submit" value="Update">Update</button>
                <button type="submit" name="submit" value="Back">Back</button>
            </div>
        </form>

        <?php
    } else {
        echo "<p style='color: red;'>User not found.</p>";
    }
} else {
    echo "<p style='color: red;'>No user ID provided.</p>";
}
?>
