<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Barangay San Marino' ?> - Barangay San Marino Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-wrap">
            <div class="logo-icon">
    <img src="<?= SITE_URL ?>/assets/img/logo.png" alt="Logo" style="width:32px;height:32px;object-fit:contain;">
            </div>
            <div class="logo-text">
                <span class="logo-main">San Marino</span>
                <span class="logo-sub">Barangay System</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="user-card">
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></span>
            <span class="user-role badge-<?= $_SESSION['role'] ?? '' ?>"><?= ucfirst($_SESSION['role'] ?? '') ?></span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-label">Main Menu</span>
        <a href="<?= SITE_URL ?>/dashboard.php" class="nav-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>

        <?php if (hasRole(['admin','secretary','captain'])): ?>
        <span class="nav-label">Records</span>
        <a href="<?= SITE_URL ?>/modules/residents/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'residents') !== false ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Residents</span>
        </a>
        <a href="<?= SITE_URL ?>/modules/documents/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'documents') !== false ? 'active' : '' ?>">
            <i class="fas fa-file-certificate"></i>
            <span>Documents</span>
        </a>
        <?php endif; ?>

        <span class="nav-label">Services</span>
        <a href="<?= SITE_URL ?>/modules/complaints/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'complaints') !== false ? 'active' : '' ?>">
            <i class="fas fa-triangle-exclamation"></i>
            <span>Complaints</span>
        </a>
        <a href="<?= SITE_URL ?>/modules/announcements/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'announcements') !== false ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i>
            <span>Announcements</span>
        </a>

        <?php if (hasRole(['admin','treasurer','captain'])): ?>
        <span class="nav-label">Finance</span>
        <a href="<?= SITE_URL ?>/modules/finance/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'finance') !== false ? 'active' : '' ?>">
            <i class="fas fa-peso-sign"></i>
            <span>Financial Records</span>
        </a>
        <?php endif; ?>

        <?php if (hasRole(['admin'])): ?>
        <span class="nav-label">System</span>
        <a href="<?= SITE_URL ?>/modules/users/index.php" class="nav-item <?= strpos($_SERVER['REQUEST_URI'],'users') !== false ? 'active' : '' ?>">
            <i class="fas fa-user-shield"></i>
            <span>User Management</span>
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= SITE_URL ?>/logout.php" class="nav-item logout-btn">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div class="main-wrapper" id="mainWrapper">
    <!-- TOP BAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <span class="breadcrumb-barangay"><i class="fas fa-location-dot"></i> Barangay San Marino</span>
                <i class="fas fa-chevron-right"></i>
                <span><?= $page_title ?? 'Dashboard' ?></span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="fas fa-calendar"></i>
                <span id="currentDate"></span>
            </div>
            <div class="topbar-time">
                <i class="fas fa-clock"></i>
                <span id="currentTime"></span>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
