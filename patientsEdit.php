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
$patientID = $_GET['patientID'] ?? '';
$patient = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Handle form update
    $stmt = $conn->prepare("UPDATE Patients SET fullName=?, icNo=?, gender=?, dateOfBirth=?, bloodType=?, allergies=?, contactNo=?, email=?, addressLine1=?, addressLine2=?, city=?, postcode=?, emergencyContactName=?, emergencyContactNo=? WHERE patientID=?");

    $stmt->bind_param("ssssssssssssssi",
        $_POST["fullName"], $_POST["icNo"], $_POST["gender"], $_POST["dateOfBirth"], $_POST["bloodType"],
        $_POST["allergies"], $_POST["contactNo"], $_POST["email"], $_POST["addressLine1"], $_POST["addressLine2"],
        $_POST["city"], $_POST["postcode"], $_POST["emergencyContactName"], $_POST["emergencyContactNo"],
        $_POST["patientID"]
    );

    if ($stmt->execute()) {
        header("Location: patientsView.php?message=Patient updated successfully");
        exit;
    } else {
        $error = "Update failed: " . $conn->error;
    }
} else {
    // Load patient data
    $stmt = $conn->prepare("SELECT * FROM Patients WHERE patientID = ?");
    $stmt->bind_param("i", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Edit Patient</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="patientID" value="<?= htmlspecialchars($patient['patientID']) ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3"><label>Full Name</label><input type="text" name="fullName" class="form-control" value="<?= htmlspecialchars($patient['fullName']) ?>" required></div>
                        <div class="mb-3"><label>IC No</label><input type="text" name="icNo" class="form-control" value="<?= htmlspecialchars($patient['icNo']) ?>" required></div>
                        <div class="mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="M" <?= $patient['gender'] == 'M' ? 'selected' : '' ?>>Male</option>
                                <option value="F" <?= $patient['gender'] == 'F' ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>
                        <div class="mb-3"><label>Date of Birth</label><input type="date" name="dateOfBirth" class="form-control" value="<?= htmlspecialchars($patient['dateOfBirth']) ?>" required></div>
                        <label>Blood Type</label> <select name="bloodType" class="form-select" required>      
                        <option value="">Select</option>
                        <option value="A+" <?= $patient['bloodType'] == 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= $patient['bloodType'] == 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= $patient['bloodType'] == 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= $patient['bloodType'] == 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= $patient['bloodType'] == 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= $patient['bloodType'] == 'AB-' ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= $patient['bloodType'] == 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= $patient['bloodType'] == 'O-' ? 'selected' : '' ?>>O-</option>
                        </select>

                        <div class="mb-3"><label>Allergies</label><input type="text" name="allergies" class="form-control" value="<?= htmlspecialchars($patient['allergies']) ?>"></div>
                        <div class="mb-3"><label>Contact No</label><input type="text" name="contactNo" class="form-control" value="<?= htmlspecialchars($patient['contactNo']) ?>" required></div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($patient['email']) ?>"></div>
                        <div class="mb-3"><label>Address Line 1</label><input type="text" name="addressLine1" class="form-control" value="<?= htmlspecialchars($patient['addressLine1']) ?>"></div>
                        <div class="mb-3"><label>Address Line 2</label><input type="text" name="addressLine2" class="form-control" value="<?= htmlspecialchars($patient['addressLine2']) ?>"></div>
                        <div class="mb-3"><label>City</label><input type="text" name="city" class="form-control" value="<?= htmlspecialchars($patient['city']) ?>"></div>
                        <div class="mb-3"><label>Postcode</label><input type="text" name="postcode" class="form-control" value="<?= htmlspecialchars($patient['postcode']) ?>"></div>
                        <div class="mb-3"><label>Emergency Contact Name</label><input type="text" name="emergencyContactName" class="form-control" value="<?= htmlspecialchars($patient['emergencyContactName']) ?>"></div>
                        <div class="mb-3"><label>Emergency Contact No</label><input type="text" name="emergencyContactNo" class="form-control" value="<?= htmlspecialchars($patient['emergencyContactNo']) ?>"></div>
                    </div>
                </div>

                <div class="text-end">
                    <a href="patientsView.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
