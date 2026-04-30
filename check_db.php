<?php
$host = '192.168.90.220';
$user = 'root';
$pass = '';
$db = 'dashboard_pa2';
$port = 3307;

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    echo "=== PERKEMBANGAN DATA ===\n";
    $result = $conn->query('SELECT * FROM perkembangan WHERE nomor_induk_siswa = "0064424163" LIMIT 5');
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "No perkembangan data found\n";
    }
    
    echo "\n=== PERKEMBANGAN KATEGORI DATA ===\n";
    $result = $conn->query('SELECT pk.* FROM perkembangan_kategori pk 
                                  INNER JOIN perkembangan p ON pk.id_perkembangan = p.id_perkembangan
                                  WHERE p.nomor_induk_siswa = "0064424163" LIMIT 10');
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
        }
    } else {
        echo "No perkembangan_kategori data found\n";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
