<?php
include("../config/db.php");
header('Content-Type: application/json');

$msg = $_POST['msg'] ?? "";
$msgLower = strtolower(trim($msg));

if($msg === ""){
    echo json_encode(["reply"=>"Please type your message"]);
    exit;
}

/* =========================
   📊 GLOBAL STATS
========================= */
$total = $conn->query("SELECT COUNT(*) as c FROM concerns")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE status='Submitted'")->fetch_assoc()['c'];
$resolved = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE status='Resolved'")->fetch_assoc()['c'];
$escalated = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE status='Escalated'")->fetch_assoc()['c'];

/* =========================
   🎯 TICKET LOOKUP
========================= */
if(preg_match('/\b(\d{1,6})\b/', $msgLower, $m)){

    $id = $m[1];
    $q = $conn->query("SELECT * FROM concerns WHERE id='$id'");

    if($q && $q->num_rows > 0){
        $r = $q->fetch_assoc();

        $suggestion = "";

        if($r['status'] == "Submitted"){
            $suggestion =
"🛠️ ACTION:
• Assign to department
• Follow up within 24–48 hrs
• Escalate if urgent";
        }
        elseif($r['status'] == "Escalated"){
            $suggestion =
"⏳ ACTION:
• Check escalation office
• Prioritize handling";
        }
        elseif($r['status'] == "Resolved"){
            $suggestion =
"✅ COMPLETED:
• No action needed
• Archive ticket";
        }

        echo json_encode([
            "reply" =>
"📌 Ticket #{$r['id']}
👤 Student: {$r['student_name']}
📂 Category: {$r['category']}
🏢 Assigned: {$r['assigned_to']}
📊 Status: {$r['status']}

$suggestion"
        ]);
        exit;
    }
}

/* =========================
   📂 CATEGORY + DEPARTMENT COUNTER AI
========================= */
if(
    strpos($msgLower, "department") !== false ||
    strpos($msgLower, "category") !== false ||
    strpos($msgLower, "ilan") !== false ||
    strpos($msgLower, "ilang") !== false ||
    strpos($msgLower, "requests") !== false ||
    strpos($msgLower, "academic") !== false ||
    strpos($msgLower, "financial") !== false ||
    strpos($msgLower, "welfare") !== false
){

    /* ================= CATEGORY ================= */
    $academic = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Academic'")->fetch_assoc()['c'];
    $financial = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Financial'")->fetch_assoc()['c'];
    $welfare = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE category='Welfare'")->fetch_assoc()['c'];

    /* ================= DEPARTMENTS ================= */
    $registrar = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Registrar'")->fetch_assoc()['c'];

    $dean = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to=\"Dean's Office\"")->fetch_assoc()['c'];

    $it = $conn->query("
        SELECT COUNT(*) as c FROM concerns 
        WHERE assigned_to LIKE '%IT%' 
        OR assigned_to LIKE '%EDUC%' 
        OR assigned_to LIKE '%HR%' 
        OR assigned_to LIKE '%BSA%'
    ")->fetch_assoc()['c'];

    $cashier = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Cashier'")->fetch_assoc()['c'];

    $accounting = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Accounting'")->fetch_assoc()['c'];

    $scholarship = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Scholarship'")->fetch_assoc()['c'];

    $osa = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='OSA'")->fetch_assoc()['c'];

    $guidance = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Guidance'")->fetch_assoc()['c'];

    $clinic = $conn->query("SELECT COUNT(*) as c FROM concerns WHERE assigned_to='Clinic'")->fetch_assoc()['c'];

    echo json_encode([
        "reply" =>
"📊 COMPLETE TICKET ANALYTICS

📂 CATEGORY TOTALS:
• Academic: $academic
• Financial: $financial
• Welfare: $welfare

🏢 DEPARTMENT TOTALS:
👨‍🎓 Registrar: $registrar
🏫 Dean's Office: $dean
💻 IT / EDUC / HR / BSA: $it

💰 Cashier: $cashier
📊 Accounting: $accounting
🎓 Scholarship: $scholarship

🧑‍⚕️ OSA: $osa
🧠 Guidance: $guidance
🏥 Clinic: $clinic

🧠 Insight:
These are real-time database counts per workload category and department."
    ]);
    exit;
}

/* =========================
   📊 SYSTEM STATUS
========================= */
if(strpos($msgLower, "pending") !== false ||
   strpos($msgLower, "resolved") !== false ||
   strpos($msgLower, "status") !== false){

    echo json_encode([
        "reply" =>
"📊 SYSTEM STATUS:

• Total: $total
• Pending: $pending
• Resolved: $resolved
• Escalated: $escalated"
    ]);
    exit;
}

/* =========================
   👋 GREETING
========================= */
if(strpos($msgLower, "hi") !== false || strpos($msgLower, "hello") !== false){

    echo json_encode([
        "reply" =>
"🤖 Hello! I am your Admin AI Assistant.

You can ask:
• ilan ang pending?
• ticket 23
• department requests
• status ng system"
    ]);
    exit;
}

/* =========================
   ❓ HELP (TAGALOG)
========================= */
if(strpos($msgLower, "paano") !== false){

    echo json_encode([
        "reply" =>
"🧠 GUIDE:
1. Check ticket category
2. Assign department
3. Wait response
4. Update status to Resolved"
    ]);
    exit;
}

/* =========================
   ❌ DEFAULT
========================= */
echo json_encode([
    "reply" =>
"🤖 I can help you with:

📊 Stats:
• pending / resolved / status
• category breakdown
• department workload

🎫 Tickets:
• ticket 23
• assignment info

🏢 Departments:
• Academic / Financial / Welfare

👉 Try a more specific question."
]);