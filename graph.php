<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>Statistic</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
   
body {
            background-color: #eeeeee;
            font-family: 'Poppins', sans-serif;
        }

        h1 {
font-size: 2.5rem; /* Larger font size for prominence */
font-weight: bold; /* Bold text for emphasis */
color: #343a40; /* Dark gray color for the text */
margin-bottom: 20px; /* Space below the heading */
text-align: center; /* Center the heading */
text-transform: uppercase; /* Uppercase letters for a formal look */
letter-spacing: 2px; /* Slightly increased letter spacing */
border-bottom: 2px solid #007bff; /* Blue border under the heading */
padding-bottom: 10px; /* Space between the text and the border */
}

        .container {

background-color: #f8f9fa; /* Light background color */
border: 1px solid #dee2e6; /* Light gray border */
border-radius: 0.3rem; /* Rounded corners */
padding: 20px; /* Padding inside the container */
box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
margin-top: 5px; /* Space above the container */
}

.nav-wrapper {
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

/* Optional: Add space between the elements if needed */
nav a, nav label, #date-time-container {
    margin-left: 10px;
    margin-bottom: 150px;
}   

/* Optional: Add space between the elements if needed */
nav a, nav label, #date-time-container {
    margin-left: 10px;
    margin-bottom: 150px;
}   
/* Default sidebar styles */
.sidebar {
    width: 250px;
    transition: width 0.3s;
    overflow: hidden;
    background-color: #343a40;
    color: #ffffff;
    height: 100vh;
}

.sidebar .logo .logo-name {
    font-size: 1.5rem;
    margin-left: 10px;
    
}

.side-menu li {
    padding: 10px;
    display: flex;
    align-items: center;
}

.side-menu li a {
    text-decoration: none;
    color: #ffffff;
    display: flex;
    align-items: center;
}

.side-menu li a .menu-text {
    margin-left: 10px;
    white-space: nowrap;
    transition: opacity 0.3s;
    
}

.sidebar.minimized {
    width: 50px;
}

.sidebar.minimized .logo-name {
    display: none;
}

.sidebar.minimized .menu-text {
    opacity: 0;
    pointer-events: none; /* Prevent text selection when hidden */
}

.sidebar.minimized li {
    justify-content: center;
}


</style>

<body>

<div class="sidebar" id="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <i class='bx bx-menu' id="toggle-sidebar" style="margin-left: 15px;"></i>

        <ul class="side-menu">
    <li class="">
        <a href="jskn/index.html" onclick="redirectTo('jskn/index.html')" style="background-color: #343a40; color: white;">
            <i class='bx bx-home'></i>Home
        </a>
    </li>

    <li class="">
        <a href="complaint.php" onclick="redirectTo('complaint.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-comment-detail'></i>Complaints
        </a>
    </li>
    <li class="active">
        <a href="graph.php" onclick="redirectTo('graph.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-bar-chart-alt-2'></i>Statistics
        </a>
    </li>
    <li class="">
        <a href="events.php" onclick="redirectTo('events.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-calendar-event'></i>Events
        </a>
    </li>
    <li class="">
        <a href="gala.php" onclick="redirectTo('gala.php')" style="background-color: #343a40; color: white;">
            <i class='bx bx-star'></i>Gala
        </a>
    </li>
</ul>

    </div>
    <div class="content">
        <div class="container mt-4">
            <h1>Statistics</h1>
    <div class="container mt-4">
        <h2>Resident Data Distribution by Barangay</h2>
        
        <h4>Age Distribution</h4>
        <canvas id="ageDistributionChart" width="400" height="200"></canvas>
        <div id="ageDataDescription" class="mt-2"></div>

        <h4>Voter Status Distribution</h4>
        <canvas id="voterStatusChart" width="400" height="200"></canvas>
        <div id="voterDataDescription" class="mt-2"></div>

        <h4>Employment Status Distribution</h4>
        <canvas id="employmentStatusChart" width="400" height="200"></canvas>
        <div id="employmentDataDescription" class="mt-2"></div>

        <h4>Total Population Distribution</h4>
        <canvas id="populationChart" width="400" height="200"></canvas>
        <div id="populationDataDescription" class="mt-2"></div>
    </div>

    <?php
    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'jk');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query for age distribution
    $age_sql = "SELECT barangay, 
                       CASE 
                           WHEN age < 20 THEN '0-19'
                           WHEN age BETWEEN 20 AND 39 THEN '20-39'
                           WHEN age BETWEEN 40 AND 59 THEN '40-59'
                           ELSE '60+' 
                       END AS age_range,
                       COUNT(*) AS count,
                       COUNT(*) OVER (PARTITION BY barangay) AS total_residents
                FROM rest
                GROUP BY barangay, age_range
                ORDER BY barangay, age_range";

    $age_result = $conn->query($age_sql);

    $data = [];
    while ($row = $age_result->fetch_assoc()) {
        $data[$row['barangay']]['age_ranges'][$row['age_range']] = $row['count'];
        $data[$row['barangay']]['total_residents'] = $row['total_residents'];
    }

    // Prepare data for age chart
    $barangays = array_keys($data);
    $age_ranges = ['0-19', '20-39', '40-59', '60+'];
    $age_data = [];    
    $total_residents = [];

    foreach ($age_ranges as $range) {
        $age_data[$range] = array_map(function($barangay) use ($data, $range) {
            return $data[$barangay]['age_ranges'][$range] ?? 0;
        }, $barangays);
    }

    foreach ($barangays as $barangay) {
        $total_residents[] = $data[$barangay]['total_residents'];
    }

    // SQL query for voter status distribution
    $voter_sql = "SELECT barangay, voter_status, COUNT(*) AS count
                  FROM rest
                  WHERE voter_status IN ('yes', 'no')
                  GROUP BY barangay, voter_status";

    $voter_result = $conn->query($voter_sql);
    $voter_data = [];
    $total_voters = ['yes' => 0, 'no' => 0];

    while ($row = $voter_result->fetch_assoc()) {
        $voter_data[$row['barangay']][$row['voter_status']] = $row['count'];
        $total_voters[$row['voter_status']] += $row['count'];
    }


    // Prepare data for voter status chart
    $voter_data_array = [];
    foreach ($barangays as $barangay) {
        $voter_data_array['yes'][] = $voter_data[$barangay]['yes'] ?? 0;
        $voter_data_array['no'][] = $voter_data[$barangay]['no'] ?? 0;
    }

    // SQL query for employment status distribution
    $employment_sql = "SELECT barangay, employment, COUNT(*) AS count
                       FROM rest
                       WHERE employment IN ('Employed', 'Unemployed', 'Student')
                       GROUP BY barangay, employment";

    $employment_result = $conn->query($employment_sql);
    $employment_data = [];
    $total_employment = ['Employed' => 0, 'Unemployed' => 0, 'Student' => 0];

    while ($row = $employment_result->fetch_assoc()) {
        $employment_data[$row['barangay']][$row['employment']] = $row['count'];
        $total_employment[$row['employment']] += $row['count'];
    }

    // Prepare data for employment status chart
    $employment_data_array = [];
    foreach ($barangays as $barangay) {
        $employment_data_array['Employed'][] = $employment_data[$barangay]['Employed'] ?? 0;
        $employment_data_array['Unemployed'][] = $employment_data[$barangay]['Unemployed'] ?? 0;
        $employment_data_array['Student'][] = $employment_data[$barangay]['Student'] ?? 0;
    }

    $population_sql = "SELECT barangay, COUNT(*) AS total_population
    FROM rest
    GROUP BY barangay";

$population_result = $conn->query($population_sql);
$population_data = [];

while ($row = $population_result->fetch_assoc()) {
$population_data[$row['barangay']] = $row['total_population'];
}

// Prepare data for population chart
$total_population = [];
foreach ($barangays as $barangay) {
$total_population[] = $population_data[$barangay] ?? 0;
}

$conn->close();
?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Age Distribution Chart
    const ctxAge = document.getElementById('ageDistributionChart').getContext('2d');
    const ageDistributionChart = new Chart(ctxAge, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barangays); ?>,
            datasets: [
                {
                    label: '0-19',
                    data: <?php echo json_encode($age_data['0-19']); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: '20-39',
                    data: <?php echo json_encode($age_data['20-39']); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: '40-59',
                    data: <?php echo json_encode($age_data['40-59']); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                },
                {
                    label: '60+',
data: <?php echo json_encode($age_data['60+']); ?>,
backgroundColor: 'rgba(105, 105, 105, 0.2)', // dark grey with some transparency
borderColor: 'rgba(105, 105, 105, 1)', // solid dark grey border
borderWidth: 1

                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Data description for Age Distribution
    ageDataDescription.innerHTML = '<h5>Total Residents by Age Range:</h5>' +
    '<ul>' +
    '<?php foreach ($age_ranges as $range): ?>' +
    '<li><?php echo $range; ?>: ' + 
    <?php echo json_encode($age_data[$range]); ?>.map(Number).reduce((a, b) => a + b, 0) + 
    '</li>' +
    '<?php endforeach; ?>' +
    '</ul>';




        
    // Voter Status Distribution Chart
    const ctxVoter = document.getElementById('voterStatusChart').getContext('2d');
    const voterStatusChart = new Chart(ctxVoter, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barangays); ?>,
            datasets: [
                {
                    label: 'Yes',
                    data: <?php echo json_encode($voter_data_array['yes']); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'No',
                    data: <?php echo json_encode($voter_data_array['no']); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Data description for Voter Status Distribution
    const voterDataDescription = document.getElementById('voterDataDescription');
    voterDataDescription.innerHTML = '<h5>Total Voters:</h5>' +
        '<ul>' +
        '<li>Yes: <?php echo $total_voters["yes"]; ?></li>' +
        '<li>No: <?php echo $total_voters["no"]; ?></li>' +
        '</ul>';

    // Employment Status Distribution Chart
    const ctxEmployment = document.getElementById('employmentStatusChart').getContext('2d');
    const employmentStatusChart = new Chart(ctxEmployment, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barangays); ?>,
            datasets: [
                {
                    label: 'Employed',
                    data: <?php echo json_encode($employment_data_array['Employed']); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Unemployed',
                    data: <?php echo json_encode($employment_data_array['Unemployed']); ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Student',
                    data: <?php echo json_encode($employment_data_array['Student']); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctxPopulation = document.getElementById('populationChart').getContext('2d');
    const populationChart = new Chart(ctxPopulation, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($barangays); ?>,
            datasets: [{
                label: 'Total Population',
                data: <?php echo json_encode($total_population); ?>,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    // Data description for Employment Status Distribution
    const employmentDataDescription = document.getElementById('employmentDataDescription');
    employmentDataDescription.innerHTML = '<h5>Total Employment Status:</h5>' +
        '<ul>' +
        '<li>Employed: <?php echo $total_employment["Employed"]; ?></li>' +
        '<li>Unemployed: <?php echo $total_employment["Unemployed"]; ?></li>' +
        '<li>Student: <?php echo $total_employment["Student"]; ?></li>' +
        '</ul>';
        const populationDataDescription = document.getElementById('populationDataDescription');
    populationDataDescription.innerHTML = '<h5>Total Population:</h5>' +
        '<ul>' +
        '<?php foreach ($barangays as $barangay): ?>' +
        '<li><?php echo $barangay; ?>: ' + (<?php echo json_encode($population_data); ?>[<?php echo json_encode($barangay); ?>] || 0) + '</li>' +
        '<?php endforeach; ?>' +
        '</ul>';
    </script>

<script>
         function displayDateTime() {
        const dateTimeContainer = document.getElementById("date-time-container");
        const now = new Date();

        // Format the date and time
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = now.toLocaleDateString(undefined, options); // e.g., November 16, 2024
        const formattedTime = now.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' }); // e.g., 02:45:20 PM

        // Display the date and time in the container
        dateTimeContainer.textContent = `Date: ${formattedDate} | Time: ${formattedTime}`;
    }

    // Call the function on page load
    displayDateTime();

    // Update the time every second
    setInterval(displayDateTime, 1000);
    </script>


</body>

</html>
