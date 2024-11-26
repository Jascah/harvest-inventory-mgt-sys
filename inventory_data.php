<?php
    include 'connection.php';
    include 'dbconnection.php';

    $query = "SELECT harvest_type, SUM(quantity) as total_quantity FROM storage GROUP BY harvest_type";
    $result = mysqli_query($con, $query);

    $data = [];
    if(mysqli_num_rows($result) > 0 ){
        while($row = mysqli_fetch_assoc($result)){
            $data[]= $row;
        }
    }

    header ('Content-Type: application/json');
    echo json_encode($data);

?>