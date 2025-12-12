<?php
header("Content-Type: application/json");

// SETTING DATABASE
$host = "IP_Server_POSTGRESQL";
$port = "5432";
$db   = "HOTSPOTLOG";  // <-- DISINI DIGANTI
$user = "USER_POSTGRE";
$pass = "PASSWORD_POSTGRE";

try {
    // Koneksi ke PostgreSQL
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil JSON dari MikroTik
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["status" => "error", "message" => "No JSON received"]);
        exit;
    }

    // Mapping data
    $id      = $input['id'] ?? null;
    $ip       = $input['ip'] ?? null;
    $mac      = $input['mac'] ?? null;
    $hostname = $input['hostname'] ?? null;
    $session  = $input['session'] ?? null;
    $hotspot       = $input['hotspot'] ?? null;

    // Kolom otomatis
    $month = date("Y-m");
    $type_device = "unknown";
    $unit = "default";

    // Simpan ke database
    $stmt = $pdo->prepare("
        INSERT INTO mykg_login
        (id, ip, mac, hostname, month, type_device, session, hotspot)
        VALUES
        (:id, :ip, :mac, :hostname, :month, :type_device, :session, :hotspot,)
    ");

    $stmt->execute([
        ':id'         => $id,
        ':ip'          => $ip,
        ':mac'         => $mac,
        ':hostname'    => $hostname,
        ':month'       => $month,
        ':type_device' => $type_device,
        ':session'     => $session,
        ':hotspot'          => $hotspot
    ]);

    echo json_encode(["status" => "OK"]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
