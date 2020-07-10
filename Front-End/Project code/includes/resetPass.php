<?php
// file to reset users password (resets it to password)
if(isset($_POST['passReset-Submit'])){

    require 'dbh.php';
    session_start();
    $email = $_POST['mailuid'];

    if(empty($email)){
        // checks form was fully filled out
        header("Location: ../reset.php?error=emptyfields");
        exit();
    }
    else{
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt  = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            //checks if input is valid (not SQL code)
            header("Location: ../reset.php?error=dbError1");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $checker = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($checker)){
                //user exists
                // reset password to 'password'
                $defaultPass = 'password';
                $hashpwd = password_hash($defaultPass, PASSWORD_DEFAULT);
                $sqlIns = "UPDATE users SET pass =? WHERE email='$email'";

                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sqlIns)){
                    //checks if input is valid (not SQL code)
                    header("Location: ../reset.php?error=dbError1");
                    exit();
                }
                else{
                    mysqli_stmt_bind_param($stmt, "s", $hashpwd); 
                    mysqli_stmt_execute($stmt);
                    header("Location: ../reset.php?success=passwordReset");
                    exit();
                }
                
            }
            else {
                //user does not exist
                header("Location: ../reset.php?error=noSuchUser");
                exit();
            }

        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}