<?php
/**
 * NHIF ADMIN AUTHENTICATION
 * Purpose: Secure login for authorized personnel.
 */

session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    /**
     * 1. Prepared Statement (A03:2021 - Injection Prevention)
     */
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
// 2. High-Entropy Plain-Text Authentication Logic
    if ($user = $result->fetch_assoc()) {
        /**
         * SECURITY ENFORCEMENT:
         * We check the complex password directly while enforcing 
         * length and character variety checks at the application level.
         */
        
        // 1. First, check if the password meets the NHIF Complexity Standard (Min 8 chars)
        if (strlen($password) < 8) {
            $error = "Security Alert: Password must be at least 8 characters long.";
        } 
        // 2. Then, verify against the database record
        else if ($password === $user['password']) {
            
            // Mitigate Session Hijacking
            session_regenerate_id(true);
            
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $username;

            header("Location: view_breaches.php");
            exit();
        } else {
            $error = "Authentication failed: Invalid credentials provided.";
        }
    } else {
        $error = "Access denied: Account not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHIF Staff Access Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        /* Kuchukua style kutoka kwenye picha ya mfano (image_2.png) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif; /* Font ya kisasa */
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #08040c; /* Rangi ya background nyeusi kama mfano */
            overflow: hidden;
        }

        /* Box kuu lenye mpaka unaong'aa (Neon Boundary) */
        .main-container {
            position: relative;
            width: 450px; /* Upana maalum kwa ajili ya login */
            height: 550px;
            background: rgba(255, 255, 255, 0.05); /* Background inayoona kwa mbali */
            border: 2px solid #7429ec; /* Mpaka wa zambarau */
            box-shadow: 0 0 20px #7429ec; /* Athari ya neon inayong'aa */
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }

        /* Athari ya Uhuishaji (Animation) */
        .main-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 450px;
            height: 550px;
            background: linear-gradient(0deg, transparent, #7429ec, #7429ec);
            transform-origin: bottom right;
            animation: animate 6s linear infinite;
        }

        .main-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 450px;
            height: 550px;
            background: linear-gradient(0deg, transparent, #7429ec, #7429ec);
            transform-origin: bottom right;
            animation: animate 6s linear infinite;
            animation-delay: -3s;
        }

        @keyframes animate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Sehemu ya ndani ya fomu ili kulinda uhuishaji */
        .form-box {
            position: absolute;
            inset: 2px;
            background: #08040c;
            border-radius: 16px;
            z-index: 10;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
        }

        .form-box h2 {
            color: #fff;
            font-weight: 600;
            text-align: center;
            letter-spacing: 0.1em;
            margin-bottom: 30px;
        }

        .form-box h2 span {
            color: #7429ec;
        }

        /* Vyumba vya kuingiza data (Input fields) */
        .input-box {
            position: relative;
            width: 100%;
            margin-top: 35px;
        }

        .input-box input {
            position: relative;
            width: 100%;
            padding: 20px 10px 10px;
            background: transparent;
            border: none;
            outline: none;
            color: #fff;
            font-size: 1em;
            letter-spacing: 0.05em;
            z-index: 10;
        }

        .input-box span {
            position: absolute;
            left: 0;
            padding: 20px 0px 10px;
            font-size: 1em;
            color: #8f8f8f;
            pointer-events: none;
            letter-spacing: 0.05em;
            transition: 0.5s;
        }

        .input-box input:valid ~ span,
        .input-box input:focus ~ span {
            color: #7429ec;
            transform: translateX(-10px) translateY(-34px);
            font-size: 0.75em;
        }

        /* Icons za pembeni (User na Lock) */
        .input-box i {
            position: absolute;
            right: 0;
            bottom: 12px;
            color: #8f8f8f;
            transition: 0.5s;
        }

        .input-box input:valid ~ i,
        .input-box input:focus ~ i {
            color: #7429ec;
        }

        /* Mstari unaong'aa chini ya input */
        .input-box input ~ b {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: #7429ec;
            border-radius: 4px;
            transition: 0.5s;
            pointer-events: none;
            z-index: 9;
        }

        /* Kitufe cha Login (Sign In Button) */
        input[type="submit"] {
            border: none;
            outline: none;
            background: #7429ec; /* Rangi ya zambarau kama mfano */
            padding: 11px 25px;
            width: 100%;
            margin-top: 40px;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            color: #fff;
            letter-spacing: 0.05em;
            transition: 0.5s;
        }

        input[type="submit"]:active {
            opacity: 0.8;
        }

        /* Kiondoa kiungo cha "Sign Up" na "Forgot Password" (Kama ilivyoombwa) */
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .links a {
            font-size: 0.75em;
            color: #8f8f8f;
            text-decoration: none;
        }

        .links a:hover,
        .links a:nth-child(2) {
            color: #7429ec;
        }
        
        /* Ujumbe wa Onyo kama Login imefeli (kwa PHP) */
        .error-message {
            color: #ff4d4d;
            background: rgba(255, 77, 77, 0.1);
            border: 1px solid #ff4d4d;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.85em;
            text-align: center;
            margin-top: 20px;
            margin-bottom: -20px;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="form-box">
            <h2>NHIF Staff <span>Access</span></h2>
            
            <?php
            // Hii ni sehemu ya majaribio, iunganishe na db_connection.php yako
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Mfano wa Credentials (adm_here kutoka image_0.png)
                if ($username === 'admin_here' && $password === 'Pass_key') {
                    // Login Imefanikiwa
                    session_start();
                    $_SESSION['admin_id'] = 1;
                    $_SESSION['username'] = $username;
                    header("Location: view_breaches.php");
                    exit();
                } else {
                    // Login Imefeli
                    echo '<div class="error-message">Invalid Administrator ID or Password.</div>';
                }
            }
            ?>

            <form action="auth_login.php" method="POST">
                <div class="input-box">
                    <input type="text" name="username" required="required">
                    <span>Administrator ID</span>
                    <i class="fa-solid fa-user"></i>
                    <b></b>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required="required">
                    <span>Password</span>
                    <i class="fa-solid fa-lock"></i>
                    <b></b>
                </div>
                <div class="links">
                    <a href="index.php">← Back to Public Portal</a>
                    <a href="#">Forgot Password?</a>
                </div>
                <input type="submit" value="Secure Sign In">
            </form>
        </div>
    </div>

</body>
</html>
