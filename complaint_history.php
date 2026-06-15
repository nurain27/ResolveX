<?php
session_start();
include("db_connect.php");

// 1. Protection Guard: Redirect to login if user isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Fetch complaints only belonging to this specific user
$query = "SELECT * FROM complaints WHERE user_id = '$user_id' ORDER BY complaint_date DESC";
$result = mysqli_query($conn, $query);
$total_complaints = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResolveX - Complaint History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Poppins,sans-serif;
        }

        body{
            background:#005da8;
            overflow: hidden; /* Menghalang scrollbar berganda pada browser */
        }

        .container{
            display:flex;
            height:100vh;
            width: 100vw;
        }

        /* Sidebar - Diperbaiki supaya butang logout tidak tenggelam */
        .sidebar{
            width:250px;
            background:#003b8e;
            color:white;
            display:flex;
            flex-direction:column;
            height:100vh; 
            padding:20px;
            position: relative;
            flex-shrink: 0; /* Menghalang sidebar daripada mengecil */
        }

        .logo{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .logo img{
            width:70px;
            height:70px;
            object-fit:cover;
        }

        .logo h2{
            color:#1de9ff;
        }

        .logo p{
            font-size:12px;
            color:white;
        }
        .menu{
            margin-top:40px;
        }

        .menu a{
            color:white;
            text-decoration:none;
            display:flex;
            align-items:center;
            gap:10px;
            padding:15px;
            border-radius:12px;
            transition:0.3s;
        }

        .menu a:hover, .menu a.active{
            background:#2563eb;
        }

        /* Diperbaiki: Memastikan kedudukan butang logout sentiasa di bawah skrin */
        .logout{
            margin-top: auto; 
            padding-bottom: 10px;
            width: 100%;
        }

        .logout a{
            display:flex;
            align-items:center;
            gap:12px;
            color:white;
            text-decoration:none;
            padding:15px;
            border-radius:12px;
            background: rgba(255, 255, 255, 0.05); /* Memudahkan user nampak butang klik */
            transition: 0.3s;
        }

        .logout a:hover{
            background:#d9534f !important; 
        }

        /* Content */
        .content{
            flex:1;
            padding:20px;
            height: 100vh;
            overflow-y: auto; /* Hanya bahagian tabel sahaja yang akan ada scrollbar */
        }

        .page-title{
            background:white;
            width:300px;
            padding:12px;
            border-radius:5px;
            font-weight:500;
        }

        .complaint-card{
            margin-top:30px;
            background:white;
            border-radius:8px;
            overflow:hidden;
        }

        .card-header{
            background:#006fcb;
            color:white;
            padding:15px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .card-header input{
            width:250px;
            padding:10px;
            border:none;
            border-radius:5px;
        }

        .filter-bar{
            padding:10px;
            background:#f8f8f8;
            border-bottom:1px solid #ddd;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#fafafa;
            padding:15px;
            text-align:left;
        }

        td{
            padding:15px;
            border-top:1px solid #eee;
        }

        .resolved{
            color:green;
            font-weight:bold;
        }

        .pending{
            color:orange;
            font-weight:bold;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #777;
            font-style: italic;
        }

        .bottom-buttons{
            display:flex;
            justify-content:flex-end;
            gap:15px;
            margin-top:20px;
            padding-bottom: 20px;
        }

        .bottom-buttons button{
            width:90px;
            height:40px;
            border-radius:20px;
            border:1px solid white;
            background:transparent;
            color:white;
            cursor:pointer;
        }
        .bottom-buttons button:hover {
            background: white;
            color: #005da8;
        }
    </style>
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <div class="logo">
            <img src="./robot.jpeg" alt="Logo">
            <div class="logo-text">
                <h3>ResolveX</h3>
                <p>Track, Assign, Resolve</p>
            </div>
        </div>

        <div class="menu">
            <a href="complaint_history.php" class="active">
                <i class="fa-solid fa-house"></i>
                My Complaints
            </a>
            <a href="submitcomplaints.php">
                <i class="fa-solid fa-file-circle-plus"></i>
                Submit New Complaint
            </a>
        </div>

        <div class="logout">
            <a href="logout.php">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="content">

        <div class="page-title">
            <i class="fa-solid fa-briefcase"></i>
            Complaints History
        </div>

        <div class="complaint-card">

            <div class="card-header">
                <div>
                    <h3>Complaints (<?php echo $total_complaints; ?>)</h3>
                    <p>View list of complaints below</p>
                </div>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search Here"
                    onkeyup="searchComplaint()">
            </div>

            <div class="filter-bar">
                Filter Your Search
            </div>

            <table id="complaintTable">
                <thead>
                    <tr>
                        <th>Complaint No.</th>
                        <th>Date of Complaint</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                      <?php if($total_complaints > 0): ?>
                             <?php while($row = mysqli_fetch_assoc($result)): 
                                    $status_clean = strtolower($row['status']);
                                    $status_class = ($status_clean == 'resolved') ? 'resolved' : 'pending';
                                    ?>
                                    <tr>
                                      <td>#<?php echo $row['id']; ?></td>
                                      <td><?php echo date('Y-m-d', strtotime($row['complaint_date'])); ?></td>
                                      <td><?php echo htmlspecialchars($row['damage_type']); ?></td>
                                      <td><span class="<?php echo $status_class; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    </tr>
                             <?php endwhile; ?>
                      <?php else: ?>
                        <tr>
                            <td colspan="4" class="no-data">You haven't submitted any complaints yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>

        <div class="bottom-buttons">
            <button type="button" onclick="goHome()">Home</button>
            <button type="button" onclick="history.back()">Back</button>
        </div>

    </main>

</div>

<script>
function searchComplaint(){
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("complaintTable");
    let tr = table.getElementsByTagName("tr");

    for(let i=1; i<tr.length; i++){
        let td = tr[i].getElementsByTagName("td")[2]; 
        if(td){
            let txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

function goHome(){
    window.location.href="index.php";
}
</script>
</body>
</html>