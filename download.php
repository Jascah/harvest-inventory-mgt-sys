<?php
include 'rolecheck.php';
checkRole(['admin','manager']);
include 'connection.php'; // Ensure this file contains $con for the database connection
include 'dbconnection.php';

// Set headers for file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=inventory_report.csv');

// Open the output stream
$output = fopen('php://output', 'w');

// Write the column headers for the CSV
fputcsv($output, ['Item ID', 'Harvest Type', 'Quantity', 'Silo Location', 'Date Added']);

// Fetch inventory data from the database
$query = "SELECT id, harvest_type, quantity, silo_location, date_added FROM storage"; // Replace with your actual table name
$result = mysqli_query($con, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Write each row to the CSV
        fputcsv($output, $row);
    }
} else {
    // If query fails, output error
    fputcsv($output, ['Error fetching data from database']);
}

// Close database connection
mysqli_close($con);
?>
