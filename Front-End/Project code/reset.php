<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
        <?php
                        echo '<div class="errors">';
                        if(isset($_GET['error'])){
                            if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1' || $_GET['error'] == 'DBError'){
                                echo '<p>Database Error!</p>';
                            }
                            else if($_GET['error'] == 'emptyfields'){
                                echo '<p>Please fill out all fields!</p>';
                            }
                            else if($_GET['error'] == 'noSuchUser'){
                                echo '<p>No user with this email!</p>';
                            }
                        }
                        else if(isset($_GET['success'])){
                            if($_GET['success'] == 'passwordReset'){
                                echo '<p style="color:green">Password Reset to "password"! Please secure account by changing this once signed in!</p>';
                            }
                        }
                        echo'</div>';
                        echo '<div class="rotate-message">Rotate Device</div>';
                ?>
                <div class="headers">
                    <h3 >Reset Password</h3>
                </div>
                <div class="changeBut">
                    <form action="includes/resetPass.php" method="post">
                                
                        <input type="text" name="mailuid" placeholder="Enter your Email...">
                        <button type="submit" name="passReset-Submit">Reset Password</button>
                    </form>
                </div>
        </div>
    </body>

<?php 
    require "footer.php"
?>