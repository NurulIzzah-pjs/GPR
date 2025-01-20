<?php
include '../db.php'; // Include database connection

header('Content-Type: application/json');

// Retrieve and decode the QR code JSON data
$input = json_decode(file_get_contents('php://input'), true);
$qrCode = json_decode($input['qrCode'], true); // Decode JSON from QR code
$data = $qrCode['data']; // Extract the data
$signature = base64_decode($qrCode['signature']); // Decode the Base64-encoded signature

// Debug: Print the QR code data and signature
error_log("QR Code Data: " . $data);
error_log("QR Code Signature: " . $qrCode['signature']);

// Load the public key for verification
$publicKeyPath = '../keys/public.pem';
if (!file_exists($publicKeyPath)) {
    error_log("Error: Public key file not found at: " . $publicKeyPath);
    echo json_encode(['success' => false, 'message' => 'Public key file not found.']);
    exit();
}

// Read public key from file into string variable for signature verification
$publicKey = file_get_contents($publicKeyPath);

// Verify the signature using public key
$isValid = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);

// Hnadle verification results
if ($isValid === 1) {
    error_log("Signature is valid.");
} elseif ($isValid === 0) {
    error_log("Invalid signature.");
    echo json_encode(['success' => false, 'message' => 'Invalid signature. QR code is not authentic.']);
    exit();
} else {
    error_log("Error verifying signature.");
    echo json_encode(['success' => false, 'message' => 'Error verifying the signature.']);
    exit();
}

// Extract the IC number from the QR code content
preg_match('/IC: (\d+)/', $data, $matches);
$ic = $matches[1] ?? null; // Extract the IC number (e.g., "12345")

// Debug: Output the extracted IC number
if ($ic) {
    error_log("Extracted IC: " . $ic);
} else {
    error_log("Failed to extract IC from data: " . $data);
    echo json_encode(['success' => false, 'message' => 'Invalid QR code data.']);
    exit();
}

// Construct the QR code file path
$qrCodePath = "qrcodes/" . $ic . ".png";

// Debug: Output the constructed QR code path
error_log("Constructed QR Code Path: " . $qrCodePath);

// Query the database for participant information
$stmt = $conn->prepare("
    SELECT 
        p.ParticipantID, 
        p.Name, 
        pkg.PackageName,
        p.QRCodeStu
    FROM 
        participant p 
    LEFT JOIN 
        package pkg 
    ON 
        p.PackageID = pkg.PackageID 
    WHERE 
        p.QRCodeStu = ?
");
$stmt->bind_param("s", $qrCodePath);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $participant = $result->fetch_assoc();

    // Debug: Output the stored QR code path
    error_log("Stored QRCodeStu Path: " . $participant['QRCodeStu']);

    // Check if the scanned QR code matches the stored QRCodeStu path
    if ($qrCodePath === $participant['QRCodeStu']) {
        // Mark attendance in the Attendance table
        $stmt = $conn->prepare("INSERT INTO attendance (AttendanceStatus, ScanTime, ParticipantID) VALUES (1, NOW(), ?)");
        $stmt->bind_param("i", $participant['ParticipantID']);
        $stmt->execute();

        // Send a success response with participant details and package name
        echo json_encode([
            'success' => true,
            'message' => 'Attendance confirmed successfully!',
            'participantName' => $participant['Name'],
            'packageName' => $participant['PackageName']
        ]);
    } else {
        error_log("QR Code path mismatch. Expected: $qrCodePath, Found: " . $participant['QRCodeStu']);
        echo json_encode(['success' => false, 'message' => 'QR code path mismatch.']);
    }
} else {
    error_log("Participant not found for QR Code Path: " . $qrCodePath);
    echo json_encode(['success' => false, 'message' => 'Participant not found.']);
}

$conn->close();
?>
