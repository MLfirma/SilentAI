<?php
include("../config/db.php");

/* =========================
   PHPMailer LOAD (NO COMPOSER)
========================= */
require("../PHPMailer/PHPMailer.php");
require("../PHPMailer/SMTP.php");
require("../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_POST) {

    $category = $_POST['category'];

    /* =========================
       ROUTING LOGIC
    ========================= */
    if ($category == "Academic") {
        $department = "Registrar";
    } 
    elseif ($category == "Financial") {
        $department = "Accounting";
    } 
    elseif ($category == "Welfare") {
        $department = "OSA";
    } 
    elseif ($category == "Guidance" || $category == "Counseling") {
        $department = "Guidance";
    } 
    elseif ($category == "Medical" || $category == "Clinic") {
        $department = "Clinic";
    } 
    else {
        $department = "OSA";
    }

    /* =========================
       FILE UPLOAD
    ========================= */
    $fileName = "";
    if (!empty($_FILES['file']['name'])) {
        $fileName = time() . "_" . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/" . $fileName);
    }

    /* =========================
       ANONYMOUS HANDLING
    ========================= */
    $anon = isset($_POST['anon']) ? 1 : 0;

    if ($anon == 1) {
        $name = "Anonymous";
        $email = "anonymous@system.local";
    } else {
        $name = $_POST['name'];
        $email = $_POST['email'];
    }

    $status = "Submitted";

    /* =========================
       INSERT DATABASE
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO concerns 
        (student_name, email, category, description, attachment, is_anonymous, department, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssssiss",
        $name,
        $email,
        $category,
        $_POST['description'],
        $fileName,
        $anon,
        $department,
        $status
    );

    $stmt->execute();

    /* =========================
       GET TICKET ID (IMPORTANT FIX)
    ========================= */
    $ticket_id = $stmt->insert_id;

    $trackLink = "http://localhost/student-concern-system/track.php?id=" . $ticket_id;

    /* =========================
       EMAIL NOTIFICATION
    ========================= */
    if ($email != "anonymous@system.local") {

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = 'firmamarylloyd@gmail.com';
            $mail->Password = 'xrwavvnt rvvmgqmr';

            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('firmamarylloyd@gmail.com', 'Student Concern System');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "📩 Concern Submitted Successfully";

            /* =========================
               FIXED EMAIL BODY (WITH TICKET ID)
            ========================= */
            $mail->Body = "
                <h3>Hello $name,</h3>

                <p>Your concern has been submitted successfully.</p>

                <hr>

                <p><b>🎫 Ticket ID:</b> #$ticket_id</p>
                <p><b>📌 Category:</b> $category</p>
                <p><b>🏢 Assigned Department:</b> $department</p>
                <p><b>📊 Status:</b> $status</p>

                <br>

                <p>
                    🔎 Track your concern here:<br>
                    <a href='$trackLink'>Click to Track Ticket</a>
                </p>

                <br>

                <small>Please save your Ticket ID: <b>#$ticket_id</b></small>
            ";

            $mail->send();

        } catch (Exception $e) {
            // silent fail
        }
    }

    /* =========================
       SUCCESS RESPONSE
    ========================= */
    echo "
    <script>
        alert('Concern Submitted Successfully!');
        window.location='submit.php';
    </script>

    <div style='text-align:center;margin-top:15px;'>
        <a href='$trackLink'
           style='padding:10px 18px;background:#007bff;color:#fff;text-decoration:none;border-radius:5px;'>
           🔎 Track My Concern
        </a>
    </div>
    ";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Concern Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
    </style>
</head>

<body>

<div class="container mt-5" style="max-width:650px;">

    <div class="card shadow p-4">

        <h3 class="text-center mb-4">📩 Student Concern Form</h3>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="name" class="form-control mb-3" placeholder="Your Name (optional)">

            <input type="email" name="email" class="form-control mb-3" placeholder="Email (optional)">

            <select name="category" class="form-select mb-3" required>
                <option value="Academic">Academic</option>
                <option value="Financial">Financial</option>
                <option value="Welfare">Welfare</option>
                <option value="Guidance">Guidance / Counseling</option>
                <option value="Medical">Medical / Clinic</option>
            </select>

            <textarea name="description" class="form-control mb-3" rows="4" required></textarea>

            <input type="file" name="file" class="form-control mb-3">

            <div class="form-check mb-3">
                <input type="checkbox" name="anon" class="form-check-input">
                <label class="form-check-label">Submit as Anonymous</label>
            </div>

            <button class="btn btn-primary w-100">Submit Concern</button>

        </form>

        <div class="text-center mt-3">
            <a href="http://localhost/student-concern-system/track.php"
               class="btn btn-outline-dark">
               🔎 Track My Concern
            </a>
        </div>

    </div>

</div>

</body>
</html>