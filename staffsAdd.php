<?php
// Display errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $isActive = ($_POST['isActive'] === "Active") ? 1 : 0;
    // Set specialization to NULL if not a doctor or empty
    $specialization = ($_POST['position'] === "Doctor" && !empty($_POST['specialization'])) 
        ? $_POST['specialization'] 
        : NULL;  // Now allowed by DB schema

    $stmt = $conn->prepare("INSERT INTO Staffs (staffID, fullName, icNo, gender, position, specialization, contactNo, email, isActive) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssi",
        $_POST['staffID'],
        $_POST['fullName'],
        $_POST['icNo'],
        $_POST['gender'],
        $_POST['position'],
        $specialization,  // Can be NULL
        $_POST['contactNo'],
        $_POST['email'],
        $isActive
    );

    if ($stmt->execute()) {
        echo "<script>alert('Staff data saved successfully!'); window.location.href='staffsView.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
    $conn->close();

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Staff</title>
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
    <a href="staffsView.php" class="active"><i class="bi bi-people-fill"></i> Staffs</a>
    <a href="treatmentsView.php"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

<div class="main-content">
  <h2>Add Staff</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="row g-3">
      <div class="col-md-6">
      <div class="mb-3"><label>Staff ID</label><input type="text" name="staffID" class="form-control" required></div>
      <div class="mb-3"><label>Full Name</label><input type="text" name="fullName" class="form-control" required></div>
      <div class="mb-3"><label>IC No</label><input type="text" name="icNo" class="form-control" required></div>
      <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-select" required>
          <option value="M">Male</option>
          <option value="F">Female</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Position</label>
        <select name="position" class="form-select" id="position" onchange="toggleSpecialization()" required>
          <option value="">-- Select --</option>
          <option value="Doctor">Doctor</option>
          <option value="Nurse">Nurse</option>
          <option value="Medical Assistant">Medical Assistant</option>
          <option value="Administrative">Administrative</option>
          <option value="Pharmacist">Pharmacist</option>
        </select>
      </div>
    </div>

    <div class="col-md-6">
      <div class="mb-3">
        <label>Specialization</label>
        <select name="specialization" id="specialization" class="form-select" disabled>
          <option value="">-- Select if Doctor --</option>
          <option value="General">General</option>
          <option value="Pediatrics">Pediatrics</option>
          <option value="Dental">Dental</option>
        </select>
      </div>
      <div class="mb-3"><label>Contact No</label><input type="text" name="contactNo" class="form-control" required></div>
      <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"></div>
      <div class="mb-3">
        <label>Status</label>
        <select name="isActive" class="form-select">
          <option value="Active" selected>Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
    </div>

    <div class="col-12 text-end">
        <a href="staffsView.php" class="btn btn-secondary">Cancel</a>
        <button type="submit" name="submit" class="btn btn-primary">Add Staff</button>    </div>
  </form>
</div>

<script>
function toggleSpecialization() {
  const position = document.getElementById('position').value;
  const specialization = document.getElementById('specialization');
  specialization.disabled = position !== 'Doctor';
  specialization.required = position === 'Doctor';
  if (position !== 'Doctor') specialization.value = '';
}
</script>
</body>
</html>
