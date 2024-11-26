<?php
include 'dbconnection.php'; 
include 'connection.php';

if (isset($_GET['action']) && $_GET['action'] === 'view') {
    $query = "SELECT harvest_type, SUM(quantity) as total_quantity, GROUP_CONCAT(silo_location) as locations
              FROM storage 
              GROUP BY harvest_type 
              ORDER BY harvest_type";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Item</th>
                    <th>Total Quantity</th>
                    <th>Locations</th>
                </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['harvest_type']) . "</td>
                    <td>" . htmlspecialchars($row['total_quantity']) . "</td>
                    <td>" . htmlspecialchars($row['locations']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No inventory data available.</p>";
    }
} else {
    echo "<p>Invalid action or no action specified.</p>";
}
?>
