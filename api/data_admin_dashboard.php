<?php
include '../db.php'; // Include your database connection

header("Content-Type: application/json"); // Ensure the response is JSON

// Get the type of data requested
$type = isset($_GET['type']) ? $_GET['type'] : null;

if ($type === 'line') {
    // Fetch data for the Line Graph (Participant Registration Trend)
    $sql = "SELECT DATE_FORMAT(RegistrationDate, '%d.%m.%Y') as RegistrationDate, COUNT(*) as DailyCount 
            FROM participant 
            GROUP BY DATE(RegistrationDate)
            ORDER BY RegistrationDate ASC";
    $result = $conn->query($sql);

    $data = array();
    $cumulativeTotal = 0;

    while ($row = $result->fetch_assoc()) {
        $cumulativeTotal += $row['DailyCount']; // Add the daily count to the cumulative total
        $data[] = [
            'RegistrationDate' => $row['RegistrationDate'], // Formatted as day.month.year
            'CumulativeCount' => $cumulativeTotal
        ];
    }

    echo json_encode($data); // Return the cumulative data as JSON
}



elseif ($type === 'bar') {
    // Fetch data for the Bar Graph (Registrations by Ticket Type)
    $sql = "SELECT p.PackageName, COUNT(pt.ParticipantID) as ParticipantCount 
            FROM package p
            LEFT JOIN participant pt ON p.PackageID = pt.PackageID
            GROUP BY p.PackageName";
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);

} elseif ($type === 'pie') {
    $data = array();
    
    // Fetch total registrations
    $sqlRegistrations = "SELECT COUNT(*) as TotalRegistrations FROM participant";
    $resultRegistrations = $conn->query($sqlRegistrations);
    $totalRegistrations = $resultRegistrations->fetch_assoc()['TotalRegistrations'];

    // Fetch total attendance
    $sqlAttendance = "SELECT COUNT(*) as TotalAttendance FROM attendance WHERE AttendanceStatus = 1"; // Assuming 1 means "present"
    $resultAttendance = $conn->query($sqlAttendance);
    $totalAttendance = $resultAttendance->fetch_assoc()['TotalAttendance'];

    // Calculate percentages
    $data[] = [
        'Label' => 'Registered',
        'Count' => $totalRegistrations,
        'Percentage' => $totalRegistrations > 0 ? ($totalRegistrations / $totalRegistrations) * 100 : 0
    ];

    $data[] = [
        'Label' => 'Attended',
        'Count' => $totalAttendance,
        'Percentage' => $totalRegistrations > 0 ? ($totalAttendance / $totalRegistrations) * 100 : 0
    ];

    echo json_encode($data);
}

elseif ($type === 'schedule') {
    // Fetch data for the Event Schedule
    $sql = "SELECT DATE_FORMAT(ActivityStart, '%h:%i %p') AS StartTime, ActivityName, Location FROM schedules ORDER BY ActivityStart ASC";
    $result = $conn->query($sql);

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} 

elseif ($type === 'eventDetails') {
    // Fetch data for the Event Details
    $sql = "SELECT EventName, DATE_FORMAT(StartDate, '%d %M %Y') as EventDate, 
            TIME_FORMAT(StartDate, '%h.%i %p') as StartTime, 
            TIME_FORMAT(EndDate, '%h.%i %p') as EndTime, 
            Location 
            FROM eventdetails
            ORDER BY EventID DESC LIMIT 1"; // Get the most recent event
    $result = $conn->query($sql);

    $data = array();
    if ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    echo json_encode($data);
}

elseif ($type === 'totalParticipants') {
    // Fetch the total number of participants
    $sql = "SELECT COUNT(*) as TotalParticipants FROM participant";
    $result = $conn->query($sql);

    $data = array();
    if ($row = $result->fetch_assoc()) {
        $data['TotalParticipants'] = $row['TotalParticipants'];
    }

    echo json_encode($data); // Return the total participants as JSON
}


else {
    // If no valid type is provided, return an error message
    echo json_encode(["error" => "Invalid or missing 'type' parameter."]);
}

$conn->close();
?>
