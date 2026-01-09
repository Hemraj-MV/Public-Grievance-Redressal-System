<?php
session_start();
include 'db_connect.php';

// Handle Login Logic
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin_users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Plain text check (Update to password_verify in production)
        if ($password == $row['password']) { 
            $_SESSION['admin_logged_in'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect password provided.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrative Access | Governance AI</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            overflow: hidden;
        }
        
        /* Left Side - The Branding Area */
        .brand-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .brand-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }

        /* Right Side - The Login Form */
        .login-section {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }

        .form-floating > .form-control:focus ~ label {
            color: #1e3a8a;
        }
        .form-control:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 0.25rem rgba(30, 58, 138, 0.15);
        }

        .btn-primary-custom {
            background-color: #1e3a8a;
            border: none;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            background-color: #172554;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
        }
    </style>
</head>
<body>

<div class="container-fluid h-100">
    <div class="row h-100">
        
        <div class="col-lg-7 d-none d-lg-flex flex-column justify-content-center px-5 brand-section">
            <div class="position-relative z-2 ms-5">
                <div class="mb-4">
                    <i class="bi bi-shield-lock-fill display-4 text-info"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Secure Intelligence for<br>Public Governance.</h1>
                <p class="lead opacity-75 mb-5" style="max-width: 500px;">
                    Automated triaging, duplicate detection, and sentiment analysis for citizen grievances.
                </p>
                <div class="d-flex gap-4 opacity-75 small text-uppercase fw-bold ls-1">
                    <span><i class="bi bi-check-circle me-2"></i>Encrypted</span>
                    <span><i class="bi bi-check-circle me-2"></i>Authorized Only</span>
                </div>
            </div>
        </div>

        <div class="col-lg-5 login-section">
            <div class="login-wrapper">
                
                <div class="text-center mb-5">
                    <div class="d-inline-block p-3 rounded-circle bg-light mb-3 text-primary">
                        <i class="bi bi-person-badge fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Welcome Back</h3>
                    <p class="text-muted">Please enter your details to sign in.</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        <div><?php echo $error; ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" placeholder="name@example.com" required>
                        <label for="username">Admin Username</label>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary-custom w-100 rounded-3 mb-4">
                        Sign In to Dashboard
                    </button>
                    
                    <div class="text-center">
                        <a href="index.php" class="text-decoration-none text-muted small hover-link">
                            <i class="bi bi-arrow-left me-1"></i> Back to Public Portal
                        </a>
                    </div>
                </form>
                
                <div class="mt-5 text-center">
                    <small class="text-muted opacity-50">&copy; 2026 Governance AI Initiative</small>
                </div>
            </div>
        </div>
        
    </div>
</div>

</body>
</html>