<?php
// Display errors (for debugging only — remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$host = 'localhost';
$db = 'ricomtrx_db2624st1g10';
$user = 'ricomtrx_db2624st1g10';
$pass = 'db2624st1g10#VhE@386.88';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get appointment data
// Modify your query to:
$sql = "SELECT a.*, p.fullName AS patientName, s.fullName AS staffName 
        FROM Appointments a
        JOIN Patients p ON a.patientID = p.patientID
        JOIN Staff s ON a.staffID = s.staffID";

// Then display patientName and staffName in your table$result = $conn->query($sql);

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get appointment data with optional status filter
$sql = "SELECT * FROM Appointments";
if (!empty($status_filter)) {
    $sql .= " WHERE status = '" . $conn->real_escape_string($status_filter) . "'";
}
$result = $conn->query($sql);

// Define possible status values
$status_options = [
    '' => 'All Statuses',
    'Scheduled' => 'Scheduled',
    'Completed' => 'Completed',
    'Cancelled' => 'Cancelled',
    'No-Show' => 'No-Show'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Appointment List</title>
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

    /* Sticky header for buttons */
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
    <a href="patientsView.php"><i class="bi bi-person-lines-fill"></i> Patients</a>
    <a href="staffsView.php"><i class="bi bi-people-fill"></i> Staffs</a>
    <a href="treatmentsView.php"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php" class="active"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="content-container">
      <!-- Sticky header section -->
      <div class="sticky-header">
        <!-- Title + Button -->
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="mb-0">Appointment Records</h2>
          <a href="appointmentsAdd.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Appointment
          </a>
        </div>
        
        <!-- Status Filter Form -->
        <form method="GET" action="" class="mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-md-3">
              <label class="form-label">Filter by Status:</label>
              <select name="status" class="form-select" onchange="this.form.submit()">
                <?php foreach ($status_options as $value => $label): ?>
                  <option value="<?= $value ?>" <?= $status_filter == $value ? 'selected' : '' ?>>
                    <?= $label ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
      <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
      <a href="appointmentsView.php" class="btn btn-secondary">Reset</a>
    </div>
          </div>
        </form>
      </div>
      <!-- Scrollable table container -->
      <div class="table-container">
        <?php if ($result && $result->num_rows > 0): ?>
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Staff Name</th>
                <th>Appointment Date</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Consultation Notes</th>
                <th>Created</th>
                <th>Follow Updates</th>
                <th>Diagnosis</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
<?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= htmlspecialchars($row['AppointmentID'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['patientName'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['staffName'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['appointmentDate'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['purpose'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['consultationnotes'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['createdAt'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['followUpDate'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['diagnosis'] ?? '') ?></td>
                  <td>
                    <a href="appointmentsEdit.php?AppointmentID=<?= urlencode($row['AppointmentID']) ?>" class="btn btn-sm btn-primary mb-1">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="appointmentsDelete.php?AppointmentID=<?= urlencode($row['AppointmentID']) ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this appointment?');">
                      <i class="bi bi-trash-fill"></i>
                    </a>
                  </td>
                </tr>
<?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-warning">No appointment records found.</div>
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