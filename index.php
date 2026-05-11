<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHIF Security Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        /* Muonekano wa Neon & Dark Mode */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        
        body {
            background: #08040c;
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar ya Juu */
        nav {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid #7429ec;
            box-shadow: 0 0 15px #7429ec;
        }

        .logo { font-size: 24px; font-weight: bold; color: #7429ec; }
        
        /* Kiungo cha Staff (Hiki kipo Pembeni) */
        .staff-link {
            text-decoration: none;
            color: #fff;
            border: 1px solid #7429ec;
            padding: 8px 20px;
            border-radius: 5px;
            transition: 0.5s;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .staff-link:hover {
            background: #7429ec;
            box-shadow: 0 0 20px #7429ec;
        }

        /* Container Kuu yenye Boundary */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .report-box {
            position: relative;
            width: 100%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.03);
            padding: 40px;
            border-radius: 20px;
            border: 1px solid rgba(116, 41, 236, 0.5);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .report-box h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            color: #7429ec;
            text-transform: uppercase;
        }

        /* Fomu ya Ripoti */
        .input-group { margin-bottom: 25px; }
        
        label { display: block; margin-bottom: 10px; color: #8f8f8f; }

        input, textarea {
            width: 100%;
            padding: 15px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            outline: none;
            transition: 0.5s;
        }

        input:focus, textarea:focus {
            border-color: #7429ec;
            box-shadow: 0 0 10px #7429ec;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #7429ec;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            font-size: 18px;
            cursor: pointer;
            transition: 0.5s;
        }

        .btn-submit:hover {
            box-shadow: 0 0 25px #7429ec;
            transform: scale(1.02);
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">NHIF SECURITY REPORT2026 </div>
        <a href="auth_login.php" class="staff-link">Staff Login (En)</a>
    </nav>

    <div class="main-content">
        <div class="report-box">
            <h2>Tuma Ripoti ya Usalama (Public Portal)</h2>
            
            <form action="save_breach_public.php" method="POST">
                <div class="input-group">
                    <label>Kichwa cha Habari/ Title</label>
                    <input type="text" name="title" placeholder="Mfano: kuripoti uhalifu..." required>
                </div>

                <div class="input-group">
                    <label>Maelezo Kamili / Description</label>
                    <textarea name="description" rows="5" placeholder="Elezea hapa ulichokiona..." required></textarea>
                </div>

                <button type="submit" class="btn-submit">TUMA TAARIFA KWA USALAMA</button>
            </form>
        </div>
    </div>

    <footer>
        &copy; 2026 NHIF Cybersecurity experts | Developed by Frank Charles Karani
    </footer>

</body>
</html>