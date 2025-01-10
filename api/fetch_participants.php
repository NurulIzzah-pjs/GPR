<?php
// Database connection
include '../db.php'; // Ensure the correct path to db.php
header('Content-Type: application/json');

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch data
$sql = "SELECT 
    p.ParticipantID,
    p.Name,
    p.Role,
    p.IdentificationNum,
    p.MatricNum,
    p.PhoneNum,
    pk.PackageID,
    pk.PackageName,
    CASE 
        WHEN a.AttendanceStatus = 1 THEN 'Yes'
        ELSE 'No'
    END AS AttendanceStatus
FROM 
    Participant p
LEFT JOIN 
    Attendance a 
ON 
    p.ParticipantID = a.ParticipantID
LEFT JOIN 
    Package pk
ON 
    p.PackageID = pk.PackageID
ORDER BY 
    pk.PackageID, p.Name";

// Execute the query and get the result
$result = $conn->query($sql);

// Initialize an array to classify participants by PackageID
$classifiedParticipants = array();

// Check if we have any results
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        // Group participants by PackageID
        $packageID = $row['PackageID'];

        // Ensure the package ID key exists in the classifiedParticipants array
        if (!isset($classifiedParticipants[$packageID])) {
            $classifiedParticipants[$packageID] = array(
                'PackageName' => $row['PackageName'],
                'Students' => array(),
                'Outsiders' => array()
            );
        }

        // Classify participants based on their Role
        if ($row['Role'] === 'student') {
            $classifiedParticipants[$packageID]['Students'][] = $row;
        } elseif ($row['Role'] === 'outsider') {
            $classifiedParticipants[$packageID]['Outsiders'][] = $row;
        }
    }
} else {
    $classifiedParticipants = array("message" => "No participants found");
}

// Return the data as JSON
echo json_encode($classifiedParticipants);

// Close the database connection
$conn->close();
?>
