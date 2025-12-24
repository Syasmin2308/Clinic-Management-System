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

// Get treatment data
$sql = "SELECT * FROM Treatments";
$result = $conn->query($sql);

// Handle filters
$filterType = isset($_GET['type']) ? $_GET['type'] : '';

// Get treatment data with optional filter
$sql = "SELECT * FROM Treatments";
if (!empty($filterType)) {
    $sql .= " WHERE treatmentType = '" . $conn->real_escape_string($filterType) . "'";
}
$result = $conn->query($sql);

// Define possible treatment types
$type_options = [
    '' => 'All Types',
    'General Medicine' => 'General Medicine',
    'Minor Procedure' => 'Minor Procedure',
    'Preventative Care' => 'Preventative Care',
    'Lab Test' => 'Lab Test'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Treatment List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden;
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
      overflow: hidden;
    }

    .content-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .table-container {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      flex: 1;
      margin-top: 20px;
      border: 1px solid #dee2e6;
    }

    .table {
      width: 100%;
      min-width: 1200px;
      margin-bottom: 0;
    }

    .table th, .table td {
      white-space: nowrap;
      font-size: 14px;
      vertical-align: middle;
    }

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
        min-width: 100%;
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
    <div class="content-container">
      <!-- Sticky header section -->
      <div class="sticky-header">
        <!-- Title + Button -->
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="mb-0">Treatment Records</h2>
          <a href="treatmentsAdd.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Add Treatment
          </a>
        </div>
        
        <!-- Type Filter Form -->
        <form method="GET" action="" class="mb-3">
          <div class="row g-2 align-items-center">
            <div class="col-md-3">
              <label class="form-label">Filter by Type:</label>
              <select name="type" class="form-select" onchange="this.form.submit()">
                <?php foreach ($type_options as $value => $label): ?>
                  <option value="<?= $value ?>" <?= $filterType == $value ? 'selected' : '' ?>>
                    <?= $label ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
              <a href="treatmentsView.php" class="btn btn-secondary">Reset</a>
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
                <th>Treatment ID</th>
                <th>Appointment ID</th>
                <th>Treatment Type</th>
                <th>Treatment Name</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Status</th>
                <th>Medical Dosage</th>
                <th>Medical Frequency</th>
                <th>Medical Duration</th>
                <th>Medical Quantity</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($row['treatmentID']) ?></td>
                  <td><?= htmlspecialchars($row['appointmentID']) ?></td>
                  <td><?= htmlspecialchars($row['treatmentType']) ?></td>
                  <td><?= htmlspecialchars($row['treatmentName']) ?></td>
                  <td><?= htmlspecialchars($row['description']) ?></td>
                  <td><?= htmlspecialchars($row['cost']) ?></td>
                  <td><?= htmlspecialchars($row['status']) ?></td>
                  <td><?= htmlspecialchars($row['medDosage']) ?></td>
                  <td><?= htmlspecialchars($row['medFrequency']) ?></td>
                  <td><?= htmlspecialchars($row['medDuration']) ?></td>
                  <td><?= htmlspecialchars($row['medQuantity']) ?></td>
                  <td>
                    <a href="treatmentsEdit.php?treatmentID=<?= urlencode($row['treatmentID']) ?>" class="btn btn-sm btn-primary mb-1">
                      <i class="bi bi-pencil-fill"></i>
                    </a>
                    <a href="treatmentsDelete.php?treatmentID=<?= urlencode($row['treatmentID']) ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this treatment?');">
                      <i class="bi bi-trash-fill"></i>
                    </a>
                  </td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-warning">No treatment records found.</div>
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