<?php
// addParticipant.php
include('db.php');

if (isset($_POST['name']) && isset($_POST['matrics']) && isset($_POST['phone']) && isset($_POST['status'])) {
    $name = $_POST['name'];
    $matrics = $_POST['matrics'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];

    // Insert data into the database
    $sql = "INSERT INTO participants (name, matrics, phone, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $matrics, $phone, $status);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
}

$conn->close();
?>
