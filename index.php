<?php
require_once 'config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll_number = trim($_POST['roll_number']);
    $full_name   = trim($_POST['full_name']);
    
    $sub1 = (int)$_POST['sub1'];
    $sub2 = (int)$_POST['sub2'];
    $sub3 = (int)$_POST['sub3'];
    $sub4 = (int)$_POST['sub4'];
    $sub5 = (int)$_POST['sub5'];

    // Calculations
    $total_obtained = $sub1 + $sub2 + $sub3 + $sub4 + $sub5;
    $percentage     = ($total_obtained / 500) * 100;

    // Determine GPA
    if ($percentage >= 80)     $gpa = 4.0;
    elseif ($percentage >= 70) $gpa = 3.5;
    elseif ($percentage >= 60) $gpa = 3.0;
    elseif ($percentage >= 50) $gpa = 2.0;
    else                       $gpa = 0.0;

    // Passing criterion: >= 40 marks in every subject
    $status = ($sub1 >= 40 && $sub2 >= 40 && $sub3 >= 40 && $sub4 >= 40 && $sub5 >= 40) ? 'PASS' : 'FAIL';

    try {
        $pdo->beginTransaction();

        // 1. Insert Student Info
        $stmt1 = $pdo->prepare("INSERT INTO students (roll_number, full_name) VALUES (?, ?)");
        $stmt1->execute([$roll_number, $full_name]);
        $student_id = $pdo->lastInsertId();

        // 2. Insert Marks & Results
        $stmt2 = $pdo->prepare("INSERT INTO marks (student_id, subject_1, subject_2, subject_3, subject_4, subject_5, total_obtained, percentage, gpa, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt2->execute([$student_id, $sub1, $sub2, $sub3, $sub4, $sub5, $total_obtained, $percentage, $gpa, $status]);

        $pdo->commit();
        $message = "Record saved successfully! <a href='marksheet.php?id={$student_id}' style='font-weight:bold; color:#166534;'>View Marksheet &rarr;</a>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Error saving record: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grade Calculator</title>
    <link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>

<div class="navbar">
    <div><strong>Academic Portal</strong></div>
    <div>
        <a href="index.php">Add New Record</a>
        <a href="records.php">View All Records</a>
    </div>
</div>

<div class="card">
    <h2>Student Marksheet Entry</h2>
    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Roll Number</label>
            <input type="text" name="roll_number" placeholder="e.g., CS-2026-01" required>
        </div>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter student full name" required>
        </div>
        
        <h4 style="margin-top: 25px; color: #1e293b;">Subject Marks (Out of 100)</h4>
        <div class="form-group"><label>Mathematics</label><input type="number" name="sub1" min="0" max="100" required></div>
        <div class="form-group"><label>Physics</label><input type="number" name="sub2" min="0" max="100" required></div>
        <div class="form-group"><label>Chemistry</label><input type="number" name="sub3" min="0" max="100" required></div>
        <div class="form-group"><label>English</label><input type="number" name="sub4" min="0" max="100" required></div>
        <div class="form-group"><label>Computer Science</label><input type="number" name="sub5" min="0" max="100" required></div>
        
        <button type="submit">Calculate & Save</button>
    </form>
</div>

</body>
</html>