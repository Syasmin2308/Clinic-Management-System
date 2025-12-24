<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO Treatments (treatmentID, appointmentID, treatmentType, treatmentName, description, cost, status, medDosage, medFrequency, medDuration, medQuantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssssssssss",
        $_POST['treatmentID'],
        $_POST['appointmentID'],
        $_POST['treatmentType'],
        $_POST['treatmentName'],
        $_POST['description'],
        $_POST['cost'],
        $_POST['status'],
        $_POST['medDosage'],
        $_POST['medFrequency'],
        $_POST['medDuration'],
        $_POST['medQuantity']
    );

    $result = $conn->query("SELECT treatmentID FROM Treatments ORDER BY treatmentID DESC LIMIT 1");
$nextID = "T01"; // default

if ($result && $row = $result->fetch_assoc()) {
    $lastID = $row['treatmentID'];
    $num = intval(substr($lastID, 1)) + 1; // remove 'T' and convert to number
    $nextID = "T" . str_pad($num, 4, "0", STR_PAD_LEFT); // pad with 0s
}

    if ($stmt->execute()) {
        echo "<script>alert('Treatment data saved successfully!'); window.location.href='treatmentsView.php';</script>";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Treatment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script>
    // Treatment data: names and default cost per type
    const treatmentOptions = {
      "General Medicine": {
        names: ["Paracetemol", "Antibiotics", "Pain Relief",],
        cost: 30
      },
      "Minor Procedure": {
        names: ["Stitches","Wound Care"],
        cost: 60
      },
      "Preventative Care": {
        names: ["Vaccinations", "Health Screening", "Flu Shot"],
        cost: 80
      },
      "Lab Test": {
        names: ["Blood Test", "Urine Test"],
        cost: 100
      }
    };

function updateTreatmentNames() {
    const type = document.getElementById("treatmentType").value;
    const nameDropdown = document.getElementById("treatmentName");
    const costInput = document.getElementById("cost");

    // Reset name dropdown
    nameDropdown.innerHTML = '<option value="">-- Select Name --</option>';
    nameDropdown.disabled = !type;

    // Update if type is selected
    if (type && treatmentOptions[type]) {
      // Populate treatment names
      treatmentOptions[type].names.forEach(name => {
        const option = document.createElement("option");
        option.value = name;
        option.textContent = name;
        nameDropdown.appendChild(option);
      });
      
      // Set default cost
      costInput.value = treatmentOptions[type].cost;
    } else {
      costInput.value = "";
    }
  }

  // Optional: Update cost if name is changed (for custom pricing)
  document.getElementById("treatmentName").addEventListener("change", function() {
    // You could add custom pricing logic here if needed
    // For example, certain treatment names might have different costs
  });
</script>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    .wrapper {
      display: flex;
      min-height: 100vh;
      overflow-x: hidden;
    }

    .sidebar {
      min-width: 220px;
      background-color: #2e7d6b;
      color: white;
      padding: 2rem 1rem;
      box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
    }

    .sidebar h4 {
      text-align: center;
      margin-bottom: 2rem;
      font-weight: bold;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      color: white;
      text-decoration: none;
      padding: 10px 15px;
      margin-bottom: 10px;
      border-radius: 6px;
      transition: background-color 0.2s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #1c5b4a;
    }

    .sidebar i {
      font-size: 18px;
    }

    .sidebar a.mt-auto {
  margin-top: auto;
}

    .main-content {
      flex: 1;
      padding: 2rem;
      background-color: rgba(212, 241, 228, 1);
    }

    .table th, .table td {
      font-size: 14px;
      vertical-align: middle;
      white-space: nowrap;
    }

    .table-responsive {
      margin-top: 20px;
    }

    @media (max-width: 768px) {
      .wrapper {
        flex-direction: column;
      }
      .sidebar {
        width: 100%;
        position: static;
        text-align: center;
      }
      .main-content {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Sidebar -->
  <div class="sidebar">
    <h4><i class="bi bi-hospital"></i> UK Admin</h4>
    <a href="home.php"><i class="bi bi-house-door-fill"></i> Home</a>
    <a href="patientsView.php"><i class="bi bi-person-lines-fill"></i> Patients</a>
    <a href="staffsView.php"><i class="bi bi-people-fill"></i> Staffs</a>
    <a href="treatmentsView.php" class="active"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
<!-- Main Content -->
<div class="main-content">
  <h2>Add Treatment</h2>
  <form action="treatmentsAdd.php" method="POST" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Treatment ID</label>
    <input type="text" name="treatmentID" class="form-control" value="<?= $nextID ?>" readonly>
    </div>
    <div class="col-md-6">
      <label class="form-label">Appointment ID</label>
      <input type="text" name="appointmentID" class="form-control" required>
    </div>
    <div class="col-md-6">
  <label class="form-label">Treatment Type</label>
    <select name="treatmentType" id="treatmentType" class="form-select" required onchange="updateTreatmentNames()">
      <option value="">-- Select Type --</option>
      <option value="General Medicine">General Medicine</option>
      <option value="Minor Procedure">Minor Procedure</option>
      <option value="Preventative Care">Preventative Care</option>
      <option value="Lab Test">Lab Test</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label">Treatment Name</label>
    <select name="treatmentName" id="treatmentName" class="form-select" required disabled>
      <option value="">-- Select Type First --</option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label">Cost (RM)</label>
    <input type="number" name="cost" id="cost" class="form-control" required readonly>
  </div>
    <div class="col-md-6">
      <label class="form-label">Description</label>
      <input name="description" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
        <option value="Cancelled">Cancelled</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label">Medicine Dosage</label>
      <input name="medDosage" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Medicine Frequency</label>
      <input name="medFrequency" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Medicine Duration</label>
      <input name="medDuration" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Medicine Quantity</label>
      <input name="medQuantity" class="form-control" required>
    </div>
    <div class="col-12 text-end">
      <a href="treatmentsView.php" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Treatment</button>
    </div>
  </form>
</div>
</body>
</html>
