<?php
     $servername ="localhost";
     $username ="root";
     $password ="";
     $dbname= "harvest_inventory";

     $con = new mysqli($servername, $username, $password, $dbname);

     // Check connection
     if ($con->connect_error) {
         die("Connection failed: " . $con->connect_error);
     }

   /* try {
        // Create a PDO connection
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }*/
?>