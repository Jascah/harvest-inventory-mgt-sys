<?php
    session_start();
    session_unset();
    session_destroy();

    header("Location: welcome_page.html");
    exit();


?>