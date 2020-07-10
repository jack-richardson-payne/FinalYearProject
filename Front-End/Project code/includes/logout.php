<?php
// ends session and destroys data, logging the user out
session_start();
session_unset();
session_destroy();
header("Location:../index.php");