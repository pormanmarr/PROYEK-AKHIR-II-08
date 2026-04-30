<?php
$host = '192.168.90.220';
$port = 3307;
$db = 'dashboard_pa2';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset("utf8");

// Check if template_deskripsi is now populated
$query = "SELECT p.id_perkembangan, p.status_utama, p.template_deskripsi 
          FROM perkembangan p 
          WHERE p.nomor_induk_siswa = '0064424163' 
          LIMIT 1";

$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "ID: " . $row['id_perkembangan'] . "\n";
    echo "Status: " . $row['status_utama'] . "\n";
    echo "Template: " . substr($row['template_deskripsi'], 0, 80) . "...\n";
} else {
    echo "No records found\n";
}

$conn->close();
?>
