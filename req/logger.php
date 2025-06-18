<?php
function logAction($conn, $user_id, $role, $action, $description) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';

    $stmt = $conn->prepare("INSERT INTO system_logs (user_id, role, action, description, ip_address, user_agent)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $role, $action, $description, $ip, $agent]);
}
?>
