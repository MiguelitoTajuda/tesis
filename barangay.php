<?php
session_start();

// Define access rules: Map user roles to barangays they can access
$access_rules = [
    'Admin Kimaya' => ['Kimaya'],
    'Admin Upper' => ['Upper Jasaan'],
    'Admin Jampason' => ['Jampason'],
    'Admin Solana' => ['Solana'],
    'Admin Luz Banson' => ['Luz Banson'],
    'Admin Aplaya' => ['Aplaya'],
    'Admin I.S Cruz' => ['I.S Cruz'],
    'Admin Natubo' => ['Natubo'],
    'Admin San Antonio' => ['San Antonio'],
    'Admin Danao' => ['Danao'],
    'Admin Bobontugan' => ['Bobontugan'],
    'Admin Corrales' => ['Corrales'],
    'Admin San Isidro' => ['San Isidro'],
    'Admin San Nicolas' => ['San Nicolas'],
    'Admin Lower Jasaan' => ['Lower Jasaan'],
    'Super Admin' => [
        'Kimaya', 'Upper Jasaan', 'Jampason', 'Solana', 'Luz Banson',
        'Aplaya', 'I.S Cruz', 'Natubo', 'San Antonio', 'Danao',
        'Bobontugan', 'Corrales', 'San Isidro', 'San Nicolas', 'Lower Jasaan'
    ],
];

// Redirect to login if session variables are not set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php");
    exit();
}

// Retrieve the user role from the session
$user_role = $_SESSION['user_role'];

// Define barangay details
$barangays = [
    ['name' => 'Upper Jasaan', 'image' => 'images/uj.png', 'link' => 'dashboard.php'],
    ['name' => 'Lower Jasaan', 'image' => 'images/lj.png', 'link' => 'dashboard.php'],
    ['name' => 'Bobontugan', 'image' => 'images/bontugan.png', 'link' => 'dashboard.php'],
    ['name' => 'Jampason', 'image' => 'images/jampason.png', 'link' => 'dashboard.php'],
    ['name' => 'Kimaya', 'image' => 'images/kimaya.png', 'link' => 'dashboard.php'],
    ['name' => 'Luz Banson', 'image' => 'images/lb.png', 'link' => 'dashboard.php'],
    ['name' => 'Natubo', 'image' => 'images/natubo.png', 'link' => 'dashboard.php'],
    ['name' => 'Danao', 'image' => 'images/danao.png', 'link' => 'dashboard.php'],
    ['name' => 'San Antonio', 'image' => 'images/sa.png', 'link' => 'dashboard.php'],
    ['name' => 'San Nicolas', 'image' => 'images/sn.png', 'link' => 'dashboard.php'],
    ['name' => 'Corrales', 'image' => 'images/corrales.png', 'link' => 'dashboard.php'],
    ['name' => 'Natubo', 'image' => 'images/natubo.png', 'link' => 'dashboard.php'],
    ['name' => 'Solana', 'image' => 'images/solana.png', 'link' => 'dashboard.php'],
    ['name' => 'Aplaya', 'image' => 'images/aplaya.png', 'link' => 'dashboard.php'],
    ['name' => 'I.S Cruz', 'image' => 'images/is.png', 'link' => 'dashboard.php'],
    // Add more barangays as needed
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>JASAANKNOWN</title>
    <style>

/* Container for each barangay item */
.insights li {
    list-style-type: none;
    margin: 20px 0;
    padding: 15px;
    border-radius: 8px;
    background-color: #f0f0f0; /* Light gray for all items */
    transition: all 0.3s ease-in-out;
}

/* Accessible barangays */
.insights li a {
    text-decoration: none;
    color: #333; /* Default dark color for accessible barangays */
    font-weight: bold;
    display: flex;
    align-items: center;
}

/* Highlight accessible barangays with a unique color */
.insights li a:hover {
    color: #fff;
    border-radius: 5px;
    padding: 5px;
}

/* Icon and Text Alignment */
.insights li i img {
    margin-right: 10px;
    width: 60px;
    height: 60px;
    border-radius: 8px;
}

/* Inaccessible barangays */
.insights li a[style*="pointer-events:none;"] {
    color: #888; /* Gray color for inaccessible barangays */
    cursor: not-allowed;
}

/* Hover effect for inaccessible barangays */
.insights li a[style*="pointer-events:none;"]:hover {
    background-color: transparent;
    color: #888;
}

/* Opacity and muted effect for inaccessible barangays */
.insights li[style*="opacity:0.5;"] {
    opacity: 0.5;
}

/* Container for barangay names */
.info p {
    margin: 0;
    font-size: 18px;
}

/* Style for the whole item */
.insights li:hover {
    transform: scale(1.05);
}

/* Styling for the logo/image */
.sidebar .logo img {
    width: 100px;
    height: auto;
    border-radius: 50%;
}


    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <ul class="side-menu">
    <?php if ($user_role === 'Super Admin'): ?>
        <li><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bx-home'></i>Dashboard</a></li>
    <?php endif; ?>
    <li class="active"><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bx-map'></i>Barangays</a></li>
    <li><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-user'></i>Users</a></li>
    <li><a href="archive.php" onclick="redirectTo('archive.php')"><i class='bx bx-folder'></i>Archive</a></li>
</ul>

        <ul class="side-menu">
            <li>
            <br><br><br><br><br><br><br><br><br><br><br><br>
                <a href="#" class="logout">
                    <i class='bx bx-log-out-circle'></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
    <!-- End of Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <!-- Navbar -->
        <nav>
            <i class='bx bx-menu'></i>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button class="search-btn" type="submit"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <div id="date-time-container"></div>
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="profile">
                <img src="images/logo-x.png">
            </a>
        </nav>

        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                    <h1>Barangays</h1>
                    <ul class="breadcrumb">
                        <li><a href="#"></a></li>
                    </ul>
                </div>
            </div>

            <ul class="insights">
    <?php foreach ($barangays as $barangay): ?>
        <?php
        // Check if the user's role has access to the current barangay
        $is_accessible = isset($access_rules[$user_role]) && in_array($barangay['name'], $access_rules[$user_role]);
        ?>
        <li style="<?= !$is_accessible ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
            <i>
                <img src="<?= $barangay['image'] ?>" alt="<?= $barangay['name'] ?>">
            </i>
            <span class="info">
                <a href="<?= $barangay['link'] ?>" <?= !$is_accessible ? 'style="pointer-events:none;opacity:0.5;"' : '' ?>>
                    <p><?= $barangay['name'] ?></p>
                </a>
            </span>
        </li>
    <?php endforeach; ?>
</ul>

            <!-- End of Insights -->

        </main>

    </div>

    <script src="index.js"></script>

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