<?php
include 'dbconnection.php';
include 'connection.php';
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'dispatch';

    if ($action === 'dispatch') {
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
    } elseif ($action === 'view_dispatch') {
        // Fetch dispatch log data
        $dispatch_query = "
            SELECT 
                dl.harvest_type, 
                dl.silo_location, 
                dl.quantity AS dispatched_quantity, 
                dl.recipient, 
                dl.dispatch_date
            FROM dispatch_log dl
            ORDER BY dl.dispatch_date DESC";

        $dispatch_result = mysqli_query($con, $dispatch_query);

        if (!$dispatch_result) {
            echo json_encode(["status" => "error", "message" => mysqli_error($con)]);
            exit;
        }

        // Generate HTML table for dispatch log
        $dispatch_table = '<table class="styled-table">
                    <thead>
                        <tr>
                            <th>Harvest Type</th>
                            <th>Silo Location</th>
                            <th>Dispatched Quantity</th>
                            <th>Recipient</th>
                            <th>Dispatch Date</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = mysqli_fetch_assoc($dispatch_result)) {
            $dispatch_table .= '<tr>
                        <td>' . htmlspecialchars($row['harvest_type']) . '</td>
                        <td>' . htmlspecialchars($row['silo_location']) . '</td>
                        <td>' . htmlspecialchars($row['dispatched_quantity']) . '</td>
                        <td>' . htmlspecialchars($row['recipient']) . '</td>
                        <td>' . htmlspecialchars($row['dispatch_date']) . '</td>
                    </tr>';
        }
        $dispatch_table .= '</tbody></table>';

        // Fetch remaining quantities from storage
        $remaining_query = "SELECT harvest_type, silo_location, quantity AS remaining_quantity FROM storage";
        $remaining_result = mysqli_query($con, $remaining_query);

        if (!$remaining_result) {
            echo json_encode(["status" => "error", "message" => "Failed to fetch remaining quantities."]);
            exit;
        }

        // Generate HTML table for remaining quantities
        $remaining_table = '<table class="styled-table">
                    <thead>
                        <tr>
                            <th>Harvest Type</th>
                            <th>Silo Location</th>
                            <th>Remaining Quantity</th>
                        </tr>
                    </thead>
                    <tbody>';
        while ($row = mysqli_fetch_assoc($remaining_result)) {
            $remaining_table .= '<tr>
                        <td>' . htmlspecialchars($row['harvest_type']) . '</td>
                        <td>' . htmlspecialchars($row['silo_location']) . '</td>
                        <td>' . htmlspecialchars($row['remaining_quantity']) . '</td>
                    </tr>';
        }
        $remaining_table .= '</tbody></table>';

        // Combine both tables
        $combined_tables = '<div><h2>Dispatch Log</h2>' . $dispatch_table . '</div>' .
                           '<div><h2>Remaining Quantities</h2>' . $remaining_table . '</div>';

        echo json_encode(["status" => "success", "data" => $combined_tables]);
        exit();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action specified."]);
    }
}
?>
