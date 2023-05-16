<?php
    session_start();
    unset($_SESSION["fetechedStudentID"]);
    unset($_SESSION["FetechedAccountType"]);
    unset($_SESSION["register"]);
    session_destroy();
    header("Location: login.php");

?>