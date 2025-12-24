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

// Get Staff ID from URL
$staffID = $_GET['staffID'] ?? '';
$staff = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle update
    $isActive = ($_POST['isActive'] === "Active") ? 1 : 0;
    $specialization = ($_POST['position'] === "Doctor" && !empty($_POST['specialization'])) ? $_POST['specialization'] : NULL;

    $stmt = $conn->prepare("UPDATE Staffs SET fullName=?, ICNo=?, gender=?, position=?, specialization=?, contactNo=?, email=?, isActive=? WHERE StaffID=?");

    $stmt->bind_param(
        "sssssssis",
        $_POST['fullName'],
        $_POST['ICNo'],
        $_POST['gender'],
        $_POST['position'],
        $specialization,
        $_POST['contactNo'],
        $_POST['email'],
        $isActive,
        $_POST['StaffID']
    );

    if ($stmt->execute()) {
        header("Location: staffsView.php?message=Staff updated successfully");
        exit;
    } else {
        $error = "Update failed: " . $conn->error;
    }
} else {
    // Load staff data
    if (!empty($staffID)) {
        $stmt = $conn->prepare("SELECT * FROM Staffs WHERE StaffID = ?");
        $stmt->bind_param("s", $staffID);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        if (!$staff) {
            die("<div class='alert alert-danger'>Staff not found.</div>");
        }
    } else {
        die("<div class='alert alert-danger'>No staff ID provided.</div>");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Edit Staff</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="StaffID" value="<?= htmlspecialchars($staff['StaffID']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3"><label>Full Name</label><input type="text" name="fullName" class="form-control" value="<?= htmlspecialchars($staff['fullName']) ?>" required></div>
                        <div class="mb-3"><label>IC No</label><input type="text" name="ICNo" class="form-control" value="<?= htmlspecialchars($staff['ICNo']) ?>" required></div>
                        <div class="mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="M" <?= $staff['gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= $staff['gender'] == 'F' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Position</label>
                            <select name="position" id="position" class="form-select" onchange="toggleSpecialization()" required>
                                <option value="">-- Select --</option>
                                <option value="Doctor" <?= $staff['position'] == 'Doctor' ? 'selected' : '' ?>>Doctor</option>
                                <option value="Nurse" <?= $staff['position'] == 'Nurse' ? 'selected' : '' ?>>Nurse</option>
                                <option value="Medical Assistant" <?= $staff['position'] == 'Medical Assistant' ? 'selected' : '' ?>>Medical Assistant</option>
                                <option value="Administrative" <?= $staff['position'] == 'Administrative' ? 'selected' : '' ?>>Administrative</option>
                                <option value="Pharmacist" <?= $staff['position'] == 'Pharmacist' ? 'selected' : '' ?>>Pharmacist</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3"><label>Contact No</label><input type="text" name="contactNo" class="form-control" value="<?= htmlspecialchars($staff['contactNo']) ?>" required></div>
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($staff['email']) ?>"></div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="isActive" class="form-select">
                                <option value="Active" <?= $staff['isActive'] ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= !$staff['isActive'] ? 'selected' : '' ?>>Inactive</option>
                            </select>
            </div>
                           <div class="mb-3">
                            <label>Specialization</label>
                            <select name="specialization" id="specialization" class="form-select">
                                <option value="">-- Select if Doctor --</option>
                                <option value="General" <?= $staff['specialization'] == 'General' ? 'selected' : '' ?>>General</option>
                                <option value="Pediatrics" <?= $staff['specialization'] == 'Pediatrics' ? 'selected' : '' ?>>Pediatrics</option>
                                <option value="Dental" <?= $staff['specialization'] == 'Dental' ? 'selected' : '' ?>>Dental</option>
                            </select>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="staffsView.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleSpecialization() {
    const position = document.getElementById('position').value;
    const specialization = document.getElementById('specialization');
    specialization.disabled = position !== 'Doctor';
    specialization.required = position === 'Doctor';
    if (position !== 'Doctor') specialization.value = '';
}
window.onload = toggleSpecialization;
</script>
</body>
</html>

<?php $conn->close(); ?>
