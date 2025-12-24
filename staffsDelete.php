<?php
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$staffID = $_GET['staffID'] ?? '';
if ($staffID) {
    $stmt = $conn->prepare("DELETE FROM Staffs WHERE staffID=?");
    $stmt->bind_param("s", $staffID);
    $stmt->execute();
}
$conn->close();
header("Location: staffsView.php?message=" . urlencode("Staff deleted successfully!"));
exit;
