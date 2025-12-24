<?php
// Enable all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'auth.php';
requireAuth();

// DB Connection
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get filter values
$filterGender = $_GET['gender'] ?? '';
$filterBlood = $_GET['bloodType'] ?? '';

// Build query with filters
$sql = "SELECT * FROM Patients WHERE 1=1";
if (!empty($filterGender)) {
    $sql .= " AND gender = '" . $conn->real_escape_string($filterGender) . "'";
}
if (!empty($filterBlood)) {
    $sql .= " AND bloodType = '" . $conn->real_escape_string($filterBlood) . "'";
}
$sql .= " LIMIT 5";

$result = $conn->query($sql);

if (!$result) die("Query failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Patient Records</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden; /* Prevent horizontal scroll on body */
    }

    .wrapper {
      display: flex;
      min-height: 100vh;
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
      display: flex;
      flex-direction: column;
      overflow: hidden; /* Contain all content */
    }

    .content-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden; /* Enable scrolling for table */
    }

    /* Table container - forces horizontal scroll */
    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      flex: 1;
      margin-top: 20px;
      border: 1px solid #dee2e6;
    }

    /* Table itself expands to fit content */
    .table {
      width: 100%;
      min-width: 1200px; /* Minimum width to show all columns */
      margin-bottom: 0;
    }

    /* Cells - prevent text wrapping */
    .table th, .table td {
      white-space: nowrap;
      font-size: 14px;
      vertical-align: middle;
    }

    /* Sticky header for buttons and filters */
    .sticky-header {
      position: sticky;
      top: 0;
      background-color: rgba(212, 241, 228, 1);
      z-index: 100;
      padding-top: 1rem;
      padding-bottom: 1rem;
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
      .table {
        min-width: 100%; /* Full width on mobile */
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
      <!-- Sticky header section -->
      <div class="sticky-header">
          <!-- Title + Add Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="mb-0">Patient Records</h2>
          <a href="patientsAdd.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Patient
          </a>
        </div>
      </div>
        <!-- Filter form -->
        <form class="row g-3 mb-3" method="GET" action="">
          <div class="col-md-3">
            <label>Filter by Gender:</label>
            <select name="gender" class="form-select">
              <option value="">-- All Genders --</option>
              <option value="M" <?= $filterGender == 'M' ? 'selected' : '' ?>>Male</option>
              <option value="F" <?= $filterGender == 'F' ? 'selected' : '' ?>>Female</option>
            </select>
          </div>
          <div class="col-md-3">
            <label>Filter by Blood Type:</label>
            <select name="bloodType" class="form-select">
              <option value="">-- All Blood Types --</option>
              <?php
              $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
              foreach ($bloodTypes as $type) {
                  $selected = $filterBlood == $type ? 'selected' : '';
                  echo "<option value=\"$type\" $selected>$type</option>";
              }
              ?>
            </select>
          </div>
          
          <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
            <a href="patientsView.php" class="btn btn-secondary">Reset</a>
          </div>
        </form>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_GET['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
      <!-- Scrollable table container -->
      <div class="table-container">
        <?php if ($result && $result->num_rows > 0): ?>
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Patient ID</th>
                <th>Full Name</th>
                <th>IC No</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Blood Type</th>
                <th>Allergies</th>
                <th>Contact No</th>
                <th>Email</th>
                <th>Address 1</th>
                <th>Address 2</th>
                <th>City</th>
                <th>Postcode</th>
                <th>Emergency Name</th>
                <th>Emergency No</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['patientID']) ?></td>
                  <td><?= htmlspecialchars($row['fullName']) ?></td>
                  <td><?= htmlspecialchars($row['icNo']) ?></td>
                  <td><?= $row['gender'] === 'M' ? 'Male' : 'Female' ?></td>
                  <td><?= htmlspecialchars($row['dateOfBirth']) ?></td>
                  <td><?= htmlspecialchars($row['bloodType']) ?></td>
                  <td><?= htmlspecialchars($row['allergies']) ?></td>
                  <td><?= htmlspecialchars($row['contactNo']) ?></td>
                  <td><?= htmlspecialchars($row['email']) ?></td>
                  <td><?= htmlspecialchars($row['addressLine1']) ?></td>
                  <td><?= htmlspecialchars($row['addressLine2']) ?></td>
                  <td><?= htmlspecialchars($row['city']) ?></td>
                  <td><?= htmlspecialchars($row['postcode']) ?></td>
                  <td><?= htmlspecialchars($row['emergencyContactName']) ?></td>
                  <td><?= htmlspecialchars($row['emergencyContactNo']) ?></td>
                  <td>
                    <a href="patientsEdit.php?patientID=<?= urlencode($row['patientID']) ?>" class="btn btn-sm btn-primary mb-1">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="patientsDelete.php?patientID=<?= urlencode($row['patientID']) ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this patient?');">
                      <i class="bi bi-trash-fill"></i>
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-warning">No patient records found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<?php
$conn->close();
?>