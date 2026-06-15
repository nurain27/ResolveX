<?php
// Include the database connection
require_once 'db_connect.php';

// 1. Fetch Statistics
$statsQuery = $pdo->query("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
$statusCounts = $statsQuery->fetchAll(PDO::FETCH_KEY_PAIR);

$totalComplaints = array_sum($statusCounts) ?: 0;
$inProgress = $statusCounts['In Progress'] ?? 0;
$resolved = $statusCounts['Resolved'] ?? 0;

// 2. Fetch Complaints Data
// Joining with the users table to get the complainant's name
$sql = "SELECT c.id, u.name AS complainant, c.infra_category AS category, 
               c.damage_type AS subject, c.complaint_date AS date, c.status 
        FROM complaints c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.complaint_date DESC";

$stmt = $pdo->query($sql);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to assign CSS classes based on values
function getCssClass($type, $value) {
    $value = strtolower(trim($value));
    if ($type === 'status') {
        if ($value === 'in progress') return 'progress';
        if ($value === 'resolved') return 'resolved';
        return 'high'; // Default for Pending or others
    }
    if ($type === 'category') {
        if (strpos($value, 'internet') !== false || strpos($value, 'wifi') !== false) return 'internet';
        if (strpos($value, 'hostel') !== false) return 'hostel';
        return 'low'; // Default generic badge
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ResolveX - Complaints</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* [KEEP ALL YOUR ORIGINAL CSS HERE EXACTLY AS IT WAS] */
        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
        body{ background:#f5f7fb; }
        .container{ display:flex; min-height:100vh; }
        .sidebar{ width:260px; background:#001f5c; color:white; display:flex; flex-direction:column; justify-content:space-between; }
        .logo{ padding:20px 15px; text-align:center; }
        .logo img{ width:210px; max-width:100%; object-fit:contain; }
        .sidebar ul{ list-style:none; }
        .sidebar ul li{ margin:15px; }
        .sidebar ul li a{ text-decoration:none; color:white; padding:15px; display:flex; gap:12px; border-radius:12px; }
        .sidebar ul li.active a{ background:linear-gradient(90deg,#2f67ff,#2950ff); }
        .logout{ margin:20px; }
        .logout a{ color:white; text-decoration:none; display:flex; gap:10px; }
        .main{ flex:1; padding:30px; }
        .header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
        .header h1{ color:#14213d; }
        .header p{ color:#666; margin-top:5px; }
        .admin-info{ display:flex; align-items:center; gap:15px; }
        .admin-info img{ width:45px; height:45px; border-radius:50%; }
        .cards{ display:flex; gap:20px; margin-bottom:25px; }
        .card{ background:white; flex:1; padding:25px; border-radius:15px; display:flex; gap:20px; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        .card i{ font-size:30px; }
        .card:nth-child(1) i{ color:#2563eb; }
        .card:nth-child(2) i{ color:#7c3aed; }
        .card:nth-child(3) i{ color:#16a34a; }
        .search-filter{ display:flex; justify-content:flex-end; gap:15px; margin-bottom:20px; }
        .search-filter input{ width:300px; padding:12px; border:1px solid #ddd; border-radius:10px; }
        .search-filter button{ padding:12px 20px; background:#fff; border:1px solid #ddd; border-radius:10px; cursor:pointer; }
        .table-container{ background:white; border-radius:15px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        table{ width:100%; border-collapse:collapse; }
        th{ background:#f8fafc; padding:18px; text-align:left; }
        td{ padding:18px; border-top:1px solid #eee; }
        .hostel{ background:#dbeafe; color:#2563eb; padding:5px 12px; border-radius:20px; }
        .internet{ background:#dcfce7; color:#16a34a; padding:5px 12px; border-radius:20px; }
        .progress{ background:#ede9fe; color:#7c3aed; padding:5px 12px; border-radius:20px; }
        .resolved{ background:#dcfce7; color:#16a34a; padding:5px 12px; border-radius:20px; }
        .high{ background:#fee2e2; color:#dc2626; padding:5px 12px; border-radius:20px; }
        .medium{ background:#ffedd5; color:#ea580c; padding:5px 12px; border-radius:20px; }
        .low{ background:#dbeafe; color:#2563eb; padding:5px 12px; border-radius:20px; }
        .view-btn{ border:none; padding:8px 16px; background:#f1f5f9; border-radius:8px; cursor:pointer; }
        .view-btn:hover{ background:#2563eb; color:white; }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <div class="logo"><img src="robot.jpeg" alt="Robot Logo" class="robot"></div>
        <ul>
            <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="active"><a href="#"><i class="fas fa-clipboard-list"></i> Complaints</a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
        </ul>
        <div class="logout"><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </div>

    <div class="main">
        <div class="header">
            <div>
                <h1>Complaints</h1>
                <p>View and manage all complaints submitted by users.</p>
            </div>
            <div class="admin-info">
                <i class="fas fa-bell notification"></i>
                <div class="admin-avatar"><i class="fas fa-user"></i></div>
                <div class="admin-text"><small>Administrator</small></div>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <i class="fas fa-clipboard-list"></i>
                <div>
                    <h4>Total Complaints</h4>
                    <h2><?= htmlspecialchars($totalComplaints) ?></h2>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-sync-alt"></i>
                <div>
                    <h4>In Progress</h4>
                    <h2><?= htmlspecialchars($inProgress) ?></h2>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-check-circle"></i>
                <div>
                    <h4>Resolved</h4>
                    <h2><?= htmlspecialchars($resolved) ?></h2>
                </div>
            </div>
        </div>

        <div class="search-filter">
            <input type="text" placeholder="Search complaints...">
            <button><i class="fas fa-filter"></i> Filter</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Complainant</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($complaints)): ?>
                        <tr><td colspan="7" style="text-align: center;">No complaints found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?= sprintf("CM-%05d", $complaint['id']) ?></td>
                            <td><?= htmlspecialchars($complaint['complainant']) ?></td>
                            <td>
                                <span class="<?= getCssClass('category', $complaint['category']) ?>">
                                    <?= htmlspecialchars($complaint['category'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($complaint['subject']) ?></td>
                            <td><?= date('d M Y', strtotime($complaint['date'])) ?></td>
                            <td>
                                <span class="<?= getCssClass('status', $complaint['status']) ?>">
                                    <?= htmlspecialchars($complaint['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="complaint_details.php?id=<?= urlencode($complaint['id']) ?>">
                                    <button class="view-btn">View</button>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>