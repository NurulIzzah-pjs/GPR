<?php
// editParticipant.php
include('db.php');

if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['matrics']) && isset($_POST['phone']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $matrics = $_POST['matrics'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];

    // Update data in the database
    $sql = "UPDATE participants SET name=?, matrics=?, phone=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $matrics, $phone, $status, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
}

$conn->close();
?>

