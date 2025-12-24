<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$nextAppointmentID = "A0001"; // Default starting ID
$error = '';

// Database connection
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate next Appointment ID
$result = $conn->query("SELECT AppointmentID FROM Appointments ORDER BY AppointmentID DESC LIMIT 1");
if ($result && $row = $result->fetch_assoc()) {
    $lastID = $row['AppointmentID'];
    $num = intval(substr($lastID, 1)) + 1;
    $nextAppointmentID = "A" . str_pad($num, 4, "0", STR_PAD_LEFT);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate required fields
        $required = ['patientName', 'staffName', 'appointmentDate', 'purpose', 'status'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("$field is required");
            }
        }

        // Handle optional fields
        $consultationnotes = !empty($_POST['consultationnotes']) ? $_POST['consultationnotes'] : NULL;
        $followUpDate = !empty($_POST['followUpDate']) ? $_POST['followUpDate'] : NULL;
        $diagnosis = !empty($_POST['diagnosis']) ? $_POST['diagnosis'] : NULL;

        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO Appointments 
            (AppointmentID, patientName, staffName, appointmentDate, purpose, status, 
             consultationnotes, createdAt, followUpDate, diagnosis) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param(
            "ssssssssss",
            $_POST['AppointmentID'],
            $_POST['patientName'],
            $_POST['staffName'],
            $_POST['appointmentDate'],
            $_POST['purpose'],
            $_POST['status'],
            $consultationnotes,
            $_POST['createdAt'],
            $followUpDate,
            $diagnosis
        );

        if ($stmt->execute()) {
            echo "<script>
                alert('Appointment saved successfully!');
                window.location.href='appointmentsView.php';
            </script>";
            exit;
        } else {
            throw new Exception("Error saving appointment: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Appointment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
    <a href="treatmentsView.php"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php" class="active"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h2>Add Appointment</h2>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form action="appointmentsAdd.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Appointment ID</label>
        <input type="text" name="AppointmentID" class="form-control" value="<?= htmlspecialchars($nextAppointmentID) ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Patient Name</label>
        <input type="text" name="patientName" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Staff Name</label>
        <input type="text" name="staffName" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Appointment Date</label>
        <input type="datetime-local" name="appointmentDate" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Purpose</label>
        <select name="purpose" class="form-select" required>
          <option value="">-- Purpose --</option>
          <option value="General Check-Up">General Check-Up</option>
          <option value="Illness/Symptoms">Illness/Symptoms</option>
          <option value="Vaccinations">Vaccinations</option>
          <option value="Medical Tests">Medical Tests</option>
          <option value="Injury Care">Injury Care</option>
          <option value="Medication Related">Medication Related</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
          <option value="">-- Status --</option>
          <option value="Scheduled">Scheduled</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
          <option value="NoShow">No Show</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Consultation Notes</label>
        <textarea name="consultationnotes" class="form-control" rows="2"></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Created Date</label>
        <input type="datetime-local" name="createdAt" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Follow Up Date</label>
        <input type="datetime-local" name="followUpDate" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Diagnosis</label>
        <input type="text" name="diagnosis" class="form-control">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Appointment</button>
      </div>
    </form>
  </div>
</div>
</body>
</html>

<?php
// Close connection if still open
if (isset($conn)) {
    $conn->close();
}
?>