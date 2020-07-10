<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
            <h2>Sign-Up page</h2>
                <div class="errors">
                <?php
                    require 'includes/dbh.php';

                    if(isset($_GET['error'])){
                        if($_GET['error'] == 'emptyfields'){
                            echo '<p>Please fill out all fields!</p>';
                        }
                        else if($_GET['error'] == 'invalidemail'){
                            echo '<p>Enter a valid Email!</p>';
                        }
                        else if($_GET['error'] == 'passwordsDoNotMatch'){
                            echo '<p>Passwords did not Match!</p>';
                        }
                        else if($_GET['error'] == 'emailTaken'){
                            echo '<p>Account already exists with this email!</p>';
                        }
                        else if($_GET['error'] == 'dbError1' || $_GET['error'] == 'dbError1'){
                            echo '<p>Database error!</p>';
                        }
                    }

                    $sqlTeams = "SELECT * FROM teams ORDER BY teamName";

                ?>
                </div>
                <div class="rotate-message">Rotate Device</div>
                <div class='signup'>
                    <form action='includes/sign-up.php' method='post'>
                    <h3>Enter your email</h3>
                    <input type="text" name="emailuid" placeholder="email...">
                    <h3>Enter your password</h3>
                    <input type="password" name="pwd" placeholder="password...">
                    <h3>Re-enter your password</h3>
                    <input type="password" name='confpwd' placeholder='Confirm password...'>
                    <h3>Select your favourite team</h3>
                    <select name='team'>
                    <?php 
                        if($teamResult = mysqli_query($conn, $sqlTeams)){
                            // get rows
                            while($teamrows = mysqli_fetch_array($teamResult)){
                                // Generate each option for each team in DB
                                echo '<option value="'.$teamrows['teamId'].'">'.$teamrows['teamName'].'</option>';
                            }
                        }
                        else{
                            header("Location: index.php?error=dbError1");
                            exit(); 
                        }

                    ?>
                        
                    </select>
                    <button type="submit" name="signup-submit">Sign Up</button>
                </form>
            </div>
        </div> 
    </body>

<?php 
    require "footer.php"
?>