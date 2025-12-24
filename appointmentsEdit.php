<?php
// Database connection
$host = 'localhost';
$db = 'ricomtrx_db2624st1g10';
$user = 'ricomtrx_db2624st1g10';
$pass = 'db2624st1g10#VhE@386.88';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get patient ID from URL
$AppointmentID = $_GET['AppointmentID'] ?? '';
$appointment = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle form update
    $stmt = $conn->prepare("UPDATE Appointments SET patientName=?, staffName=?, appointmentDate=?, purpose=?, status=?, consultationnotes=?, createdAt=?, followUpDate=?, diagnosis=? WHERE AppointmentID=?");

    $stmt->bind_param("sssssssssi",
        $_POST["patientName"], $_POST["staffName"], $_POST["appointmentDate"], $_POST["purpose"], $_POST["status"],
        $_POST["consultationnotes"], $_POST["createdAt"], $_POST["followUpDate"], $_POST["diagnosis"],
        $_POST["AppointmentID"]
    );

    if ($stmt->execute()) {
        header("Location: appointmentsView.php?message=Appointment updated successfully");
        exit;
    } else {
        $error = "Update failed: " . $conn->error;
    }
} else {
    // Load patient data
    $stmt = $conn->prepare("SELECT * FROM Appointments WHERE AppointmentID = ?");
    $stmt->bind_param("i", $AppointmentID);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Edit Appointment</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="AppointmentID" value="<?= htmlspecialchars($appointment['AppointmentID']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3"><label>Patient Name</label><input type="text" name="patientName" class="form-control" value="<?= htmlspecialchars($appointment['patientName']) ?>" required></div>
                        <div class="mb-3"><label>Staff Name</label><input type="text" name="staffName" class="form-control" value="<?= htmlspecialchars($appointment['staffName']) ?>" required></div>
                        <div class="mb-3"><label>Appointment Date</label><input type="datetime-local" name="appointmentDate" class="form-control" value="<?= htmlspecialchars($appointment['appointmentDate']) ?>" required></div>
                        <div class="mb-3"><label>Purpose</label><input type="text" name="purpose" class="form-control" value="<?= htmlspecialchars($appointment['purpose']) ?>" required></div>
                        <div class="mb-3"><label>Diagnosis</label><input type="text" name="diagnosis" class="form-control" value="<?= htmlspecialchars($appointment['diagnosis']) ?>" required></div>

                    </div>
                        <div class="col-md-6">
                        <div class="mb-3"><label>Status</label><input type="text" name="status" class="form-control" value="<?= htmlspecialchars($appointment['status']) ?>" required></div>
                        <div class="mb-3"><label>consultation Notes</label><input type="text" name="consultationnotes" class="form-control" value="<?= htmlspecialchars($appointment['consultationnotes']) ?>" required></div>
                        <div class="mb-3"><label>createdAt</label><input type="datetime-local" name="createdAt" class="form-control" value="<?= htmlspecialchars($appointment['createdAt']) ?>" required></div>
                        <div class="mb-3"><label>Follow Update</label><input type="datetime-local" name="followUpDate" class="form-control" value="<?= htmlspecialchars($appointment['followUpDate']) ?>" required></div>
            </div>
            </div>

                <div class="text-end">
                    <a href="appointmentsView.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
