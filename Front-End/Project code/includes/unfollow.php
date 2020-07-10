<?php
// file to unfollow a user 
if(isset($_POST['unfollow-submit'])){

    
    require 'dbh.php';
    session_start();
    $userId = $_POST['unfollow-submit'];

    $delete = "DELETE FROM usersfollow WHERE followerId=".$_SESSION['userId']." AND followingVal=".$userId."";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $delete)){
        //checks if input is valid (not SQL code) etc..
        header("Location: ../profile.php?error=dbError1");
        exit();
    }
    else{
        mysqli_stmt_execute($stmt);

        header("Location: ../profile.php?success=deleted");
        exit();
    }

}
else{
    header("Location: ../index.php");
    exit();
}