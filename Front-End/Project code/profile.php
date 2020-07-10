<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
            <?php
               
                require 'includes/dbh.php';
                if(!isset($_SESSION['userId'])){
                    header("Location: index.php");
                    exit();
                }
                echo '<div class="errors">';
                if(isset($_GET['error'])){
                    if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1' || $_GET['error'] == 'DBError' || $_GET['error'] == 'dbError3' || $_GET['error'] == 'dbError2'){
                        echo '<p>Database Error!</p>';
                    }
                    else if($_GET['error'] == 'emptyfields'){
                        echo '<p>Please fill out all fields!</p>';
                    }
                    else if($_GET['error'] == 'passwordsDoNotMatch'){
                        echo '<p>Passwords did not Match!</p>';
                    }
                    else if($_GET['error'] == 'incorrectPassword'){
                        echo '<p>Password Incorrect!</p>';
                    }
                    else if($_GET['error'] == 'yourself'){
                        echo '<p>Cannot follow yourself!</p>';
                    }
                    else if($_GET['error'] == 'alreadyFollowing'){
                        echo '<p>Already following!</p>';
                    }
                    else if($_GET['error'] == 'noSuchUser'){
                        echo '<p>No user with this email!</p>';
                    }
                    
                }
                else if(isset($_GET['success'])){
                    if($_GET['success'] == 'passwordChanged'){
                        echo '<p style="color:green">Password Changed!</p>';
                    }
                    else if($_GET['success'] == 'TeamChanged'){
                        echo '<p style="color:green">Team Changed!</p>';
                    }
                    else if($_GET['success'] == 'deleted'){
                        echo '<p style="color:green">Unfollowed!</p>';
                    }
                    else if($_GET['success'] == 'followed'){
                        echo '<p style="color:green">Successfully followed!</p>';
                    }
                }
                // reset to favourite
                $_SESSION['defaultStatsId'] = $_SESSION['favTeamId'];
                $_SESSION['defaultStatsName'] = $_SESSION['userTeam']; 
                $_SESSION['defaultStatsUserId'] = $_SESSION['userId'];
                $_SESSION['defaultStatsUserEmail'] = $_SESSION['userEmail'];
                // reset dates to all time
                $_SESSION['dateStart'] = '2015-07-01';
                $_SESSION['dateEnd'] = '2020-06-01';
                $_SESSION['dateTitle'] = 'All Time';
                
                //get teams 
                $sqlTeams = "SELECT * FROM teams ORDER BY teamName";
                echo '</div>';
                echo '<div class="rotate-message">Rotate Device</div>';
                echo '<div class="headers" style="float:left; margin-left: 1.5%;">';
                    echo '<h3 style="width:100%;">My Profile</h3>';
                    echo '<p><b>Profile Email:</b> '.$_SESSION['userEmail'].'</p>';
                    echo '<p><b>Favourite Team:</b> '.$_SESSION['userTeam'] .'</p>';
                    echo '<div class="changeBar" style="float:left;">
                        <form action="includes/changeTeam.php" method="post">
                        <select name="FavteamChange" style="float:left">
                            <option value="'.$_SESSION['userTeam'] .'">'.$_SESSION['userTeam'] .'</option>';
                            if($teamResult = mysqli_query($conn, $sqlTeams)){
                                // get rows
                                while($teamrows = mysqli_fetch_array($teamResult)){
                                    // Generate each option for each team in DB
                                    echo '<option value="'.$teamrows['teamName'].'">'.$teamrows['teamName'].'</option>';
                                }
                            }
                            else{
                                header("Location: index.php?error=dbError1");
                                exit(); 
                            }
                            
                            
                        echo '</select> 
                        <button type="submit" name="teamChange-submit" >Change Team</button>
                        </form>
                    </div>';
                    echo '<p><b>Change Password: </b></p>';
                echo '</div>';
                
                echo '<div class="changeBut" style="float:left; margin-left:4%; width:57%;">';
                echo '<form action="includes/changePass.php" method="post">
                        <input type="password" name="pwdOld" placeholder="Old password...">
                        <input type="password" name="pwdNew" placeholder="New password...">
                        <input type="password" name="pwdNew1" placeholder="Re-Enter new password...">
                        <button type="submit" name="passChange-Submit" >Change Password</button>
                        </form>';
                echo '</div>';
                
                echo '<div class="headers">';
                    echo '<p><b>Follow user:</b></p>';
                echo '</div>';
                
                echo '<div class="changeBut" >';
                echo '<form action="includes/follow.php" method="post">
                        <input type="text" name="userEmail" placeholder="Enter user Email..." style="width:80%;">
                        <button type="submit" name="follow-submit" >Follow</button>
                        </form>';
                echo '</div>';
            
                $sql = "SELECT usersfollow.followerId, usersfollow.followingVal, users.email FROM usersfollow JOIN users ON usersfollow.followingVal = users.userId WHERE usersfollow.followerId = ".$_SESSION['userId']."";
                if($result = mysqli_query($conn, $sql)){
                    if(mysqli_num_rows($result) > 0){
                        echo '<div class="followTable">
                            <p><b>Following:</b></p>
                            <table>
                                <tr>
                                    <th style="width:80%;">User Email</th>
                                    <th style="text-align:center; width:20%;">Unfollow</th>
                                </tr>';
                        while($row = mysqli_fetch_assoc($result)){
                            echo '<tr>
                                <td style="width:80%;">'.$row['email'].'</td>';
                                echo "<td style='padding-left:7%; padding-right:7%; width:20%;'><form action='includes/unfollow.php' method='post'><button type='submit' name='unfollow-submit' value=". $row['followingVal']." ></button></form></td>
                            </tr>";
                        }
                        echo '</table>';
                        echo '</div>'; 
                    }
                }
                   
                
            ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>