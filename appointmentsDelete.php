<?php
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");

$AppointmentID = $_GET['AppointmentID'] ?? '';
if ($AppointmentID) {
    $stmt = $conn->prepare("DELETE FROM Appointments WHERE AppointmentID=?");
    $stmt->bind_param("s", $AppointmentID);
    $stmt->execute();
}
$conn->close();
header("Location: appointmentsView.php?message=" . urlencode("Appointment deleted successfully!"));
exit;
