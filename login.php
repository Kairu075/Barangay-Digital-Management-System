<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

            header('Location: ' . SITE_URL . '/dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please enter your username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay San Marino Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --blue: #446CAC;
            --blue-dark: #2d4d80;
            --gold: #FBC531;
            --cloud: #F0EEE9;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3557 0%, #2d4d80 50%, #446CAC 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(251,197,49,0.12) 0%, transparent 70%);
            top: -150px; right: -150px;
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
            bottom: -100px; left: -100px;
            border-radius: 50%;
        }
        .login-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 900px;
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .login-left {
            background: linear-gradient(160deg, #1e3557 0%, #446CAC 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border: 40px solid rgba(251,197,49,0.1);
            border-radius: 50%;
            top: -100px; left: -100px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border: 30px solid rgba(255,255,255,0.05);
            border-radius: 50%;
            bottom: -50px; right: -50px;
        }
        .emblem {
            width: 100px; height: 100px;
            background: var(--gold);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 30px rgba(251,197,49,0.5);
            position: relative;
            z-index: 1;
        }
        .emblem i { font-size: 40px; color: var(--blue-dark); }
        .login-left h1 {
            font-family: 'Lora', serif;
            font-size: 26px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            position: relative;
            z-index: 1;
        }
        .login-left .sub {
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            margin-top: 8px;
            position: relative;
            z-index: 1;
        }
        .gold-divider {
            width: 50px;
            height: 3px;
            background: var(--gold);
            border-radius: 2px;
            margin: 16px auto;
            position: relative;
            z-index: 1;
        }
        .login-left .desc {
            color: rgba(255,255,255,0.55);
            font-size: 12.5px;
            max-width: 240px;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }
        .login-badges {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 28px;
            position: relative;
            z-index: 1;
        }
        .login-badge {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .login-right {
            padding: 50px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right h2 {
            font-size: 24px;
            font-weight: 800;
            color: #1e3557;
            margin-bottom: 6px;
        }
        .login-right .greeting {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 32px;
        }
        .error-msg {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #dee2e6;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #343a40;
            transition: all 0.2s;
        }
        input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(68,108,172,0.12);
        }
        .toggle-pw {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
            font-size: 14px;
        }
        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--blue) 0%, var(--blue-dark) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 20px rgba(68,108,172,0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(68,108,172,0.4);
        }
        .demo-accounts {
            margin-top: 24px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        .demo-accounts p {
            font-size: 11.5px;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
        }
        .demo-btn {
            padding: 7px 10px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: white;
            font-family: inherit;
            font-size: 11.5px;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
        }
        .demo-btn:hover { background: var(--blue); color: white; border-color: var(--blue); }
        .demo-btn strong { display: block; font-size: 12px; }
        @media (max-width: 640px) {
            .login-container { grid-template-columns: 1fr; }
            .login-left { padding: 32px 24px; }
            .login-right { padding: 32px 24px; }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-left">
        <div class="emblem">
            <i class="fas fa-shield-halved"></i>
        </div>
        <h1>Barangay San Marino</h1>
        <div class="gold-divider"></div>
        <p class="sub">City of Manila, Metro Manila</p>
        <p class="desc">Integrated Digital Management System — Serving the community with transparency, efficiency, and excellence.</p>
        <div class="login-badges">
            <span class="login-badge"><i class="fas fa-users"></i> 10,500 Residents</span>
            <span class="login-badge"><i class="fas fa-home"></i> 3,800 Households</span>
            <span class="login-badge"><i class="fas fa-lock"></i> Secure System</span>
        </div>
    </div>

    <div class="login-right">
        <h2>Welcome Back</h2>
        <p class="greeting">Sign in to access the barangay management system.</p>

        <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-circle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <div class="input-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="Enter your username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="passwordField" placeholder="Enter your password" required>
                    <i class="fas fa-eye toggle-pw" id="togglePw"></i>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-right-to-bracket"></i>
                Sign In
            </button>
        </form>

        <div class="demo-accounts">
            <p>Demo Accounts (password: <strong>password</strong>)</p>
            <div class="demo-grid">
                <button class="demo-btn" onclick="fillLogin('admin','password')">
                    <strong>Admin</strong>admin
                </button>
                <button class="demo-btn" onclick="fillLogin('captain_reyes','password')">
                    <strong>Captain</strong>captain_reyes
                </button>
                <button class="demo-btn" onclick="fillLogin('sec_santos','password')">
                    <strong>Secretary</strong>sec_santos
                </button>
                <button class="demo-btn" onclick="fillLogin('treas_garcia','password')">
                    <strong>Treasurer</strong>treas_garcia
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('togglePw').addEventListener('click', function () {
    const input = document.getElementById('passwordField');
    input.type = input.type === 'password' ? 'text' : 'password';
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
});
function fillLogin(user, pass) {
    document.querySelector('[name=username]').value = user;
    document.getElementById('passwordField').value = pass;
}
</script>
</body>
</html>
