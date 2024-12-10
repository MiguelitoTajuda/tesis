<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="style.css">
    <title>JASAANKNOWN</title>

</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" class="logo">
            <div class="logo-name"><span>Jasaan</span>Known</div>
        </a>
        <ul class="side-menu">
            <li class=""><a href="dashboard.php" onclick="redirectTo('dashboard.php')"><i class='bx bxs-dashboard'></i>Dashboard</a></li>
            <li class=""><a href="barangay.php" onclick="redirectTo('barangay.php')"><i class='bx bxs-compass'></i>Barangays</a></li>
            <li class="active"><a href="maps.php" onclick="redirectTo('maps.php')"><i class='bx bx-map-alt'></i>Maps</a></li>
            <li class=""><a href="users.php" onclick="redirectTo('users.php')"><i class='bx bx-group'></i>Users</a></li>
            <li class=""><a href="archive.php" onclick="redirectTo('archv.php')"><i class='bx bx-cog'></i>Archive</a></li>
        </ul>
        <ul class="side-menu">
            <li>
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
            <input type="checkbox" id="theme-toggle" hidden>
            <label for="theme-toggle" class="theme-toggle"></label>
            <a href="#" class="notif">
                <i class='bx bx-bell'></i>
                <span class="count"></span>
            </a>
            <a href="#" class="profile">
                <img src="images/logo.png">
            </a>
        </nav>

        <!-- End of Navbar -->

        <main>
            <div class="header">
                <div class="left">
                <body>
  <h1>Google Map</h1>
  <iframe
    width="1558"
    height="610"
    frameborder="0" style="border:0"
    src="https://www.google.com/maps/embed?ll=8.6498173,124.7548322&z=15" allowfullscreen>
  </iframe>
</body>
                    <ul class="breadcrumb">
                        <li><a href="#">
                            
                            </a></li>
                    </ul>
                </div>
              
            </div>

        </main>

    </div>

    <script src="index.js"></script>
</body>

</html>