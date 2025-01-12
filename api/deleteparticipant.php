<?php
// deleteParticipant.php
include('db.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Delete the participant from the database
    $sql = "DELETE FROM participants WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
}

$conn->close();
?>

