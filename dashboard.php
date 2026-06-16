<?php
session_start();
include("db_connect.php");

// Session Protection Guard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php"); // <--- Pastikan nama fail login admin anda betul di sini
    exit();
}

// 1. Fetch KPI Metrics counts from Complaints Table
$total_query     = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints");
$progress_query  = mysqli_query($conn, "SELECT COUNT(*) as progress FROM complaints WHERE status = 'In Progress'");
$resolved_query  = mysqli_query($conn, "SELECT COUNT(*) as resolved FROM complaints WHERE status = 'Resolved'");
$pending_query   = mysqli_query($conn, "SELECT COUNT(*) as pending FROM complaints WHERE status = 'Pending'");

$total_complaints = mysqli_fetch_assoc($total_query)['total'];
$in_progress      = mysqli_fetch_assoc($progress_query)['progress'];
$resolved         = mysqli_fetch_assoc($resolved_query)['resolved'];
$pending          = mysqli_fetch_assoc($pending_query)['pending'];

// 2. Fetch System Overview Elements
$users_query      = mysqli_query($conn, "SELECT COUNT(*) as total_users FROM users");
$total_users      = mysqli_fetch_assoc($users_query)['total_users'];

// Count unique historical dynamic values (Building blocks / category metrics)
$category_query   = mysqli_query($conn, "SELECT COUNT(DISTINCT damage_type) as total_cat FROM complaints");
$total_categories = mysqli_fetch_assoc($category_query)['total_cat'];

// Current Monthly tracking metrics calculations
$current_month = date('m');
$current_year  = date('Y');

$monthly_complaints_query = mysqli_query($conn, "SELECT COUNT(*) as monthly_total FROM complaints WHERE MONTH(complaint_date) = '$current_month' AND YEAR(complaint_date) = '$current_year'");
$monthly_resolved_query   = mysqli_query($conn, "SELECT COUNT(*) as monthly_res FROM complaints WHERE status = 'Resolved' AND MONTH(complaint_date) = '$current_month' AND YEAR(complaint_date) = '$current_year'");

$monthly_total    = mysqli_fetch_assoc($monthly_complaints_query)['monthly_total'];
$monthly_resolved = mysqli_fetch_assoc($monthly_resolved_query)['monthly_res'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResolveX Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6fb;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: #002d72;
            color: white;
            display: flex;
            flex-direction: column;
            padding: 25px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo img {
            width: 70px;
        }

        .logo h2 {
            color: #1de9ff;
        }

        .logo p {
            font-size: 12px;
        }

        nav {
            margin-top: 40px;
        }

        nav a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            color: white;
            text-decoration: none;
            margin-bottom: 15px;
            border-radius: 12px;
        }

        nav a.active {
            background: #2563eb;
        }

        nav a:hover {
            background: #2563eb;
        }

        .logout {
            margin-top: auto;
        }

        .logout a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        /* MAIN */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h1 {
            color: #0f2354;
        }

        header p {
            color: #556;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .admin-avatar i {
            font-size: 24px;
            color: #1e3a8a;
        }

        .notification {
            position: relative;
            font-size: 24px;
        }

        .notification span {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            gap: 20px;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .card-icon {
            font-size: 45px;
            padding: 25px;
            border-radius: 15px;
        }

        .blue { background: #e7efff; color: #2563eb; }
        .purple { background: #efe7ff; color: #6a4cc2; }
        .green { background: #e8f8ed; color: #28a745; }

        /* MIDDLE */
        .middle-section {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 20px;
        }

        .overview, .chart-box {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .overview-item {
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }

        .chart-box {
            text-align: center;
        }

        .chart-box canvas {
            max-height: 250px;
            margin: 0 auto;
        }

        .chart-summary {
            margin-top: 20px;
            font-size: 18px;
        }

        /* TIP */
        .tip-box {
            margin-top: 25px;
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .tip-box i {
            font-size: 40px;
            color: #2563eb;
        }

        footer {
            text-align: center;
            padding: 25px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <div class="logo">
            <img src="robot.jpeg" alt="Robot Logo" class="robot">
            <div>
                <h2>ResolveX</h2>
                <p>Complaint Management</p>
            </div>
        </div>

        <nav>
            <a href="dashboard.php" class="active">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="complaints.php">
                 <i class="fa-solid fa-clipboard-list"></i> Complaints
            </a>
        </nav>

        <div class="logout">
              <a href="admin_logout.php">
                   <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
              </a>        
        </div>
    </aside>

    <main class="main-content">

        <header>
            <div>
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</p>
            </div>

            <div class="admin-info">
                <div class="notification">
                    <i class="fa-regular fa-bell"></i>
                    <span><?php echo $pending; ?></span>
                </div>

                <div class="admin-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

                <div>
                    <h3>Admin</h3>
                    <small>Administrator</small>
                </div>
            </div>
        </header>

        <section class="cards">
            <div class="card">
                <i class="fa-solid fa-clipboard-list card-icon blue"></i>
                <div>
                    <h3>Total Complaints</h3>
                    <h1><?php echo $total_complaints; ?></h1>
                    <p>All time complaints received</p>
                </div>
            </div>

            <div class="card">
                <i class="fa-solid fa-rotate card-icon purple"></i>
                <div>
                    <h3>In Progress</h3>
                    <h1><?php echo $in_progress; ?></h1>
                    <p>Currently being handled</p>
                </div>
            </div>

            <div class="card">
                <i class="fa-solid fa-circle-check card-icon green"></i>
                <div>
                    <h3>Resolved</h3>
                    <h1><?php echo $resolved; ?></h1>
                    <p>Successfully resolved</p>
                </div>
            </div>
        </section>

        <section class="middle-section">
            <div class="overview">
                <h2>System Overview</h2>
                <div class="overview-item">
                    <span>Total Users</span>
                    <strong><?php echo $total_users; ?></strong>
                </div>
                <div class="overview-item">
                    <span>Total Categories</span>
                    <strong><?php echo $total_categories; ?></strong>
                </div>
                <div class="overview-item">
                    <span>Complaints This Month</span>
                    <strong><?php echo $monthly_total; ?></strong>
                </div>
                <div class="overview-item">
                    <span>Resolved This Month</span>
                    <strong><?php echo $monthly_resolved; ?></strong>
                </div>
            </div>

            <div class="chart-box">
                <h2>Complaints By Status</h2>
                <canvas id="complaintChart"></canvas>
                <div class="chart-summary">
                    Total Complaints: <strong><?php echo $total_complaints; ?></strong>
                </div>
            </div>
        </section>

        <section class="tip-box">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <h3>Quick Tip</h3>
                <p>Click on "Complaints" in the menu to view and manage all complaints.</p>
            </div>
        </section>

        <footer>
            © <?php echo date("Y"); ?> ResolveX Complaint Management System.
        </footer>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.onload = function(){
    const ctx = document.getElementById('complaintChart');
    
    // Direct compilation injection from PHP counters array variables
    const pendingCount = <?php echo $pending; ?>;
    const progressCount = <?php echo $in_progress; ?>;
    const resolvedCount = <?php echo $resolved; ?>;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'In Progress', 'Resolved'],
            datasets: [{
                data: [pendingCount, progressCount, resolvedCount],
                backgroundColor: [
                    '#ffc107', // Gold yellow for Pending
                    '#6a4cc2', // Purple for In Progress
                    '#28a745'  // Emerald Green for Resolved
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
};
</script>
</body>
</html>