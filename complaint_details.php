<?php
session_start();
include("db_connect.php");

/* =====================================
   SESSION SECURITY
===================================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

/* =====================================
   GET COMPLAINT ID
===================================== */
if (!isset($_GET['id'])) {
    header("Location: complaints.php");
    exit();
}

$complaint_id = intval($_GET['id']);

/* =====================================
   UPDATE COMPLAINT (POST ACTION)
===================================== */
$message = "";
$message_type = "";

if (isset($_POST['update_complaint'])) {
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $assigned_staff_id = intval($_POST['assigned_staff_id']);
    $admin_response = mysqli_real_escape_string($conn, $_POST['admin_response']);

    // Menggunakan Prepared Statement untuk keselamatan optimum
    $update_stmt = mysqli_prepare($conn, "UPDATE complaints SET status = ?, assigned_staff_id = ?, admin_response = ?, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($update_stmt, "sisi", $status, $assigned_staff_id, $admin_response, $complaint_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $message = "Complaint updated successfully!";
        $message_type = "success";
    } else {
        $message = "Failed to update complaint.";
        $message_type = "error";
    }
    mysqli_stmt_close($update_stmt);
}

/* =====================================
   FETCH STAFF FOR DROPDOWN
===================================== */
$staff_query = "SELECT * FROM staff ORDER BY staff_name ASC";
$staff_result = mysqli_query($conn, $staff_query);

/* =====================================
   FETCH COMPLAINT DETAILS
===================================== */
$query = "
    SELECT complaints.*, users.name, users.email, staff.staff_name, staff.position 
    FROM complaints 
    INNER JOIN users ON complaints.user_id = users.id 
    LEFT JOIN staff ON complaints.assigned_staff_id = staff.staff_id 
    WHERE complaints.id = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $complaint_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Complaint not found.");
}

$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

/* =====================================
   GENERATE LOCATION DISPLAY
===================================== */
$location_display = "";
if ($data['area'] == "inside") {
    $location_display = $data['building'] . " - " . $data['block'] . ", " . $data['location'];
} else {
    $location_display = $data['infra_category'] . " - " . $data['infra_subcategory'];
}

/* =====================================
   ATTACHMENT FILE LOGIC
===================================== */
$attachment_path = "";
if (!empty($data['evidence_file'])) {
    $attachment_path = "uploads/" . $data['evidence_file'];
}

$image_extensions = ["jpg", "jpeg", "png"];
$is_image = false;

if (!empty($attachment_path)) {
    $file_extension = strtolower(pathinfo($attachment_path, PATHINFO_EXTENSION));
    if (in_array($file_extension, $image_extensions)) {
        $is_image = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResolveX - Complaint Details</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* =====================================
           GENERAL & LAYOUT
        ===================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f5f7fb;
            color: #1e293b;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }

        /* =====================================
           SIDEBAR
        ===================================== */
        .sidebar {
            width: 260px;
            background: #002d72;
            color: white;
            padding: 25px;
            display: flex;
            flex-direction: column;
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
            color: white;
            text-decoration: none;
            padding: 18px;
            margin-bottom: 15px;
            border-radius: 12px;
            transition: .3s;
        }
        nav a:hover, nav a.active {
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

        /* =====================================
           MAIN CONTENT & HEADER
        ===================================== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: white;
            padding: 25px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            color: #0f2354;
        }
        .header p {
            color: #666;
            margin-top: 5px;
        }
        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .notification {
            position: relative;
            font-size: 22px;
        }
        .notification span {
            position: absolute;
            top: -10px;
            right: -10px;
            background: red;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 11px;
        }
        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .avatar i {
            font-size: 20px;
            color: #1e3a8a;
        }
        .content {
            padding: 30px;
        }

        /* =====================================
           CARDS & COMPONENTS
        ===================================== */
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            margin-bottom: 25px;
        }
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .success {
            background: #dcfce7;
            color: #166534;
        }
        .error {
            background: #fee2e2;
            color: #991b1b;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #ddd;
            background: white;
            padding: 12px 18px;
            border-radius: 10px;
            text-decoration: none;
            color: #0f2354;
            margin-bottom: 20px;
            transition: 0.2s;
        }
        .back-btn:hover {
            background: #f3f4f6;
        }
        .info-row {
            display: flex;
            margin-bottom: 18px;
        }
        .label {
            width: 180px;
            font-weight: 600;
        }
        .value {
            color: #333;
        }
        .description p {
            margin-top: 10px;
            line-height: 1.7;
            color: #555;
        }
        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #eee;
        }

        /* ATTACHMENT */
        .attachment img {
            width: 180px;
            margin-top: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }
        .file-icon {
            font-size: 55px;
            color: #2563eb;
            margin-top: 15px;
        }
        .view-file {
            display: inline-block;
            margin-top: 15px;
            background: #2563eb;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s;
        }
        .view-file:hover {
            background: #1d4ed8;
        }

        /* =====================================
           ADMIN ACTION SECTION
        ===================================== */
        .action-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
        }
        .status-select {
            width: 100%;
            padding: 14px;
            border: 2px solid #8b5cf6;
            border-radius: 10px;
            font-size: 15px;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .status-note {
            background: #eef2ff;
            color: #4338ca;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            gap: 10px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .response-box {
            width: 100%;
            height: 140px;
            margin-top: 10px;
            padding: 15px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            resize: none;
        }
        .update-details {
            margin-top: 20px;
            color: #555;
            line-height: 1.8;
        }
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        .cancel-btn, .update-btn {
            flex: 1;
            padding: 14px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 15px;
            transition: 0.2s;
        }
        .cancel-btn {
            background: white;
            border: 1px solid #ddd;
        }
        .cancel-btn:hover {
            background: #f3f4f6;
        }
        .update-btn {
            background: #16a34a;
            color: white;
            border: none;
        }
        .update-btn:hover {
            background: #15803d;
        }

        /* =====================================
           ACTIVITY HISTORY
        ===================================== */
        .activity-card {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            padding: 25px;
            margin-top: 25px;
        }
        .activity-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #166534;
            margin-bottom: 25px;
        }
        .timeline-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }
        .green-dot { background: #22c55e; }
        .blue-dot { background: #2563eb; }
        .timeline-content h4 {
            color: #334155;
            margin-bottom: 5px;
        }
        .timeline-content p {
            color: #475569;
            line-height: 1.7;
        }

        /* =====================================
           RESPONSIVE DESIGN
        ===================================== */
        @media(max-width: 900px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            .button-group {
                flex-direction: column;
            }
            .info-row {
                flex-direction: column;
                gap: 8px;
            }
            .label {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <div class="logo">
            <img src="robot.jpeg" alt="ResolveX">
            <div>
                <h2>ResolveX</h2>
                <p>Complaint Management</p>
            </div>
        </div>
        <nav>
            <a href="dashboard.php">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
            <a href="complaints.php" class="active">
                <i class="fa-solid fa-clipboard-list"></i> Complaints
            </a>
            </nav>
        <div class="logout">
            <a href="logout.php">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <div class="main-content">
        
        <div class="header">
            <div>
                <h1>Complaint Details</h1>
                <p>Dashboard <i class="fa-solid fa-angle-right"></i> Complaints <i class="fa-solid fa-angle-right"></i> Complaint Details</p>
            </div>
            <div class="admin-info">
                <div class="notification">
                    <i class="fa-regular fa-bell"></i>
                    <span>1</span>
                </div>
                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <h3>Admin</h3>
                    <small>Administrator</small>
                </div>
            </div>
        </div>

        <div class="content">

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <a href="complaints.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i> Back to Complaints
            </a>

            <div class="card">
                <h2><i class="fa-solid fa-file-lines"></i> Complaint Information</h2>
                <br>

                <div class="info-row">
                    <div class="label">Complaint ID</div>
                    <div class="value">CM-<?php echo str_pad($data['id'], 5, "0", STR_PAD_LEFT); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Complainant</div>
                    <div class="value"><?php echo htmlspecialchars($data['name']); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Email</div>
                    <div class="value"><?php echo htmlspecialchars($data['email']); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Category</div>
                    <div class="value"><?php echo ucfirst(htmlspecialchars($data['area'])); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Subject</div>
                    <div class="value"><?php echo htmlspecialchars($data['damage_type']); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Assigned Staff</div>
                    <div class="value"><?php echo !empty($data['staff_name']) ? htmlspecialchars($data['staff_name']) : "Not assigned yet"; ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Staff Position</div>
                    <div class="value"><?php echo !empty($data['position']) ? htmlspecialchars($data['position']) : "-"; ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Date Submitted</div>
                    <div class="value"><?php echo date("d M Y, h:i A", strtotime($data['complaint_date'])); ?></div>
                </div>

                <div class="info-row">
                    <div class="label">Location</div>
                    <div class="value"><?php echo htmlspecialchars($location_display); ?></div>
                </div>

                <hr>

                <div class="description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($data['description'])); ?></p>
                </div>

                <hr>

                <div class="attachment">
                    <h3>Attachment</h3>
                    <?php if (!empty($attachment_path)): ?>
                        <?php if ($is_image): ?>
                            <img src="<?php echo htmlspecialchars($attachment_path); ?>" alt="Complaint Evidence">
                        <?php else: ?>
                            <div class="file-icon"><i class="fa-solid fa-file"></i></div>
                        <?php endif; ?>
                        <br>
                        <a href="<?php echo htmlspecialchars($attachment_path); ?>" target="_blank" class="view-file">View Attachment</a>
                    <?php else: ?>
                        <p style="margin-top:10px; color:#666;">No attachment uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="action-section">
                <div class="card">
                    <h2><i class="fa-solid fa-clipboard-check"></i> Update Complaint</h2>
                    
                    <form method="POST" id="updateForm">
                        <label style="font-weight:600;">Current Status</label>
                        <select name="status" class="status-select">
                            <option value="Pending" <?php if($data['status']=="Pending") echo "selected"; ?>>Pending</option>
                            <option value="In Progress" <?php if($data['status']=="In Progress") echo "selected"; ?>>In Progress</option>
                            <option value="Resolved" <?php if($data['status']=="Resolved") echo "selected"; ?>>Resolved</option>
                        </select>

                        <label style="font-weight:600;">Assign Staff</label>
                        <select name="assigned_staff_id" class="status-select">
                            <option value="">-- Select Staff --</option>
                            <?php while($staff = mysqli_fetch_assoc($staff_result)): ?>
                                <option value="<?php echo $staff['staff_id']; ?>" <?php if($data['assigned_staff_id'] == $staff['staff_id']) echo "selected"; ?>>
                                    <?php echo htmlspecialchars($staff['staff_name']) . " - " . htmlspecialchars($staff['position']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>

                        <div class="status-note">
                            <i class="fa-solid fa-circle-info"></i>
                            <p>Once marked as <b>Resolved</b>, the complaint will be considered completed.</p>
                        </div>

                        <h3 style="margin-bottom:10px;">Admin Response</h3>
                        <textarea name="admin_response" class="response-box" placeholder="Enter response for complainant..."><?php echo htmlspecialchars($data['admin_response'] ?? ""); ?></textarea>

                        <div class="update-details">
                            <p><strong>Last Updated By:</strong> Admin</p>
                            <p><strong>Last Updated On:</strong> 
                                <?php echo !empty($data['updated_at']) ? date("d M Y, h:i A", strtotime($data['updated_at'])) : "No updates yet"; ?>
                            </p>
                        </div>

                        <div class="button-group">
                            <button type="button" class="cancel-btn" onclick="history.back()">Cancel</button>
                            <button type="submit" name="update_complaint" class="update-btn">Update Complaint</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="activity-card">
                <div class="activity-title">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <h2>Activity History</h2>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot green-dot"></div>
                    <div class="timeline-content">
                        <h4><?php echo date("d M Y, h:i A", strtotime($data['complaint_date'])); ?></h4>
                        <p>Complaint submitted by <strong><?php echo htmlspecialchars($data['name']); ?></strong></p>
                    </div>
                </div>

                <?php if (!empty($data['updated_at']) || !empty($data['admin_response']) || !empty($data['assigned_staff_id'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot blue-dot"></div>
                        <div class="timeline-content">
                            <h4><?php echo !empty($data['updated_at']) ? date("d M Y, h:i A", strtotime($data['updated_at'])) : "No update date"; ?></h4>
                            <p>
                                Status changed to <strong><?php echo htmlspecialchars($data['status']); ?></strong><br><br>
                                Assigned Staff: <strong><?php echo !empty($data['staff_name']) ? htmlspecialchars($data['staff_name'])." (".htmlspecialchars($data['position']).")" : "No staff assigned"; ?></strong><br><br>
                                Admin Response:<br>
                                <?php echo !empty($data['admin_response']) ? nl2br(htmlspecialchars($data['admin_response'])) : "No response provided"; ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div> </div> </div> <script>
    document.getElementById("updateForm").addEventListener("submit", function(e){
        let confirmUpdate = confirm("Are you sure you want to update this complaint?");
        if(!confirmUpdate) {
            e.preventDefault();
        }
    });
</script>
</body>
</html>