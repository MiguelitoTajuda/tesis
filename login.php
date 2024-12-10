<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = ""; // Your MySQL root password
    $dbname = "jk";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to fetch user data using prepared statements
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];  // User role, e.g., 'adminlower'

            // Redirect to dashboard
            if ($user['role'] == 'Super Admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: barangay.php");
            }
            exit(); // Ensure subsequent code is not executed after redirection
        } else {
            // Invalid password
            $message = "Invalid email or password";
        }
    } else {
        // Invalid email
        $message = "Invalid email or password";
    }

    // Close prepared statement and database connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <title>JasaanKnown - Login</title>
   <style>
       @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap");

       * {
           margin: 0;
           padding: 0;
           box-sizing: border-box;
           font-family: "Poppins", sans-serif;
       }

       body {
           display: flex;
           justify-content: center;
           align-items: center;
           min-height: 100vh;
           background: gainsboro;
           background-size: cover;
           background-position: center;
           background-image: url('images/js.png');
    background-size: cover; /* Adjusts the image to cover the entire background */
    background-repeat: no-repeat; /* Ensures the image doesn't repeat */
    background-position: center; /* Centers the image */
}
       .wrapper {
           text-align: center;
           width: 500px;
           background: white;
           backdrop-filter: blur(20px);
           color: #191970;
           border-radius: 30px;
           padding: 30px 40px;
           transform: translate(20%, -10%);
           height: 400px;
       }

       .wrapper h1 {
           font-size: 30px;
           text-align: center;
       }

       .form-group {
    position: relative;
    width: 100%;
    height: 60px; /* Match the height of the input */
    margin: 30px 0;
}

.form-group i {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: gray;
    font-size: 18px;
    cursor: pointer;
}


       .form-group input {
           width: 400px;
           height: 60px;
           outline: none;
           border: #191970;
           border-radius: 18px;
           font-size: 16px;
           color: gray;
           box-shadow: 0 0 5px rgba(128, 128, 128, 0.5);
           padding: 20px 45px 20px 20px;
       }

       .form-group input::placeholder {
           color: gray;
       }

       .form-btn {
           margin-top: 10px;
       }

       .btn {
           width: 95%;
           height: 60px;
           background-color: #187bcd;
           border: none;
           outline: none;
           border-radius: 20px;
           color: white;
           box-shadow: 0 0 10px rgba(255, 255, 255, .1);
           cursor: pointer;
           font-size: 16px;
           font-weight: 600;
       }

       .toggle-password {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: gray;
    font-size: 18px;
}

/* Add a more defined focus on the password field when the icon is visible */
.form-group input:focus + .toggle-password {
    color: #187bcd;
}

       .form-container {
           margin-left: 700px;
           text-align: center;
           width: 50%;
           padding: 20px;
           box-sizing: border-box;
       }

       .about-container {
           width: 600px;
           background-size: cover;
           background-position: center;
           border-radius: 10px;
           padding: 30px 40px;
           margin-top: -500px;
           margin-left: -100px;
       }

       .forgot-password-link {
           color: #187bcd;
           text-decoration: none;
           margin-left: 10px;
           font-size: 16px;
           transition: text-decoration 0.3s ease;
       }

       .forgot-password-link:hover {
           text-decoration: underline;
       }

       

   </style>
</head>

<body>
<div class="container">
        <div class="form-container">
            <form action="" method="post">
                <?php
                if (isset($message)) {
                    echo '<div class="message">' . $message . '</div>';
                }
                ?>

<div class="wrapper" style="background-color: whitesmoke;">
                    <h1><span style="color: orange;">Jasaan</span><span style="color: red;">Known</span></h1>
                    <div class="form-group">
                        <input type="email" placeholder="Email or phone number" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
    <input type="password" placeholder="Password" name="password" class="form-control" id="password" required>
    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
</div>
                    <div class="form-btn">
                        <input type="submit" value="Login" name="submit" class="btn btn-primary"><br></br>
                    </div>
                </div>
            </form>
        </div>

        <div class="about-container">
            <img src="images/official.png" alt="Description of your image">
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const passwordInput = document.getElementById('password');
                const togglePasswordButton = document.getElementById('togglePassword');

                togglePasswordButton.style.display = 'none';

                passwordInput.addEventListener('focus', function () {
                    togglePasswordButton.style.display = 'block';
                });

                passwordInput.addEventListener('blur', function () {
                    if (passwordInput.value.trim() === '') {
                        togglePasswordButton.style.display = 'none';
                    }
                });

                passwordInput.addEventListener('input', function () {
                    if (passwordInput.value.trim() !== '') {
                        togglePasswordButton.style.display = 'block';
                    } else {
                        togglePasswordButton.style.display = 'none';
                    }

                    if (passwordInput.getAttribute('type') === 'text') {
                        passwordInput.setAttribute('type', 'password');
                        togglePasswordButton.classList.remove('fa-eye');
                        togglePasswordButton.classList.add('fa-eye-slash');
                    }
                });

                togglePasswordButton.addEventListener('click', function () {
                    if (passwordInput.getAttribute('type') === 'password') {
                        passwordInput.setAttribute('type', 'text');
                        togglePasswordButton.classList.remove('fa-eye-slash');
                        togglePasswordButton.classList.add('fa-eye');
                    } else {
                        passwordInput.setAttribute('type', 'password');
                        togglePasswordButton.classList.remove('fa-eye');
                        togglePasswordButton.classList.add('fa-eye-slash');
                    }
                });
            });
        </script>
    </div>
</body>
</html>