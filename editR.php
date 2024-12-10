<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<title>Edit Activity</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: gainsboro;
    }
        .container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h3 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #333;
        }
        input[type="text"], input[type="date"], input[type="email"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 10%;
        }
        button:disabled {
            background-color: #ddd;
        }
        .checkbox-group {
            margin-top: 20px;
        }
        .checkbox-group label {
            display: inline-block;
            margin-right: 10px;
        }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-danger {
        background-color: #f44336;
        border-color: #f44336;
    }

    .btn:hover {
        opacity: 0.8;
    }
   

</style>
</head>
<body>
<div class="container">
<div class="row align-items-center mb-4">
        <div class="col-md-12 d-flex justify-content-between">
            <h3 class="m-0 text-center flex-grow-1">Resident Registration Form</h3>
            <img src="images/official.png" alt="Logo" class="logo" style="height: 80px;">
        </div>
    </div>

    <?php
    include 'config.php';

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM rest WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (isset($_POST['submit'])) {
                if ($_POST['submit'] == "Update") {
                    $lastname = $_POST['lastname'];
                    $suffix = $_POST['suffix'];
                    $firstname = $_POST['firstname'];
                    $middlename = $_POST['middlename'];
                    $birthdate = $_POST['birthdate'];
                    $birthplace = $_POST['birthplace'];
                    $sex = $_POST['sex'];
                    $civilstatus = $_POST['civilstatus'];
                    $religion = $_POST['religion'];
                    $municipality = $_POST['municipality'];
                    $barangay = $_POST['barangay'];
                    $zone = $_POST['zone'];
                    $employment = $_POST['employment'];
                    $contactnum = $_POST['contactnum'];
                    $emailadd = $_POST['emailadd'];
                    $highesteduc = $_POST['highesteduc'];
                    $is_leader = isset($_POST['is_leader']) ? 1 : 0;

                    $update_sql = "UPDATE rest SET lastname=?, suffix=?, firstname=?, middlename=?, birthdate=?, birthplace=?, sex=?, civilstatus=?, religion=?, municipality=?, barangay=?, zone=?, employment=?, contactnum=?, emailadd=?, highesteduc=?, is_leader=? WHERE id=?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("ssssssssssssssssii", $lastname, $suffix, $firstname, $middlename, $birthdate, $birthplace, $sex, $civilstatus, $religion, $municipality, $barangay, $zone, $employment, $contactnum, $emailadd, $highesteduc, $is_leader, $id);

                    if ($update_stmt->execute()) {
                        header("Location: residents.php");
                        exit();
                    } else {
                        echo "<p style='color: red;'>Error updating resident: " . $conn->error . "</p>";
                    }
                } elseif ($_POST['submit'] == "Back") {
                    header("Location: residents.php");
                    exit();
                }
            }
            ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <label for="lastname">Last Name</label>
                        <input type="text" class="form-control" name="lastname" value="<?= $row['lastname'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="suffix">Suffix</label>
                        <input type="text" class="form-control" name="suffix" value="<?= $row['suffix'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="firstname">First Name</label>
                        <input type="text" class="form-control" name="firstname" value="<?= $row['firstname'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="middlename">Middle Name</label>
                        <input type="text" class="form-control" name="middlename" value="<?= $row['middlename'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="birthdate">Date of Birth</label>
                        <input type="date" class="form-control" name="birthdate" value="<?= $row['birthdate'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="birthplace">Place of Birth</label>
                        <input type="text" class="form-control" name="birthplace" value="<?= $row['birthplace'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Sex</label><br>
                        <input type="radio" name="sex" value="Male" <?= $row['sex'] == 'Male' ? 'checked' : '' ?>> Male
                        <input type="radio" name="sex" value="Female" <?= $row['sex'] == 'Female' ? 'checked' : '' ?>> Female
                    </div>
                    <div class="col-md-6">
                        <label for="civilstatus">Civil Status</label>
                        <select class="form-control" name="civilstatus">
                            <option value="Single" <?= $row['civilstatus'] == 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= $row['civilstatus'] == 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Widowed" <?= $row['civilstatus'] == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                            <option value="Divorced" <?= $row['civilstatus'] == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="religion">Religion</label>
                        <input type="text" class="form-control" name="religion" value="<?= $row['religion'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="municipality">Municipality</label>
                        <input type="text" class="form-control" name="municipality" value="<?= $row['municipality'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="barangay">Barangay</label>
                        <select class="form-control" name="barangay">
                            <option value="Lower Jasaan" <?= $row['barangay'] == 'Lower Jasaan' ? 'selected' : '' ?>>Lower Jasaan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="zone">Zone</label>
                        <input type="text" class="form-control" name="zone" value="<?= $row['zone'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="employment">Employment</label>
                        <input type="text" class="form-control" name="employment" value="<?= $row['employment'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="contactnum">Contact Number</label>
                        <input type="text" class="form-control" name="contactnum" value="<?= $row['contactnum'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="emailadd">Email Address</label>
                        <input type="email" class="form-control" name="emailadd" value="<?= $row['emailadd'] ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="highesteduc">Highest Education</label>
                        <input type="text" class="form-control" name="highesteduc" value="<?= $row['highesteduc'] ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <label>
                            <input type="checkbox" name="is_leader" <?= $row['is_leader'] == 1 ? 'checked' : '' ?>> Is Household Head?
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" name="submit" value="Update" class="btn btn-primary">Update</button>
                    <button type="submit" name="submit" value="Back" class="btn btn-danger">Back</button>
                </div>
            </form>

            <?php
        } else {
            echo "<p class='text-danger'>Resident not found.</p>";
        }
    } else {
        echo "<p class='text-danger'>Invalid request.</p>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
