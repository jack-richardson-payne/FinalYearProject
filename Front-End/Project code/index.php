<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
            <?php
                if(isset($_SESSION['userId'])){
                    require 'includes/dbh.php';
                    echo '<h2 style="color:green">Logged In!</h2>';
                    echo '<div class="rotate-message">Rotate Device</div>';
                    echo '<div class="errors">';
                    if(isset($_GET['error'])){
                        if($_GET['error'] == 'noMatches'){
                            echo '<p>No matches have been logged for these settings (change season or user)!</p>';
                        }
                        else if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1'){
                            echo '<p>Database Error!</p>';
                        }
                        else if($_GET['error'] == 'noMatchesSearch'){
                            echo '<p>No matches for this team!</p>';
                        }
                        else if($_GET['error'] == 'noTeamSelected'){
                            echo '<p>Please select a team from drop down!</p>';
                        }
                        else if($_GET['error'] == 'noMatchessearch'){
                            echo '<p>No matches available for this season for this team!</p>';
                        }
                        else if($_GET['error'] == 'noMatchesLogged'){
                            echo '<p>No matches Logged!</p>';
                        }
                        
                    }
                    else if(isset($_GET['success'])){
                        if($_GET['success'] == 'search'){
                            echo '<p style="color:green">Use search bar below to search for matches!</p>';
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
                    echo '<div class="headers">';
                    echo '<p style="margin-left:15%; margin-top:5%; width:70%; margin-bottom:2%;"><b>Use the search bar below to search for matches!</b> Matches available from the Premier League
                    down to League 2, for the past four seasons. Select a team from the drop down and press "Search"! </p>';
                    echo '</div>';
                    // add code for buttons in here 
                    echo '<div class="searchbar">
                            <form action="searchResult.php" method="post">
                            <select name="team"  style="width:60%;">
                                <option value="none" selected>---Search for matches by team---</option>';

                                if($teamResult = mysqli_query($conn, $sqlTeams)){
                                    // get rows
                                    while($rows = mysqli_fetch_array($teamResult)){
                                        // Generate each option for each team in DB   
                                        echo '<option value="'.$rows['teamName'].'">'.$rows['teamName'].'</option>';
                                    }
                                }
                                else{
                                    header("Location: index.php?error=dbError1");
                                    exit(); 
                                }
                                
                            echo '</select> 
                            <select name="Date" style="width:20%;">
                                <option value="AllTime">All Time</option>
                                <option value="19/20">19/20</option>
                                <option value="18/19">18/19</option>
                                <option value="17/18">17/18</option>
                                <option value="16/17">16/17</option>
                                <option value="15/16">15/16</option>
                            </select>
                            <button type="submit" name="search-submit" style="width:20%;">Search</button>
                            </form>
                        </div>';
                          
                    echo '<div class="headers">';
                    echo '<p style="margin-left:15%; margin-top:5%; width:20%; margin-bottom:1%; float:left;"><b>This is the My Matches Section!</b> Click here to view all the matches you have logged.</p>';
                    echo '<p style="margin-left:6%; margin-top:5%; width:20%; margin-bottom:1%; float:left;"><b>This is the My Stats Section!</b><br>Click here to view statistics for matches that you have logged.</p>';
                    echo '<p style="margin-left:5%; margin-top:5%; width:20%; margin-bottom:1%; float:left;"><b>This is the My Profile Section!</b><br>Click here to view your account settings and to follow friends.</p>';
                    echo '</div>';
                    echo '<div class="pages-buttons">
                        <a href="myMatches.php" title="My Matches" style="float:left">My Matches</a>
                        <a href="myStats.php" title="My Stats" style="float:left ;margin-left: 8%">My Stats</a>
                        <a href="profile.php" title="My Profile" style="float:right ">My Profile</a>
                        
                    </div>';
         
                }
                else{
                    echo '<h2  style="color:red">Please Sign in or create an Account!</h2>';
                    echo '<div class="rotate-message">Rotate Device</div>';
                    echo '<div class="errors">';
                    if(isset($_GET['error'])){
                        if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1'){
                            echo '<p>Database Error!</p>';
                        }
                        else if($_GET['error'] == 'incorrectPassword'){
                            echo '<p>Password Incorrect!</p>';
                        }
                        else if($_GET['error'] == 'emptyfields'){
                            echo '<p>Please fill out all fields!</p>';
                        }
                        else if($_GET['error'] == 'noSuchUser'){
                            echo '<p>No user registered with this email!</p>';
                        }
                        
                    }
                    else if(isset($_GET['success'])){
                        if($_GET['success'] == 'signedup'){
                            echo '<p style="color:green">Successfully signed up!</p>';
                        }
                    }
                    echo '</div>';
                    
                    echo '<div class="reset">';
                    echo '<a href="reset.php">Forgotten Password?</a>';
                    echo '</div>';
                    echo '<div class="headers">';
                    echo '<h3>Welcome to Footy-DB</h3>';
                    echo '<ul>
                            <li>Footy-DB is a site that allows you to track your team by logging that you have attended matches, Footy-DB
                            will then show statistics personalised to the matches you have seen. These statistcis can be viewed by team and 
                            by season for matches that a user has logged.</li>
                            <li>Currently Footy-DB holds records for matches in the Premier League, Sky Bet Championship, Sky Bet League One 
                            and Sky Bet League Two for the past 4 seasons (2018/19, 2017/18, 2016/17, 2015/16). And the current Season (19/20)
                            as of 13/02/20. The aim is to increase this number in the future.</li>
                        </ul>';
                    echo '<h3>Aims and Objectives</h3>';
                    echo '<ul>
                            <li>The aim of Footy-DB is to enhance the fan experience of attending matches by allowing fans to analyse how thier
                            team performs when they are in attendance. This allows fans to see how lucky or unlucky they are and to see what effect
                            they may have on thier team. </li>
                            <li>Footy-DB also aims to provide a way for fans to track thier team in a way personalised to them, which is different
                            to most services available.  </li>
                            <li>The hope is that allowing fans to have this ability to track their teams performance when they are attending
                            mathes, it will encourage fans to attend more potentially to improve thier stats.</li>
                        </ul>';
                    echo '</div>';
                   
                    
                                
                    
                             
                    

                }
                
            ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>