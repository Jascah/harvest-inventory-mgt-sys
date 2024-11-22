<?php
session_start();
include 'connection.php';
include 'rolecheck.php';

if (!isset($_SESSION['name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}
if (!isset($_SESSION['role'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session role not set.']);
    exit;
}

// Check if the action parameter exists
$action = $_REQUEST['action'] ?? null;

if (!$action) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    session_start();

    // Ensure the user is logged in and their role is checked
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    $entry_id = mysqli_real_escape_string($con, $_POST['entry_id']);
    $query = "DELETE FROM storage WHERE id = '$entry_id'";

    if (mysqli_query($con, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Entry deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete entry.']);
    }
    exit;
}


// Handle VIEW action
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'view') {
    $query = "SELECT id, harvest_type, silo_location, quantity, date_added FROM storage";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo "<p>Error fetching storage data.</p>";
        exit;
    }

    echo '<table class="styled-table">';
    echo '<thead>';
    echo '<tr><th>Harvest Type</th><th>Silo Location</th><th>Quantity</th><th>Date Added</th><th>Actions</th></tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo "<td>{$row['harvest_type']}</td>";
        echo "<td>{$row['silo_location']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['date_added']}</td>";
        echo '<td>';
        echo "<button class='edit-button' onclick=\"editEntry({$row['id']})\">Edit</button>";
        
        // Add the delete button with data-entry-id attribute
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'manager'])) {
            echo "<button class='delete-button' data-entry-id='{$row['id']}' onclick=\"deleteEntry({$row['id']})\">Delete</button>";
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    exit;
}



// Helper function to check if the silo can store the item
function canStoreItem($con, $silo_location, $harvest_type) {
    $query = "SELECT harvest_type FROM storage WHERE silo_location = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $silo_location);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['harvest_type'] == $harvest_type;
    }
    return true; // True if no items stored yet, meaning it can store this item
}

// Handle ADD/UPDATE action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'update'])) {
    $harvest_type = mysqli_real_escape_string($con, $_POST['harvest_type']);
    $silo_location = mysqli_real_escape_string($con, $_POST['silo_location']);
    $quantity = intval($_POST['quantity']);
    $entered_by = mysqli_real_escape_string($con, $_POST['entered_by']);
    $entry_id = $_POST['entry_id'] ?? null;

    // Check if the silo can store the item
    if (!canStoreItem($con, $silo_location, $harvest_type)) {
        echo json_encode(['status' => 'error', 'message' => "This silo can only store $harvest_type."]);
        exit;
    }

    if ($action === 'add') {
        $date_added = date("Y-m-d H:i:s");
        $query = "INSERT INTO storage (harvest_type, silo_location, quantity, date_added, entered_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssiss", $harvest_type, $silo_location, $quantity, $date_added, $entered_by);
    } elseif ($action === 'update') {
        $query = "UPDATE storage SET harvest_type = ?, silo_location = ?, quantity = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $harvest_type, $silo_location, $quantity, $entry_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Entry saved successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save entry.']);
    }
    exit;
}


// Handle CHART_DATA action
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'chart_data') {
    $query = "SELECT harvest_type, SUM(quantity) AS total_quantity FROM storage GROUP BY harvest_type";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch chart data.']);
        exit;
    }

    $chartData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $chartData[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $chartData]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'silo_capacity') {
    // Define the fixed capacity for each silo
    $totalCapacity = 10000;

    // Query to get the used capacity for each silo location
    $query = "SELECT silo_location, SUM(quantity) AS used_capacity FROM storage GROUP BY silo_location";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch silo capacities.']);
        exit;
    }

    // Calculate remaining capacity for each silo
    $capacities = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $siloLocation = $row['silo_location'];
        $usedCapacity = $row['used_capacity'] ?? 0;

        // Calculate remaining capacity
        $remainingCapacity = $totalCapacity - $usedCapacity;

        // Append result
        $capacities[] = [
            'silo_location' => $siloLocation,
            'remaining_capacity' => $remainingCapacity
        ];
    }

    // Respond with JSON data
    echo json_encode(['status' => 'success', 'data' => $capacities]);
    exit;
}

// Invalid Action
echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
exit;



?>
