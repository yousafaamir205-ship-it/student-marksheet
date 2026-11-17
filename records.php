<?php
require_once 'config/db.php';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: records.php?deleted=1");
    exit;
}

// Handle Search Filter
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT s.id, s.roll_number, s.full_name, m.total_obtained, m.percentage, m.gpa, m.status 
        FROM students s 
        JOIN marks m ON s.id = m.student_id 
        WHERE s.full_name LIKE ? OR s.roll_number LIKE ?
        ORDER BY s.id DESC
    ");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("
        SELECT s.id, s.roll_number, s.full_name, m.total_obtained, m.percentage, m.gpa, m.status 
        FROM students s 
        JOIN marks m ON s.id = m.student_id 
        ORDER BY s.id DESC
    ");
}
$records = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records Directory</title>
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

<div class="card card-wide">
    <h2>Student Records Directory</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="msg" style="background:#fee2e2; border-color:#fca5a5; color:#991b1b;">Student record deleted.</div>
    <?php endif; ?>

    <!-- Search Bar -->
    <form method="GET" action="" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search by Roll Number or Name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" style="width: auto; padding: 10px 20px;">Search</button>
        <?php if ($search !== ''): ?>
            <a href="records.php" class="btn" style="width: auto; background: #64748b; padding: 10px 15px;">Reset</a>
        <?php endif; ?>
    </form>

    <?php if (count($records) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Student Name</th>
                    <th>Total (500)</th>
                    <th>Percentage</th>
                    <th>GPA</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['roll_number']) ?></strong></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= $row['total_obtained'] ?></td>
                        <td><?= $row['percentage'] ?>%</td>
                        <td><?= $row['gpa'] ?></td>
                        <td>
                            <span class="<?= $row['status'] === 'PASS' ? 'status-pass' : 'status-fail' ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="marksheet.php?id=<?= $row['id'] ?>" class="btn btn-sm">Marksheet</a>
                            <a href="records.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record permanently?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color: #64748b; text-align: center; padding: 20px;">No student records found.</p>
    <?php endif; ?>
</div>

</body>
</html>