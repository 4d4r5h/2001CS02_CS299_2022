<?php
    $filename = 'current_allocation.csv';
    $export_data = unserialize($_POST['export_data']);

    // File creation
    $file = fopen($filename, "w");

    foreach ($export_data as $line) {
        fputcsv($file, $line);
    }

    fclose($file);

    // Download
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=" . $filename);
    header("Content-Type: application/csv; ");

    readfile($filename);

    // Deleting file
    unlink($filename);

    exit();
?>
