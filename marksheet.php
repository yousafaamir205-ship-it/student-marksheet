<?php
require_once 'config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Student ID missing.");
}

$stmt = $pdo->prepare("
    SELECT s.roll_number, s.full_name, m.* 
    FROM students s 
    JOIN marks m ON s.id = m.student_id 
    WHERE s.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marksheet - <?= htmlspecialchars($data['full_name']) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="marksheet">
    <h1>Academic Report Card</h1>
    <h3>Session 2026</h3>
    <hr>
    <p><strong>Student Name:</strong> <?= htmlspecialchars($data['full_name']) ?></p>
    <p><strong>Roll Number:</strong> <?= htmlspecialchars($data['roll_number']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Total Marks</th>
                <th>Obtained Marks</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Mathematics</td><td>100</td><td><?= $data['subject_1'] ?></td></tr>
            <tr><td>Physics</td><td>100</td><td><?= $data['subject_2'] ?></td></tr>
            <tr><td>Chemistry</td><td>100</td><td><?= $data['subject_3'] ?></td></tr>
            <tr><td>English</td><td>100</td><td><?= $data['subject_4'] ?></td></tr>
            <tr><td>Computer Science</td><td>100</td><td><?= $data['subject_5'] ?></td></tr>
            <tr>
                <th>Total</th>
                <th>500</th>
                <th><?= $data['total_obtained'] ?></th>
            </tr>
        </tbody>
    </table>

    <br>
    <p><strong>Percentage:</strong> <?= $data['percentage'] ?>%</p>
    <p><strong>GPA:</strong> <?= $data['gpa'] ?></p>
    <p><strong>Status:</strong> <span class="<?= $data['status'] === 'PASS' ? 'status-pass' : 'status-fail' ?>"><?= $data['status'] ?></span></p>
</div>

<div class="btn-print">
    <button onclick="window.print()" style="max-width: 200px;">Print Marksheet</button>
    <br><br>
    <a href="index.php" style="color: #007bff; text-decoration: none;">&larr; Back to Entry Form</a>
</div>

</body>
</html>