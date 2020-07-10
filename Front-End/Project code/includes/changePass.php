<?php
// file called when user wishes to change thier password
session_start();
if(isset($_POST['passChange-Submit'])){
    require 'dbh.php';
    $OldPass = $_POST['pwdOld'];
    $newPass = $_POST['pwdNew'];
    $newPass2 = $_POST['pwdNew1'];

    if(empty($OldPass)|| empty($newPass) || empty($newPass2)){
        // checks form was fully filled out
        header("Location: ../profile.php?error=emptyfields");
        exit();
    }
    else if($newPass != $newPass2 ){
        //checks if two passwords match
        header("Location: ../profile.php?error=passwordsDoNotMatch");
        exit();
    }
    else {
        //form fully filled
        $sql = "SELECT * FROM users WHERE email='".$_SESSION['userEmail']."'";
        if($result = mysqli_query($conn, $sql)){
            $row = mysqli_fetch_assoc($result);

            $hashpwd = password_verify($OldPass, $row['pass']);
            if($hashpwd == false){
                //if passwords do not match
                header("Location: ../profile.php?error=incorrectPassword");
                exit();
            }
            else if($hashpwd == true){
                //password corrrect
                $sqlIns = "UPDATE users SET pass = ? WHERE email='".$_SESSION['userEmail']."'";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sqlIns)){
                    //checks if input is valid (not SQL code)
                    header("Location: ../profile.php?error=dbError2");
                    exit();
                }
                else{
                    $hashpwd = password_hash($newPass, PASSWORD_DEFAULT); // hash the password

                    mysqli_stmt_bind_param($stmt, "s", $hashpwd); // s = string passing one string into statement
                    mysqli_stmt_execute($stmt);
                    header("Location: ../profile.php?success=passwordChanged");
                    exit();
                }
            }
        }
        else{
            header("Location: ../profile.php?error=DBError");
            exit();
        }
    }
}
else{
    header("Location: ../index.php");
    exit();
}