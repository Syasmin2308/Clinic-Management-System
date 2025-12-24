<?php
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$treatmentID = $_GET['treatmentID'] ?? '';
$appointments = $conn->query("SELECT appointmentID FROM Appointments");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE Treatments SET appointmentID=?, treatmentType=?, treatmentName=?, description=?, cost=?, status=?, medDosage=?, medFrequency=?, medDuration=?, medQuantity=? WHERE treatmentID=?");
    $stmt->bind_param("issssdssssi",
        $_POST['appointmentID'], $_POST['treatmentType'], $_POST['treatmentName'], $_POST['description'], $_POST['cost'],
        $_POST['status'], $_POST['medDosage'], $_POST['medFrequency'], $_POST['medDuration'], $_POST['medQuantity'], $_POST['treatmentID']
    );
    $stmt->execute();
    header("Location: treatmentsView.php?message=Treatment updated successfully");
    exit;
} else {
    $stmt = $conn->prepare("SELECT * FROM Treatments WHERE treatmentID = ?");
    $stmt->bind_param("i", $treatmentID);
    $stmt->execute();
    $treatment = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html><head><title>Edit Treatment</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5">
<div class="card shadow">
<div class="card-header bg-success text-white"><h4>Edit Treatment</h4></div>
<div class="card-body">
<form method="post">
<input type="hidden" name="treatmentID" value="<?= $treatment['treatmentID'] ?>">
<div class="row">
<div class="col-md-6">
    <div class="mb-3"><label>Appointment ID</label><select name="appointmentID" class="form-select">
        <?php while ($a = $appointments->fetch_assoc()): ?>
            <option value="<?= $a['appointmentID'] ?>" <?= $a['appointmentID'] == $treatment['appointmentID'] ? 'selected' : '' ?>><?= $a['appointmentID'] ?></option>
        <?php endwhile; ?>
    </select></div>
    <div class="mb-3"><label>Treatment Type</label><input type="text" name="treatmentType" class="form-control" value="<?= $treatment['treatmentType'] ?>"></div>
    <div class="mb-3"><label>Treatment Name</label><input type="text" name="treatmentName" class="form-control" value="<?= $treatment['treatmentName'] ?>"></div>
    <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"><?= $treatment['description'] ?></textarea></div>
    <div class="mb-3"><label>Cost</label><input type="number" step="0.01" name="cost" class="form-control" value="<?= $treatment['cost'] ?>"></div>
</div>
<div class="col-md-6">
    <div class="mb-3"><label>Status</label><input type="text" name="status" class="form-control" value="<?= $treatment['status'] ?>"></div>
    <div class="mb-3"><label>Medication Dosage</label><input type="text" name="medDosage" class="form-control" value="<?= $treatment['medDosage'] ?>"></div>
    <div class="mb-3"><label>Medication Frequency</label><input type="text" name="medFrequency" class="form-control" value="<?= $treatment['medFrequency'] ?>"></div>
    <div class="mb-3"><label>Medication Duration</label><input type="text" name="medDuration" class="form-control" value="<?= $treatment['medDuration'] ?>"></div>
    <div class="mb-3"><label>Medication Quantity</label><input type="text" name="medQuantity" class="form-control" value="<?= $treatment['medQuantity'] ?>"></div>
</div>
</div>
<div class="text-end">
    <a href="treatmentsView.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">Update Treatment</button>
</div>
</form></div></div></div></body></html>