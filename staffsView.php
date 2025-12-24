<?php
// Display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'auth.php';
requireAuth();

// DB connection
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all treatments
$sql = "SELECT * FROM Staffs";
$result = $conn->query($sql);

// Handle filters
$filterPosition = $_GET['position'] ?? '';
$filterSpecialization = $_GET['specialization'] ?? '';

// SQL with filters
$sql = "SELECT * FROM Staffs WHERE 1=1";
if (!empty($filterPosition)) {
    $sql .= " AND position = '" . $conn->real_escape_string($filterPosition) . "'";
}
if (!empty($filterBlood)) {
    $sql .= " AND specialization = '" . $conn->real_escape_string($filterSpecialization) . "'";
}
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staffs List</title>
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

.table-responsive {
    overflow-x: auto;
    max-width: 100%;
    border: 1px solid #dee2e6;
    border-radius: 5px;
}

.table {
    min-width: 1000px;
    margin-bottom: 0;
}

.table th {
    white-space: nowrap;
    background-color: #020202ff;
    color: white;
}

.table td {
    vertical-align: middle;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
    @media (max-width: 768px) {
      .wrapper {
        flex-direction: column;
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

  <!-- Main content -->
  <div class="main-content">
      <div class="d-flex align-items-center justify-content-between mb-3">
      <h2 class="mb-0">Staffs Records</h2>
      <a href="staffsAdd.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Staff</a>
    </div>

<!-- Filter form -->
<form class="row g-3 mb-3 align-items-end" method="GET" action="">
    <div class="col-md-3 col-6">
        <label class="form-label">Filter by Position:</label>
        <select name="position" class="form-select">
            <option value="">All Positions</option>
            <option value="Doctor" <?= $filterPosition == 'Doctor' ? 'selected' : '' ?>>Doctor</option>
            <option value="Nurse" <?= $filterPosition == 'Nurse' ? 'selected' : '' ?>>Nurse</option>
            <option value="Medical Assistant" <?= $filterPosition == 'Medical Assistant' ? 'selected' : '' ?>>Medical Assistant</option>
        </select>
    </div>
    <div class="col-md-3 col-6">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="staffsView.php" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>   

        <?php if ($result && $result->num_rows > 0): ?>
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Staff ID</th>
                <th>Full Name</th>
                <th>IC No</th>
                <th>Gender</th>
                <th>Position</th>
                <th>Specialization</th>
                <th>Contact No</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['StaffID']) ?></td>
                <td><?= htmlspecialchars($row['fullName']) ?></td>
                <td><?= htmlspecialchars($row['ICNo']) ?></td>
                <td><?= $row['gender'] === 'M' ? 'Male' : 'Female' ?></td>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td><?= $row['specialization'] ? htmlspecialchars($row['specialization']) : 'N/A' ?></td>                <td><?= htmlspecialchars($row['contactNo']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['isActive'] ? 'Active' : 'Inactive' ?></td>
                <td>
                    <a href="staffsEdit.php?staffID=<?= $row['StaffID'] ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="staffsDelete.php?staffID=<?= $row['StaffID'] ?>" class="btn btn-sm btn-danger" 
                       onclick="return confirm('Delete this staff member?')">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
    <?php else: ?>
      <div class="alert alert-warning">No staff records found.</div>
    <?php endif; ?>
</body>
</html>

<?php $conn->close(); ?>
