<?php
include '../db.php'; // Include database connection

header('Content-Type: application/json');

// Retrieve the input data (QR Code) from the POST request
$input = json_decode(file_get_contents('php://input'), true);
$qrCode = $input['qrCode'] ?? ''; // Use null coalescing to assign an empty string if 'qrCode' is not provided

if (!empty($qrCode)) {
    // Extract the IC number from the QR code content
    // Assuming the QR code format is: "Name: Anis IC: 12"
    preg_match('/IC: (\d+)/', $qrCode, $matches);
    $ic = $matches[1] ?? null; // Extract the IC number (e.g., "12")

    if ($ic) {
        // Construct the QR code file path (e.g., "qrcodes/12.png")
        $qrCodeFilePath = "qrcodes/" . $ic . ".png";

        // Query the database using the constructed file path
        $stmt = $conn->prepare("
            SELECT 
                p.ParticipantID, 
                p.Name, 
                pkg.PackageName 
            FROM 
                Participant p 
            LEFT JOIN 
                Package pkg 
            ON 
                p.PackageID = pkg.PackageID 
            WHERE 
                p.QRCodeStu = ?
        ");
        $stmt->bind_param("s", $qrCodeFilePath); // Bind the constructed file path
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $participant = $result->fetch_assoc();

            // Mark attendance in the Attendance table
            $stmt = $conn->prepare("INSERT INTO Attendance (AttendanceStatus, ScanTime, ParticipantID) VALUES (1, NOW(), ?)");
            $stmt->bind_param("i", $participant['ParticipantID']);
            $stmt->execute();

            // Send a success response with participant details and package name
            echo json_encode([
                'success' => true,
                'message' => 'Attendance confirmed successfully!',
                'participantName' => $participant['Name'],
                'packageName' => $participant['PackageName'] // Fetched from the Package table
            ]);
        } else {
            // Send a failure response if the QR code is not found
            echo json_encode(['success' => false, 'message' => 'QR code not found in the database.']);
        }

        $stmt->close();
    } else {
        // Send a failure response if IC number extraction fails
        echo json_encode(['success' => false, 'message' => 'Invalid QR code format.']);
    }
} else {
    // Send a failure response if the QR code is empty
    echo json_encode(['success' => false, 'message' => 'No QR code data provided.']);
}

$conn->close();
