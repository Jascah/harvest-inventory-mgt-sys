<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch historical data from the database
    $query = "SELECT date_added, quantity FROM storage ORDER BY date_added ASC";
    $result = mysqli_query($con, $query);

    if (!$result) {
        echo json_encode(["status" => "error", "message" => "Failed to fetch data"]);
        exit;
    }

    // Prepare data for the Python script
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            "date_added" => $row['date_added'],
            "quantity" => intval($row['quantity'])
        ];
    }

    // Call the Python script
    $command = 'python3 predict_inventory.py';
    $process = proc_open($command, [
        0 => ["pipe", "r"], // STDIN
        1 => ["pipe", "w"], // STDOUT
        2 => ["pipe", "w"]  // STDERR
    ], $pipes);

    if (is_resource($process)) {
        fwrite($pipes[0], json_encode($data)); // Send data to Python script
        fclose($pipes[0]);
        
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        proc_close($process);

        if ($error) {
            echo json_encode(["status" => "error", "message" => $error]);
        } else {
            echo $output; // Return the Python script's response
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to execute script"]);
    }
}
?>
