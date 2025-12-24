<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'auth.php';
requireAuth();

// DB connection
$host = 'localhost';
$db = 'ricomtrx_db2624st1g10';
$user = 'ricomtrx_db2624st1g10';
$pass = 'db2624st1g10#VhE@386.88';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Total patients
$totalPatients = 0;
$totalResult = $conn->query("SELECT COUNT(*) as total FROM Patients");
if ($totalResult && $row = $totalResult->fetch_assoc()) {
    $totalPatients = $row['total'];
}

// Today's Appointments
$todaysAppointments = 0;
$today = date('Y-m-d');
$apptResult = $conn->query("SELECT COUNT(*) as total FROM Appointments WHERE appointmentDate = '$today'");
if ($apptResult && $row = $apptResult->fetch_assoc()) {
    $todaysAppointments = $row['total'];
}

// Search logic
$patients = [];
$searchTerm = '';
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $searchTerm = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM Patients WHERE fullName LIKE '%$searchTerm%'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>UNIT KESIHATAN UiTM PUNCAK PERDANA</title>
<head>
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

    .main-content {
      flex: 1;
      padding: 2rem;
      background-color: rgba(212, 241, 228, 1);
    }
    .user-info {
  position: absolute;
  top: 1rem;
  right: 1rem;
  color: #2e7d6b;
  font-weight: bold;
}

    .logo-title {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
      margin-bottom: 20px;
    }

    .logo-title img {
      height: 100px;
    }

    .stats-card {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      text-align: center;
    }

    .action-buttons {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-bottom: 20px;
    }

    .search-bar {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .search-bar input {
      width: 50%;
    }

    @media (max-width: 768px) {
      .sidebar {
        min-width: 100%;
        height: auto;
        position: static;
        text-align: center;
      }

      .sidebar a {
        justify-content: center;
      }

      .main-content {
        padding: 1rem;
        margin-left: 0;
      }

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
    <a href="home.php" class="<?= basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : '' ?>"><i class="bi bi-house-fill"></i> Home</a>
    <a href="patientsView.php" class="<?= basename($_SERVER['PHP_SELF']) == 'patientsView.php' ? 'active' : '' ?>"><i class="bi bi-person-lines-fill"></i> Patients</a>
    <a href="staffsView.php" class="<?= basename($_SERVER['PHP_SELF']) == 'staffsView.php' ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Staffs</a>
    <a href="treatmentsView.php" class="<?= basename($_SERVER['PHP_SELF']) == 'treatmentsView.php' ? 'active' : '' ?>"><i class="bi bi-capsule-pill"></i> Treatments</a>
    <a href="appointmentsView.php" class="<?= basename($_SERVER['PHP_SELF']) == 'appointmentsView.php' ? 'active' : '' ?>"><i class="bi bi-calendar-check-fill"></i> Appointments</a>
    <a href="logout.php" class="mt-auto"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
  
<!-- Main Content -->
<div class="main-content">
<div class="user-info">
  Welcome, <?= htmlspecialchars(getUserName()) ?> | <?= getUserRole() ?>
</div>
  <!-- Logo and Title -->
  <div class="logo-title">
    <img src="uk.png" alt="uk">
    <h2 class="mb-0">UNIT KESIHATAN UiTM PUNCAK PERDANA</h2>
  </div>

  <!-- Dashboard Stat Cards -->
  <div class="row text-center mb-4">
    <div class="col-md-6">
      <div class="stats-card">
        <h5>Total Registered Patients</h5>
        <h2><?= $totalPatients ?></h2>
      </div>
    </div>
    <div class="col-md-6">
      <div class="stats-card">
        <h5>Today's Appointments</h5>
        <h2><?= $todaysAppointments ?></h2>
      </div>
    </div>
  </div>

  <!-- Search form -->
  <form class="search-bar" method="get" action="home.php">
    <input type="text" name="search" class="form-control" placeholder="Search patient by name..." value="<?= htmlspecialchars($searchTerm) ?>">
    <button type="submit" class="btn btn-primary ms-2">Search</button>
  </form>

  <!-- Centered Action Buttons -->
  <div class="d-flex justify-content-center gap-2 mb-4 flex-wrap">
    <a href="patientsAdd.php" class="btn btn-success">+ Add Patient</a>
    <a href="appointmentsAdd.php" class="btn btn-primary">+ Add Appointment</a>
    <a href="staffsAdd.php" class="btn btn-info">+ Add Staff</a>
    <a href="treatmentsAdd.php" class="btn btn-warning text-white">+ Add Treatment</a>
  </div>

  <!-- Results Table -->
  <?php if ($searchTerm !== ''): ?>
    <h5>Search results for: <strong><?= htmlspecialchars($searchTerm) ?></strong></h5>
    <?php if (count($patients) > 0): ?>
      <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Patient ID</th>
              <th>Full Name</th>
              <th>IC No</th>
              <th>Contact No</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($patients as $patient): ?>
              <tr>
                <td><?= htmlspecialchars($patient['patientID']) ?></td>
                <td><?= htmlspecialchars($patient['fullName']) ?></td>
                <td><?= htmlspecialchars($patient['icNo']) ?></td>
                <td><?= htmlspecialchars($patient['contactNo']) ?></td>
                <td><?= htmlspecialchars($patient['email']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">No patients found matching your search.</div>
    <?php endif; ?>
  <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
