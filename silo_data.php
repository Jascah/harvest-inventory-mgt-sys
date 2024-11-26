
<?php
include 'dbconnection.php';
include 'connection.php';

// Query to calculate used and remaining capacity
$query = "SELECT silo_location, SUM(quantity) AS used_capacity, 
            (10000 - SUM(quantity)) AS remaining_capacity 
          FROM storage 
          GROUP BY silo_location";

$result = mysqli_query($con, $query);

$data = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
