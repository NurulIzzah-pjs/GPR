CREATE TABLE Admin (
    AdminID INT PRIMARY KEY AUTO_INCREMENT,
    AdminName VARCHAR(100) NOT NULL,
    AdminUsername VARCHAR(50) NOT NULL UNIQUE,
    AdminPassword VARCHAR(255) NOT NULL
);

CREATE TABLE Package (
    PackageID INT PRIMARY KEY AUTO_INCREMENT,
    PackageName ENUM('basic', 'lite','pro') NOT NULL,
    PackagePrice DECIMAL(10, 2) NOT NULL
);

CREATE TABLE Participant (
    ParticipantID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    PhoneNum VARCHAR(15) NOT NULL,
    IdentificationNum VARCHAR(200) NOT NULL,
    Role ENUM('student', 'outsider') NOT NULL,
    MatricNum VARCHAR(50) NULL,
    Campus ENUM('induk', 'kejut','kesit') NULL,
    School VARCHAR(100) NULL,
    PackageName ENUM('basic', 'lite','pro'),
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    PackageID INT,
    QRCodeStu VARCHAR(255),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID)
);


CREATE TABLE Attendance (
    AttendanceID INT PRIMARY KEY AUTO_INCREMENT,
    AttendanceStatus VARCHAR(20) NOT NULL,
    ScanTime DATETIME NOT NULL,
    ParticipantID INT NOT NULL,
    AdminID INT NOT NULL,
    FOREIGN KEY (ParticipantID) REFERENCES Participant(ParticipantID),
    FOREIGN KEY (AdminID) REFERENCES Admin(AdminID)
);

CREATE TABLE Payment (
    PaymentID INT PRIMARY KEY AUTO_INCREMENT,
    PaymentDate DATETIME NOT NULL,
    PaymentMethod VARCHAR(50) NOT NULL,
    TotalAmount DECIMAL(10, 2) NOT NULL,
    ParticipantID INT NOT NULL,
    PackageID INT NOT NULL,
    FOREIGN KEY (ParticipantID) REFERENCES Participant(ParticipantID),
    FOREIGN KEY (PackageID) REFERENCES Package(PackageID)
);

CREATE TABLE EventDetails (
    EventID INT PRIMARY KEY AUTO_INCREMENT,
    EventName VARCHAR(255) NOT NULL,
    Description TEXT,
    Location VARCHAR(255) NOT NULL,
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    AdminID INT NOT NULL,
    FOREIGN KEY (AdminID) REFERENCES Admin(AdminID)
);

CREATE TABLE Schedules (
    ScheduleID INT PRIMARY KEY AUTO_INCREMENT,
    EventID INT NOT NULL,
    ActivityName VARCHAR(255) NOT NULL,
    ActivityStart DATETIME NOT NULL,
    ActivityEnd DATETIME NOT NULL,
    Location VARCHAR(255),
    FOREIGN KEY (EventID) REFERENCES EventDetails(EventID)
);

--------------------------------------------------------

-- change data type
ALTER TABLE Package
MODIFY PackageName VARCHAR(50);

-- remove column package name
ALTER TABLE Participant
DROP COLUMN PackageName;

-- add registration date column
ALTER TABLE Participant
ADD COLUMN RegistrationDate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- remove adminID from attendance table
ALTER TABLE Attendance
DROP FOREIGN KEY attendance_ibfk_2;

ALTER TABLE Attendance
DROP COLUMN AdminID;

-- remove adminID from eventsdetails table
ALTER TABLE eventdetails
DROP FOREIGN KEY eventdetails_ibfk_1;

ALTER TABLE eventdetails
DROP COLUMN AdminID;

-- remove eventID from schedules
ALTER TABLE schedules
DROP FOREIGN KEY schedules_ibfk_1;

ALTER TABLE schedules 
DROP COLUMN EventID;

--insert data into schedules table
INSERT INTO Schedules (ActivityName, ActivityStart, ActivityEnd, Location)
VALUES
('Registration Opens', '2024-12-14 19:30:00', '2024-12-14 20:00:00', 'In front of The Brick'),
('Warm Up', '2024-12-14 20:00:00', '2024-12-14 20:15:00', 'In front of The Brick'),
('Flag Off', '2024-12-14 20:15:00', '2024-12-14 20:20:00', 'Starting Line'),
('Participant Finish', '2024-12-14 22:00:00', '2024-12-14 22:15:00', 'Finish Line'),
('Medal Distribution', '2024-12-14 22:15:00', '2024-12-14 22:30:00', 'Medal Counter');
