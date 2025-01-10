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


-- change data type attendancestatus
ALTER TABLE Attendance
MODIFY AttendanceStatus BOOLEAN NOT NULL;

-- insert data into eventdetails table
INSERT INTO EventDetails (EventName, Description, Location, StartDate, EndDate)
VALUES 
('Glow Paint Run', 'A fun and exciting glow-themed event', 'The Bricks, USM', '2024-12-15 20:00:00', '2024-12-15 23:00:00');


-- -- insert data into package table
-- INSERT INTO Package (PackageName, PackagePrice, PackageCode)
-- VALUES 
-- ('GLOW-RIOUS STARTER', 15.00, 'basic'),
-- ('GLOW-RIOUS LITE', 30.00, 'lite'),
-- ('GLOW-RIOUS PRO', 50.00, 'pro');


-- alter table package
ALTER TABLE Package
    ADD COLUMN description VARCHAR(255) DEFAULT NULL,
    ADD COLUMN image VARCHAR(255) NOT NULL,
    ADD COLUMN features TEXT NOT NULL,
    ADD COLUMN is_popular TINYINT(1) DEFAULT 0,
    ADD COLUMN delay INT DEFAULT 100,
    ADD COLUMN PackageCode VARCHAR(100) NOT NULL;
    MODIFY COLUMN PackageName VARCHAR(255) NOT NULL;

-- insert package data
INSERT INTO Package (PackageName, description, PackagePrice, image, features, is_popular, delay, PackageCode) 
VALUES
('GLOW-RIOUS STARTER', NULL, 15.00, 'assets/img/starterpack.png', 'LED Stick, Refreshments, Drawstring Bag, Face Paint Service, Lucky Draw Ticket, Wristband', 0, 100 , 'basic'),
('GLOW-RIOUS PRO', '* Only this package is open to public *', 50.00, 'assets/img/propack.png', 'T-shirt, LED Stick, Refreshments, Drawstring Bag, Face Paint Service, Lucky Draw Ticket, Wristband, Medal', 1, 200, 'pro'),
('GLOW-RIOUS LITE', NULL, 35.00, 'assets/img/litepack.png', 'T-shirt, LED Stick, Refreshments, Drawstring Bag, Face Paint Service, Lucky Draw Ticket, Wristband', 0, 300, 'lite');


DROP TABLE Payment

CREATE TABLE payments (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_method VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    name_on_card VARCHAR(255) NOT NULL,
    card_number VARCHAR(255) NOT NULL,
    cvv VARCHAR(3) NOT NULL,
    expiration_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




