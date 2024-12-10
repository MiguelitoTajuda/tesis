<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    // Redirect to login.php if not logged in
    header("Location: login.php");
    exit();
}

// Include the database configuration file
include 'config.php';
// Function to calculate age from birthdate
function calculateAge($birthdate) {
    $birthdate = new DateTime($birthdate); // Convert the birthdate string to a DateTime object
    $today = new DateTime(); // Get the current date
    $age = $today->diff($birthdate); // Calculate the difference between today and the birthdate
    return $age->y; // Return the age in years
}
// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gather form data
    $lastname = $_POST['lastname'];
    $suffix = $_POST['suffix'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $birthdate = $_POST['birthdate'];
    $birthplace = $_POST['birthplace'];
    $sex = $_POST['sex'];
    $civilstatus = $_POST['civilstatus'];
    $religion = $_POST['religion'];
    $voter_status = $_POST['voter_status'];
    $municipality = $_POST['municipality'];
    $barangay = $_POST['barangay'];
    $zone = $_POST['zone'];
    $employment = $_POST['employment'];
    $contactnum = $_POST['contactnum'];
    $emailadd = $_POST['emailadd'];
    $highesteduc = $_POST['highesteduc'];
    $age = calculateAge($birthdate);
    $is_leader = isset($_POST['is_leader']) ? 1 : 0;
    $household_leader_id = $_POST['household_leader'] ?? NULL; // If not leader, link to a household leader

    // Set household_leader_id to NULL if the resident is the leader
    if ($is_leader) {
        $household_leader_id = NULL;
    }

    // SQL query to insert the resident
    $sql = "INSERT INTO rest (lastname, suffix, firstname, middlename, birthdate, birthplace, sex, civilstatus, religion, voter_status, municipality, barangay, zone, employment, contactnum, emailadd, age, highesteduc, is_leader, household_leader_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssssssssssssiis', $lastname, $suffix, $firstname, $middlename, $birthdate, $birthplace, $sex, $civilstatus, $religion, $voter_status, $municipality, $barangay, $zone, $employment, $contactnum, $emailadd, $highesteduc, $age, $is_leader, $household_leader_id);

    if ($stmt->execute()) {
        echo "<script>
        alert('Resident added successfully!');
        window.location.href = 'residents.php';  // Redirect to residents.php
      </script>";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
// Fetch all household leaders for the dropdown list
$leaders_sql = "SELECT id, CONCAT(firstname, ' ', lastname) AS leader_name FROM rest WHERE household_leader_id IS NULL";
$leaders_result = $conn->query($leaders_sql);

// Role-based barangay restrictions
$user_role = $_SESSION['user_role'];
$barangay_options = [
    'Admin Lower Jasaan' => ['Lower Jasaan'],
    'Admin Upper Jasaan' => ['Upper Jasaan'],
    'Admin Bobontugan' => ['Bobontugan'],
    'Admin Jampason' => ['Jampason'],
    'Admin San Antonio' => ['San Antonio'],
    'Admin Danao' => ['Danao'],
    'Admin I.S Cruz' => ['I.S Cruz'],
    'Admin San Nicolas' => ['San Nicolas'],
    'Admin Corrales' => ['Corrales'],
    'Admin San Isidro' => ['San Isidro'],
    'Admin Aplaya' => ['Aplaya'],
    'Admin Solana' => ['Solana'],
    'Admin Luz Banson' => ['Luz Banson'],
    'Admin Kimaya' => ['Kimaya'],
    'Admin Natubo' => ['Natubo'],
    'Super Admin' => [
        'Lower Jasaan', 'Upper Jasaan', 'Bobontugan', 'Jampason', 'San Antonio',
        'Danao', 'I.S Cruz', 'San Nicolas', 'Corrales', 'San Isidro',
        'Aplaya', 'Solana', 'Luz Banson', 'Kimaya', 'Natubo'
    ],
];

// Get the barangays available for the current user role
$available_barangays = $barangay_options[$user_role] ?? [];
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .logo {
    position: absolute;
    top: 5px;
    right: 20px;
    height: 100px; /* Larger logo */
    width: auto;   /* Maintain aspect ratio */
    object-fit: contain;
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
            width: 100%;
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
        #leader_selection {
            display: none;
            margin-top: 20px;
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

<script>
    // Function to toggle the display of the household leader selection
    function toggleLeaderSelection() {
        var isLeader = document.getElementById("is_leader").checked;
        var leaderSelection = document.getElementById("leader_selection");
        var householdLeaderSelect = document.getElementById("household_leader");

        // Show/hide leader selection based on whether the checkbox is checked
        if (isLeader) {
            leaderSelection.style.display = "none"; // Hide if checked
            householdLeaderSelect.required = false; // Not required if a leader
        } else {
            leaderSelection.style.display = "block"; // Show if not checked
            householdLeaderSelect.required = true; // Required if not a leader
        }
    }

    // Validate the form when it's submitted
    function validateForm() {
        var isLeader = document.getElementById("is_leader").checked;
        var householdLeaderSelect = document.getElementById("household_leader");

        if (!isLeader && householdLeaderSelect.value === "") {
            alert("Please select a household leader.");
            return false;
        }
        return true;
    }

    // Initialize Select2 on the household_leader dropdown
    $(document).ready(function() {
        // Initialize Select2 on the #household_leader select
        $('#household_leader').select2({
            placeholder: "-- Select Leader --", // Placeholder text
            width: '100%' // Ensure dropdown takes full width of its container
        });

        // Call toggleLeaderSelection to properly initialize the form based on checkbox state
        toggleLeaderSelection();

        // Listen for changes in the checkbox state to update the visibility of the leader selection dynamically
        $('#is_leader').change(function() {
            toggleLeaderSelection();
        });
    });
</script>
</head>
<body>

<a href="residents.php" class="close-btn">
            <i class="fas fa-times"></i> <!-- Close icon -->
        </a>

<div class="container">
<div class="row">
        <div class="col-12 text-right">
            <img src="images/official.png" alt="Logo" class="logo" style="height: 80px;">
        </div>
    </div>
        <h3 class="text-center my-4">Resident Registration Form</h3>
    
        <form method="POST" action="" onsubmit="return validateForm()">
            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="suffix">Suffix</label>
                    <input type="text" class="form-control" name="suffix" id="suffix">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="middlename">Middle Name</label>
                    <input type="text" class="form-control" name="middlename" id="middlename">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="birthdate">Date of Birth</label>
                    <input type="date" class="form-control" name="birthdate" id="birthdate" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="birthplace">Place of Birth</label>
                    <input type="text" class="form-control" name="birthplace" id="birthplace" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label>Sex</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="sex" value="Male" id="sexMale" required>
                        <label class="form-check-label" for="sexMale">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="sex" value="Female" id="sexFemale">
                        <label class="form-check-label" for="sexFemale">Female</label>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="civilstatus">Civil Status</label>
                    <select class="form-control" name="civilstatus" id="civilstatus">
                        <option value="Single" selected>Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Separated">Separated</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="religion">Religion</label>
                    <input type="text" class="form-control" name="religion" id="religion">
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="municipality">Municipality</label>
                    <input type="text" class="form-control" name="municipality" id="municipality" required>
                </div>
                <div class="col-md-4 mb-3">
    <label for="barangay">Barangay</label>
    <select name="barangay" id="barangay" class="form-control" required>
        <option value="">-- Select Barangay --</option> 
        <?php
        // Check if the user role is 'Super Admin'
        if ($_SESSION['user_role'] === 'Super Admin') {
            // Show all barangays for Super Admin
            $all_barangays = [
                "Lower Jasaan", "Upper Jasaan", "Bobontugan", "Jampason", "San Antonio",
                "Danao", "I.S Cruz", "San Nicolas", "Corrales", "San Isidro",
                "Aplaya", "Solana", "Luz Banson", "Kimaya", "Natubo"
            ];
            foreach ($all_barangays as $barangay) {
                echo "<option value='" . htmlspecialchars($barangay) . "'>" . htmlspecialchars($barangay) . "</option>";
            }
        } else {
            // Show restricted barangays based on the role
            if (!empty($available_barangays)) {
                foreach ($available_barangays as $barangay) {
                    echo "<option value='" . htmlspecialchars($barangay) . "'>" . htmlspecialchars($barangay) . "</option>";
                }
            } else {
                echo "<option value='' disabled>No barangays available</option>";
            }
        }
        ?>
    </select>
</div>

                <div class="col-md-4 mb-3">
                    <label for="zone">Zone/Purok/Street No.</label>
                    <input type="text" class="form-control" name="zone" id="zone" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="employment">Employment</label>
                    <input type="text" class="form-control" name="employment" id="employment" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="contactnum">Contact Number</label>
                    <input type="text" class="form-control" name="contactnum" id="contactnum" required>
                </div>

                <div class="col-md-4 mb-3">
                <label for="voter_status">Voter Status</label>
                    <select class="form-control" name="voter_status" id="voter_status">
                        <option value="yes" selected>Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="emailadd">Email Address</label>
                    <input type="email" class="form-control" name="emailadd" id="emailadd" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="highesteduc">Highest Educational Attainment</label>
                    <input type="text" class="form-control" name="highesteduc" id="highesteduc">
                </div>

                <div class="col-md-6 mb-3">
    <label>
        <input type="checkbox" name="is_leader" id="is_leader" onclick="toggleLeaderSelection()">
        <span>Is Household Head?</span>
    </label>
</div>

<!-- Leader selection dropdown (hidden by default) -->
<div id="leader_selection" class="form-row" style="display: none;">
    <div class="col-md-12">
        <label for="household_leader">Select Household Leader</label>
        <select name="household_leader" id="household_leader" class="form-control">
            <option value="">-- Select Leader --</option>
            <?php while ($leader = $leaders_result->fetch_assoc()): ?>
                <option value="<?php echo $leader['id']; ?>"><?php echo $leader['leader_name']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>
</div>

            <button type="submit" class="btn btn-primary mt-3">Register Resident</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        

    </script>
</body>
</html>
