<?php
// Show all errors (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // DB connection
    $conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

$result = $conn->query("SELECT patientID FROM Patients ORDER BY patientID DESC LIMIT 1");
$nextPatientID = "P0001";
if ($result && $row = $result->fetch_assoc()) {
    $num = intval(substr($row['patientID'], 1)) + 1;
    $nextPatientID = "P" . str_pad($num, 4, "0", STR_PAD_LEFT);
}


    // Collect form data
    $patientID = $_POST['patientID'] ?? '';
    $fullName = $_POST['fullName'] ?? '';
    $icNo = $_POST['icNo'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dateOfBirth = $_POST['dateOfBirth'] ?? '';
    $bloodType = $_POST['bloodType'] ?? '';
    $allergies = $_POST['allergies'] ?? '';
    $contactNo = $_POST['contactNo'] ?? '';
    $email = $_POST['email'] ?? '';
    $addressLine1 = $_POST['addressLine1'] ?? '';
    $addressLine2 = $_POST['addressLine2'] ?? '';
    $city = $_POST['city'] ?? '';
    $postcode = $_POST['postcode'] ?? '';
    $emergencyContactName = $_POST['emergencyContactName'] ?? '';
    $emergencyContactNo = $_POST['emergencyContactNo'] ?? '';

    // Prepare SQL
    $stmt = $conn->prepare("INSERT INTO Patients (
        patientID, fullName, icNo, gender, dateOfBirth,
        bloodType, allergies, contactNo, email,
        addressLine1, addressLine2, city, postcode,
        emergencyContactName, emergencyContactNo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssssss",
        $patientID, $fullName, $icNo, $gender, $dateOfBirth,
        $bloodType, $allergies, $contactNo, $email,
        $addressLine1, $addressLine2, $city, $postcode,
        $emergencyContactName, $emergencyContactNo
    );

    if ($stmt->execute()) {
        echo "<script>alert('Patient data saved successfully!'); window.location.href='patientsView.php';</script>";
    } else {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Patient</title>
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
    <a href="patientsView.php" class="active"><i class="bi bi-person-lines-fill"></i> Patients</a>
    <a href="staffsView.php"><i class="bi bi-people-fill"></i> Staffs</a>
    <a href="treatmentsView.php"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

<!-- Main Content -->
<div class="main-content">
  <h2>Add Patient</h2>
  <form action="patientsAdd.php" method="POST" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Patient ID</label>
<input type="text" name="patientID" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Full Name</label>
      <input type="text" name="fullName" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">IC Number</label>
      <input type="text" name="icNo" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label d-block">Gender</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" value="M" required>
        <label class="form-check-label">Male</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" value="F" required>
        <label class="form-check-label">Female</label>
      </div>
    </div>
    <div class="col-md-6">
      <label class="form-label">Date of Birth</label>
      <input type="date" name="dateOfBirth" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Blood Type</label>
      <select name="bloodType" class="form-select" required>
        <option value="">Select</option>
        <option>A+</option>
        <option>A-</option>
        <option>B+</option>
        <option>B-</option>
        <option>AB+</option>
        <option>AB-</option>
        <option>O+</option>
        <option>O-</option>
      </select>
    </div>
    <div class="col-md-12">
      <label class="form-label">Allergies</label>
      <input type="text" name="allergies" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Contact No</label>
      <input type="tel" name="contactNo" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Address Line 1</label>
      <input type="text" name="addressLine1" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Address Line 2</label>
      <input type="text" name="addressLine2" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">City</label>
      <input type="text" name="city" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Postcode</label>
      <input type="text" name="postcode" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Emergency Contact Name</label>
      <input type="text" name="emergencyContactName" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Emergency Contact No</label>
      <input type="tel" name="emergencyContactNo" class="form-control" required>
    </div>
    <div class="col-12">
      <button type="submit" class="btn btn-primary">Save Patient</button>
    </div>
  </form>
</div>

</body>
</html>
