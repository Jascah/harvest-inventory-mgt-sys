<?php
include 'dbconnection.php';
include 'connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check the action type
    $action = isset($_POST['action']) ? $_POST['action'] : 'dispatch';

    if ($action === 'dispatch') {
        // Handle dispatch operation
        $harvest_type = $_POST['harvest_type'];
        $silo_location = $_POST['silo_location'];
        $quantity = intval($_POST['quantity']);
        $recipient = $_POST['recipient'];

        // Fetch current quantity from storage
        $query = "SELECT quantity FROM storage WHERE harvest_type = ? AND silo_location = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $harvest_type, $silo_location);
        $stmt->execute();
        $result = $stmt->get_result();
        $storage_data = $result->fetch_assoc();

        if ($storage_data && $storage_data['quantity'] >= $quantity) {
            // Update storage table
            $new_quantity = $storage_data['quantity'] - $quantity;
            $update_query = "UPDATE storage SET quantity = ? WHERE harvest_type = ? AND silo_location = ?";
            $update_stmt = $con->prepare($update_query);
            $update_stmt->bind_param("iss", $new_quantity, $harvest_type, $silo_location);
            $update_stmt->execute();

            // Insert into dispatch log
            $dispatch_query = "INSERT INTO dispatch_log (harvest_type, silo_location, quantity, recipient, dispatch_date) VALUES (?, ?, ?, ?, NOW())";
            $dispatch_stmt = $con->prepare($dispatch_query);
            $dispatch_stmt->bind_param("ssis", $harvest_type, $silo_location, $quantity, $recipient);
            $dispatch_stmt->execute();

            echo json_encode(["status" => "success", "message" => "Dispatch successful!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Not enough quantity in storage."]);
        }
        exit;
    } elseif ($action === 'generate_report') {
        // Handle dispatch report generation
        $query = "SELECT harvest_type, silo_location, quantity, recipient, DATE_FORMAT(dispatch_date, '%Y-%m-%d %H:%i:%s') AS dispatch_date FROM dispatch_log";

        $result = mysqli_query($con, $query);

        if (!$result) {
            echo json_encode(["status" => "error", "message" => "Failed to fetch dispatch log data."]);
            exit;
        }

        // Create CSV file
        $filename = "dispatch_report_" . date('Y-m-d') . ".csv";
        $file = fopen($filename, 'w');

        // Add CSV headers
        fputcsv($file, ['Harvest Type', 'Silo Location', 'Quantity', 'Recipient', 'Dispatch Date']);

        // Add data rows
        while ($row = mysqli_fetch_assoc($result)) {
            $row['dispatch_date']=(string)$row['dispatch_date'];
            fputcsv($file, $row);
        }

        fclose($file);

        // Download the file
        header('Content-Description: File Transfer');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        readfile($filename);

        unlink($filename); // Clean up temporary file
        exit();
    }
    elseif ($action === 'view_dispatch') {
        $query = "SELECT harvest_type, silo_location, quantity, recipient, dispatch_date FROM dispatch_log ORDER BY dispatch_date DESC";
        $result = mysqli_query($con, $query);

        if (!$result) {
            echo json_encode(["status" => "error", "message" => "Failed to fetch dispatch records."]);
            exit;
        }

        // Generate HTML table
        $table = '<table class="styled-table">
                    <thead>
                        <tr>
                            <th>Harvest Type</th>
                            <th>Silo Location</th>
                            <th>Quantity</th>
                            <th>Recipient</th>
                            <th>Dispatch Date</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = mysqli_fetch_assoc($result)) {
            $table .= '<tr>
                        <td>' . htmlspecialchars($row['harvest_type']) . '</td>
                        <td>' . htmlspecialchars($row['silo_location']) . '</td>
                        <td>' . htmlspecialchars($row['quantity']) . '</td>
                        <td>' . htmlspecialchars($row['recipient']) . '</td>
                        <td>' . htmlspecialchars($row['dispatch_date']) . '</td>
                    </tr>';
        }
        $table .= '</tbody></table>';

        echo json_encode(["status" => "success", "data" => $table]);
        exit();
    }


     else {
        echo json_encode(["status" => "error", "message" => "Invalid action specified."]);
    }
}
?>
