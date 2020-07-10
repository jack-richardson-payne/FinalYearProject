<?php
// file to sign up a new user 
if(isset($_POST['signup-submit'])){

    require 'dbh.php';


    $email = $_POST['emailuid'];
    $pwd = $_POST['pwd'];
    $pwd2 = $_POST['confpwd'];
    $team = $_POST['team'];

    if(empty($email)|| empty($pwd)|| empty($pwd2) || empty($team) ){
        // checks form was fully filled out
        header("Location: ../signup.php?error=emptyfields&emailuid=".$email);
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        //checks if email is valid
        header("Location: ../signup.php?error=invalidemail");
        exit();
    }
    else if($pwd != $pwd2 ){
        //checks if two passwords match
        header("Location: ../signup.php?error=passwordsDoNotMatch&emailuid=".$email);
        exit();
    }
    // add checks for length etc..
    else{
        //check if email exists in db
        $sql = "SELECT * FROM users WHERE email=?"; //? is placeholder
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            //checks if input is valid (not SQL code)
            header("Location: ../signup.php?error=dbError1");
            exit();
        }
        else{
            mysqli_stmt_bind_param($stmt, "s", $email); // s = string passing one string into statement
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $checker = mysqli_stmt_num_rows($stmt);
            if($checker > 0){
                //email exists in db
                header("Location: ../signup.php?error=emailTaken");
                exit();
            }
            else{
                $sqlIns = "INSERT INTO users (email, pass, team) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if(!mysqli_stmt_prepare($stmt, $sqlIns)){
                    //checks if input is valid (not SQL code)
                    header("Location: ../signup.php?error=dbError2");
                    exit();
                }
                else{
                    $hashpwd = password_hash($pwd, PASSWORD_DEFAULT); // hash the password

                    mysqli_stmt_bind_param($stmt, "ssi", $email, $hashpwd, $team); // s = string passing one string into statement
                    mysqli_stmt_execute($stmt);
                    header("Location: ../index.php?success=signedup");
                    exit();
                }
            }
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);


}
else{
    header("Location: ../signup.php");
    exit();
}
