<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'ricomtrx_db2624st1g10';
$user = 'ricomtrx_db2624st1g10';
$pass = 'db2624st1g10#VhE@386.88';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patientID = $_GET['patientID'] ?? '';
if ($patientID) {
    $stmt = $conn->prepare("DELETE FROM Patients WHERE patientID = ?");
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
}

$conn->close();
header("Location: patientsView.php?message=" . urlencode("Patient deleted successfully!"));
exit;
