<?php
session_start();
include("db_connect.php");

// Session Protection Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$status_type = "";

// Intercept form post requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $block = mysqli_real_escape_string($conn, $_POST['block']);
    $damage_type = mysqli_real_escape_string($conn, $_POST['damage_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Conditional values based on selected area
    $level = ($area === 'inside') ? mysqli_real_escape_string($conn, $_POST['level']) : null;
    $location = ($area === 'inside') ? mysqli_real_escape_string($conn, $_POST['location']) : null;
    $location_description = ($area === 'inside') ? mysqli_real_escape_string($conn, $_POST['location_description']) : null;
    
    $infra_category = ($area === 'outside') ? mysqli_real_escape_string($conn, $_POST['infra_category']) : null;
    $infra_subcategory = ($area === 'outside') ? mysqli_real_escape_string($conn, $_POST['infra_subcategory']) : null;

    // Secure File Upload Engine
    $evidence_filename = null;
    if (isset($_FILES['evidence']) && $_FILES['evidence']['error'] == 0) {
        $target_dir = "uploads/";
        
        // Build folder dynamically if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = basename($_FILES["evidence"]["name"]);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Define clean system extensions restrictions
        $allowed_extensions = array("jpg", "jpeg", "png", "pdf", "docx");

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate non-colliding randomized file name
            $evidence_filename = uniqid("REV_", true) . "." . $file_ext;
            $target_file = $target_dir . $evidence_filename;

            if (!move_uploaded_file($_FILES["evidence"]["tmp_name"], $target_file)) {
                $message = "Failed to upload file evidence.";
                $status_type = "error";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, PDF, & DOCX allowed.";
            $status_type = "error";
        }
    }

    // Insert statement execution path if no errors found
    if ($status_type !== "error") {
        $query = "INSERT INTO complaints (user_id, area, building, block, level, location, location_description, infra_category, infra_subcategory, damage_type, description, evidence_file) 
                  VALUES ('$user_id', '$area', '$building', '$block', " . 
                  ($level ? "'$level'" : "NULL") . ", " . 
                  ($location ? "'$location'" : "NULL") . ", " . 
                  ($location_description ? "'$location_description'" : "NULL") . ", " . 
                  ($infra_category ? "'$infra_category'" : "NULL") . ", " . 
                  ($infra_subcategory ? "'$infra_subcategory'" : "NULL") . ", " . 
                  "'$damage_type', '$description', " . 
                  ($evidence_filename ? "'$evidence_filename'" : "NULL") . ")";

        if (mysqli_query($conn, $query)) {
            // Success redirection directly to history page
            header("Location: complaint_history.php");
            exit();
        } else {
            $message = "Database System Error: " . mysqli_error($conn);
            $status_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #0B5AA2;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .container {
            background: white;
            width: 100%;
            max-width: 1000px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            color: #1e293b;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #1e293b;
        }

        .radio-group {
            display: flex;
            gap: 30px;
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            flex: 1;
            margin-bottom: 20px;
        }

        input, select, textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #f8fafc;
        }

        textarea {
            min-height: 120px;
            resize: none;
        }

        .alert-box {
            padding: 15px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .actions-div {
            display: flex;
            gap: 15px;
        }

        button {
            flex: 2;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #1d4ed8;
        }

        .back-btn {
            flex: 1;
            background: #64748b;
        }
        .back-btn:hover {
            background: #475569;
        }

        @media (max-width: 768px) {
            .row {
                flex-direction: column;
            }
            .actions-div {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <h1>Submit Complaint</h1>
    <p class="subtitle">Report issues quickly and efficiently</p>

    <?php if (!empty($message)): ?>
        <div class="alert-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <div class="section">
            <label>Complaint Area</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="area" value="inside" checked> Inside Building
                </label>
                <label>
                    <input type="radio" name="area" value="outside"> Outside Building
                </label>
            </div>
        </div>

        <div class="row">
            <div class="input-group">
                <label for="buildingSelect">Building</label>
                <select id="buildingSelect" name="building" required>
                    <option value="">Select Building</option>
                    <option value="D0101">D0101 - BANGUNAN PENTADBIRAN</option>
                    <option value="D0102">D0102 - DEWAN PROFESIONAL</option>
                    <option value="D0103">D0103 - Kolej Dato Onn</option>
                    <option value="D0104">D0104 - KOMPLEKS SUKAN A</option>
                </select>
            </div>
            <div class="input-group">
                <label for="blockSelect">Block</label>
                <select id="blockSelect" name="block" disabled required>
                    <option value="">Select Block</option>
                </select>
            </div>
        </div>

        <div id="insideFields">
            <div class="row">
                <div class="input-group">
                    <label for="levelSelect">Level</label>
                    <select id="levelSelect" name="level" disabled>
                        <option value="">Select Level</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="locationSelect">Location</label>
                    <select id="locationSelect" name="location" disabled>
                        <option value="">Select Location</option>
                    </select>
                </div>
            </div>
            <div class="input-group">
                <label for="locDesc">Description of Location</label>
                <textarea id="locDesc" name="location_description" placeholder="Provide more details about the location"></textarea>
            </div>
        </div>

        <div id="outsideFields">
            <div class="row">
                <div class="input-group">
                    <label for="infraCategorySelect">Infrastructure Category</label>
                    <select id="infraCategorySelect" name="infra_category" disabled>
                        <option value="">Select Category</option>
                        <option value="Kafeteria">Kafeteria Outside</option>
                        <option value="Rumah Sampah">Rumah Sampah Outside</option>
                        <option value="Jalan Raya">Jalan Raya / Tar</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="infraSubcategorySelect">Infrastructure Subcategory</label>
                    <select id="infraSubcategorySelect" name="infra_subcategory" disabled>
                        <option value="">Select Subcategory</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="section">
            <label for="damageType">Type of Damage</label>
            <select id="damageType" name="damage_type" required>
                <option value="">Select Type of Damage</option>
            </select>
        </div>

        <div class="input-group">
            <label for="complaintDesc">Complaint Description</label>
            <textarea id="complaintDesc" name="description" placeholder="Describe the issue" required></textarea>
        </div>

        <div class="input-group">
            <label for="evidenceFile">Upload Evidence</label>
            <input type="file" id="evidenceFile" name="evidence">
        </div>

        <div class="actions-div">
            <button type="button" class="back-btn" onclick="window.location.href='complaint_history.php'">Cancel</button>
            <button type="submit">Submit Complaint</button>
        </div>

    </form>
</div>

<script>
// Keep your existing cascading dropdown mechanics exactly as they were
const areaOptions = document.querySelectorAll('input[name="area"]');
const insideFields = document.getElementById('insideFields');
const outsideFields = document.getElementById('outsideFields');

const buildingSelect = document.getElementById('buildingSelect');
const blockSelect = document.getElementById('blockSelect');
const levelSelect = document.getElementById('levelSelect');
const locationSelect = document.getElementById('locationSelect');
const infraCategorySelect = document.getElementById('infraCategorySelect');
const infraSubcategorySelect = document.getElementById('infraSubcategorySelect');

areaOptions.forEach(option => {
    option.addEventListener('change', () => {
        buildingSelect.value = "";
        
        blockSelect.innerHTML = '<option value="">Select Block</option>';
        levelSelect.innerHTML = '<option value="">Select Level</option>';
        locationSelect.innerHTML = '<option value="">Select Location</option>';
        infraCategorySelect.innerHTML = '<option value="">Select Category</option>';
        infraSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

        blockSelect.disabled = true;
        levelSelect.disabled = true;
        locationSelect.disabled = true;
        infraCategorySelect.disabled = true; 
        infraSubcategorySelect.disabled = true; 

        if (option.value === "inside" && option.checked) {
            insideFields.style.display = "block";
            if(outsideFields) outsideFields.style.display = "none";
        } 
        else if (option.value === "outside" && option.checked) {
            insideFields.style.display = "none";
            if(outsideFields) outsideFields.style.display = "block";
        }
    });
});

const damageTypes = [
    "Electrical Damage", "Plumbing Damage", "Furniture Damage", "Door & Window Damage",
    "Air Conditioning Damage", "Internet / Network Issue", "Building Structure Damage",
    "Cleanliness Issue", "Water Supply Issue", "Safety Hazard", "Equipment Damage"
];

const damageSelect = document.getElementById('damageType');
if (damageSelect) {
    damageSelect.innerHTML = '<option value="">Select Type of Damage</option>';
    damageTypes.forEach(type => {
        let opt = document.createElement('option');
        opt.value = type;
        opt.textContent = type;
        damageSelect.appendChild(opt);
    });
}

const dataMaster = {
    "buildings": {
        "D0101": {
            "blocks": {
                "D0101A": { "nama": "D0101A - KAFETERIA PENTADBIRAN", "levels": { "1": ["Bilik Penyediaan Makanan", "Dapur", "Kaunter", "Koridor Awam 1", "Koridor Awam 2"] } },
                "D0101B": { "nama": "D0101B - RUMAH SAMPAH PENTADBIRAN 1", "levels": { "1": ["RUMAH SAMPAH PENTADBIRAN 1"] } },
                "D0101C": { "nama": "D0101C - PERHENTIAN BAS PENTADBIRAN", "levels": { "1": ["PERHENTIAN BAS PENTADBIRAN"] } }
            }
        },
        "D0102": {
            "blocks": {
                "D0102A": { "nama": "D0102A - DEWAN PROFESIONAL", "levels": { "B2": ["Bengkel Am", "Bilik Juruteknik", "HR", "Koridor Awam", "Stor", "Surau", "Tandas"], "B1": ["Stor Bahagian Kewangan"], "1": ["Bilik Siti Wan Kembang", "Bilik Puteri Saadong", "Bilik Sri Tanjong", "Gelanggang Ping Pong", "Gelanggang Skuasy"], "2": ["Koridor Awam", "Tandas (P)", "Tandas (L)", "Bilik Sri Teratai"] } },
                "D0102B": { "nama": "D0102B - RUMAH SAMPAH DEWAN PRO", "levels": { "1": ["Ante-room", "Stor", "Tandas"] } }
            }
        },
        "D0103": {
            "blocks": {
                "D0103A": { "nama": "D0103A - Kolej Dato Onn 1", "levels": { "1": ["Bilik Pelawat", "Bilik TV", "Koridor Awam", "Stor", "Tandas"], "2": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "3": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "4": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "5": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"] } },
                "D0103B": { "nama": "D0103B - Kolej Dato Onn 2", "levels": { "1": ["Bilik Pelawat", "Bilik TV", "Koridor Awam", "Stor", "Tandas"], "2": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "3": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "4": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"], "5": ["Bilik Air", "Bilik Sinki", "Bilik Mandi Semburan", "Bilik Tidur Pelajar", "HR", "Koridor Awam", "Utiliti"] } },
                "D0103C": { "nama": "D0103C - DEWAN MAKAN DATO ONN", "levels": { "1": ["Bilik Pengurus", "Bilik Rehat (P)", "Bilik Rehat (L)", "Foyer 1", "Foyer 2", "Kaunter Makanan", "Tandas (P)", "Tandas (L)", "Koridor Awam"] } }
            }
        },
        "D0104": {
            "blocks": {
                "D0104A": { "nama": "D0104A - GELANGGANG KOMPLEKS SUKAN A", "levels": { "1": ["Bilik Pegawai", "Bilik Utiliti 1", "Bilik Utiliti 2", "Dewan Serbaguna", "Koridor Awam", "Tandas (P)", "Tandas (L)"] } },
                "D0104B": { "nama": "D0104B - RUMAH PERSALINAN 1", "levels": { "1": ["Ante-room", "Stor", "Tandas (P)", "Tandas (L)"] } }
            }
        }
    },
    "outside": {
        "categories": ["Lanskap", "Longkang", "Pagar", "Parkir"],
        "subcategories": {
            "Lanskap": ["Hardscape", "Kawasan Berumput", "Kolam", "Pokok Renek", "Waterscape"],
            "Longkang": ["Culvert", "Roadside Drainage", "Subsurface Drainage", "Sump", "Surface Drainage"],
            "Pagar": ["Pagar", "Pagar Utama"],
            "Parkir": ["Bas", "Kenderaan Berat", "Kereta", "Lori", "Motorsikal"]
        }
    }
};

buildingSelect.addEventListener('change', function() {
    const buildingId = this.value;
    blockSelect.innerHTML = '<option value="">Select Block</option>';
    levelSelect.innerHTML = '<option value="">Select Level</option>';
    locationSelect.innerHTML = '<option value="">Select Location</option>';
    infraCategorySelect.innerHTML = '<option value="">Select Category</option>';
    infraSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
    
    blockSelect.disabled = true;
    levelSelect.disabled = true;
    locationSelect.disabled = true;
    infraCategorySelect.disabled = true;
    infraSubcategorySelect.disabled = true;

    if (buildingId && dataMaster.buildings[buildingId]) {
        const blocks = dataMaster.buildings[buildingId].blocks;
        for (let key in blocks) {
            let opt = document.createElement('option');
            opt.value = key;
            opt.textContent = blocks[key].nama;
            blockSelect.appendChild(opt);
        }
        blockSelect.disabled = false;
    }
});

blockSelect.addEventListener('change', function() {
    const buildingId = buildingSelect.value;
    const blockId = this.value;
    const currentArea = document.querySelector('input[name="area"]:checked').value;

    levelSelect.innerHTML = '<option value="">Select Level</option>';
    locationSelect.innerHTML = '<option value="">Select Location</option>';
    infraCategorySelect.innerHTML = '<option value="">Select Category</option>';
    infraSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
    
    levelSelect.disabled = true;
    locationSelect.disabled = true;
    infraCategorySelect.disabled = true;
    infraSubcategorySelect.disabled = true;

    if (blockId && dataMaster.buildings[buildingId] && dataMaster.buildings[buildingId].blocks[blockId]) {
        const blockData = dataMaster.buildings[buildingId].blocks[blockId];
        
        if (currentArea === "inside") {
            const levels = blockData.levels;
            for (let key in levels) {
                let opt = document.createElement('option');
                opt.value = key;
                opt.textContent = `Level ${key}`;
                levelSelect.appendChild(opt);
            }
            levelSelect.disabled = false;
        } 
        else if (currentArea === "outside") {
            dataMaster.outside.categories.forEach(cat => {
                let opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                infraCategorySelect.appendChild(opt);
            });
            infraCategorySelect.disabled = false;
        }
    }
});

levelSelect.addEventListener('change', function() {
    const buildingId = buildingSelect.value;
    const blockId = blockSelect.value;
    const levelId = this.value;

    locationSelect.innerHTML = '<option value="">Select Location</option>';
    locationSelect.disabled = true;

    if (levelId && dataMaster.buildings[buildingId].blocks[blockId].levels[levelId]) {
        const locations = dataMaster.buildings[buildingId].blocks[blockId].levels[levelId];
        locations.forEach(loc => {
            let opt = document.createElement('option');
            opt.value = loc;
            opt.textContent = loc;
            locationSelect.appendChild(opt);
        });
        locationSelect.disabled = false;
    }
});

if (infraCategorySelect && infraSubcategorySelect) {
    infraCategorySelect.addEventListener('change', function() {
        const catId = this.value;
        infraSubcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        infraSubcategorySelect.disabled = true;

        if (catId && dataMaster.outside.subcategories[catId]) {
            const subCategories = dataMaster.outside.subcategories[catId];
            subCategories.forEach(sub => {
                let opt = document.createElement('option');
                opt.value = sub;
                opt.textContent = sub;
                infraSubcategorySelect.appendChild(opt);
            });
            infraSubcategorySelect.disabled = false;
        }
    });
}
</script>
</body>
</html>