<?php
include 'test.php';

try {
    // Show tables
    $stmt = $mysqlClient->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in database 'kart':\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    echo "\n";

    // Describe each table
    foreach ($tables as $table) {
        echo "Structure of table '$table':\n";
        $stmt = $mysqlClient->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})" . ($col['Null'] == 'NO' ? ' NOT NULL' : '') . ($col['Key'] ? " {$col['Key']}" : '') . ($col['Default'] !== null ? " DEFAULT '{$col['Default']}'" : '') . ($col['Extra'] ? " {$col['Extra']}" : '') . "\n";
        }
        echo "\n";

        // Show sample data (first 5 rows)
        echo "Sample data from '$table' (first 5 rows):\n";
        $stmt = $mysqlClient->query("SELECT * FROM $table LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            foreach ($rows as $row) {
                echo "  " . json_encode($row) . "\n";
            }
        } else {
            echo "  (No data)\n";
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>