<?php
$conn = new mysqli("localhost", "ricomtrx_db2624st1g10", "db2624st1g10#VhE@386.88", "ricomtrx_db2624st1g10");

$treatmentID = $_GET['treatmentID'] ?? '';
if ($treatmentID) {
    $stmt = $conn->prepare("DELETE FROM Treatments WHERE treatmentID=?");
    $stmt->bind_param("s", $treatmentID);
    $stmt->execute();
}
$conn->close();
header("Location: treatmentsView.php?message=" . urlencode("Treatment deleted successfully!"));
exit;
