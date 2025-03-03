CREATE DATABASE shenieva_DB;
USE shenieva_DB;

CREATE TABLE teacher_table (
    pk_teacherID INT AUTO_INCREMENT PRIMARY KEY,
    teacherName VARCHAR(255) NOT NULL,
    teacherEmail VARCHAR(255) UNIQUE NOT NULL,
    teacherPass VARCHAR(255) NOT NULL,
    teacherGender ENUM('Male', 'Female', 'Other') NOT NULL
);

CREATE TABLE students_table (
    pk_studentID INT AUTO_INCREMENT PRIMARY KEY,
    studentName VARCHAR(255) NOT NULL,
    studentPass VARCHAR(255) NOT NULL,
    studentGender ENUM('Male', 'Female', 'Other') NOT NULL,
    studentLevel INT NOT NULL,
    studentRibbon VARCHAR(255),
    studentColtrash VARCHAR(255),
    studentProgress VARCHAR(255)
);

CREATE TABLE story1_table (
    pk_storyQuiz1ID INT AUTO_INCREMENT PRIMARY KEY,
    storyQuiz1 TEXT NOT NULL,
    answer BOOLEAN NOT NULL,
    choices TEXT NOT NULL,
    points INT NOT NULL
);

CREATE TABLE story2_table (
    pk_storyQuiz1ID INT AUTO_INCREMENT PRIMARY KEY,
    storyQuiz1 TEXT NOT NULL,
    answer VARCHAR(255) NOT NULL,
    points INT NOT NULL
);

CREATE TABLE story3_table (
    pk_storyQuiz1ID INT AUTO_INCREMENT PRIMARY KEY,
    storyQuiz1 TEXT NOT NULL,
    answer VARCHAR(255) NOT NULL,
    points INT NOT NULL
);

CREATE TABLE attendance_table (
    pk_attendanceID INT AUTO_INCREMENT PRIMARY KEY,
    fk_studentID INT NOT NULL,
    attendanceDateTime DATETIME NOT NULL,
    FOREIGN KEY (fk_studentID) REFERENCES students_table(pk_studentID) ON DELETE CASCADE
);

CREATE TABLE quiz1_table (
    pk_quiz1ID INT AUTO_INCREMENT PRIMARY KEY,
    fk_studentID INT NOT NULL,
    fk_storyQuiz1ID INT NOT NULL,
    studentAnswer TEXT NOT NULL,
    studentAttempts INT NOT NULL,
    studentPoints INT NOT NULL,
    FOREIGN KEY (fk_studentID) REFERENCES students_table(pk_studentID) ON DELETE CASCADE,
    FOREIGN KEY (fk_storyQuiz1ID) REFERENCES story1_table(pk_storyQuiz1ID) ON DELETE CASCADE
);

CREATE TABLE quiz2_table (
    pk_quiz2ID INT AUTO_INCREMENT PRIMARY KEY,
    fk_studentID INT NOT NULL,
    fk_storyQuiz1ID INT NOT NULL,
    studentAnswer TEXT NOT NULL,
    studentAttempts INT NOT NULL,
    studentPoints INT NOT NULL,
    FOREIGN KEY (fk_studentID) REFERENCES students_table(pk_studentID) ON DELETE CASCADE,
    FOREIGN KEY (fk_storyQuiz1ID) REFERENCES story2_table(pk_storyQuiz1ID) ON DELETE CASCADE
);

CREATE TABLE quiz3_table (
    pk_quiz3ID INT AUTO_INCREMENT PRIMARY KEY,
    fk_studentID INT NOT NULL,
    fk_storyQuiz1ID INT NOT NULL,
    studentAnswer TEXT NOT NULL,
    studentAttempts INT NOT NULL,
    studentPoints INT NOT NULL,
    FOREIGN KEY (fk_studentID) REFERENCES students_table(pk_studentID) ON DELETE CASCADE,
    FOREIGN KEY (fk_storyQuiz1ID) REFERENCES story3_table(pk_storyQuiz1ID) ON DELETE CASCADE
);
