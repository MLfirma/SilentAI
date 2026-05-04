<?php
include("../config/db.php");

/* prevent caching para realtime talaga */
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/html; charset=UTF-8");

/* get latest data (important: updated_at first) */
$res = $conn->query("SELECT * FROM concerns ORDER BY updated_at DESC, created_at DESC");

while($row = $res->fetch_assoc()):
?>

<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['student_name'] ?></td>
    <td><?= $row['email'] ?></td>

    <td><?= $row['category'] ?></td>

    <!-- 🔥 REALTIME ASSIGNED TO -->
    <td>
        <span class="badge bg-info">
            <?= !empty($row['assigned_to']) ? $row['assigned_to'] : 'Not Assigned' ?>
        </span>
    </td>

    <!-- STATUS -->
    <td>
        <?php if($row['status'] == 'Escalated'): ?>
            <span class="badge bg-danger">Escalated</span>

        <?php elseif($row['status'] == 'Submitted'): ?>
            <span class="badge bg-primary">Submitted</span>

        <?php elseif($row['status'] == 'Resolved'): ?>
            <span class="badge bg-success">Resolved</span>

        <?php else: ?>
            <span class="badge bg-secondary"><?= $row['status'] ?></span>
        <?php endif; ?>
    </td>

    <td><?= $row['created_at'] ?></td>
</tr>

<?php endwhile; ?>