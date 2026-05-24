<?php
include 'db_config.php';

echo "Database: " . $db . "\n";

$res = mysqli_query($conn, 'SHOW TABLES');
if (!$res) {
    die("Error showing tables: " . mysqli_error($conn));
}

while($row = mysqli_fetch_array($res)) {
    $table = $row[0];
    echo "Checking Table: $table\n";
    
    $check = mysqli_query($conn, "CHECK TABLE $table");
    if ($check) {
        $status = mysqli_fetch_assoc($check);
        echo "  - Op: " . $status['Op'] . " | Status: " . $status['Msg_text'] . "\n";
    } else {
        echo "  - Error running CHECK TABLE: " . mysqli_error($conn) . "\n";
    }
    
    // Also try a simple count
    $count_res = mysqli_query($conn, "SELECT COUNT(*) FROM $table");
    if ($count_res) {
        $count = mysqli_fetch_array($count_res)[0];
        echo "  - Row count: $count\n";
    } else {
        echo "  - Error selecting: " . mysqli_error($conn) . "\n";
    }
    echo "--------------------\n";
}
?>
