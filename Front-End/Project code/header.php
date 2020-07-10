<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content="width=device-width, initial-scale=1.0"/>
        
        <link rel="stylesheet" href="style/style.css?version=177" type="text/css">
        
        <link rel="shortcut icon" type="image/png" href="pictures/favicon.png"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        
    </head>
    <body>
        <header>
        
            <a href='index.php' style="float:left; width:4.7%;text-decoration: none; color:black; font-size: 1.3vw; font-family: Impact, sans-serif; border-right: 1px solid black">
                <img src="pictures/house.png" alt='home' width=93% height=93% style="float:left" title="Home">&nbsp&nbsp&#8239Home <!-- https://freesvg.org/house-or-home-vector-icon-->
            </a>
        
            <h1>Footy-DB</h1>
            <div class='header-buttons'>
                <?php
                    if(isset($_SESSION['userId'])){
                        echo '<form action="includes/logout.php" method="post">
                        <button type="submit" name="logout-submit" title="Logout">Logout</button>
                        </form>
                        <!-- https://publicdomainvectors.org/en/free-clipart/Logout-vector-icon/9200.html --> ';
                        echo '</div>';
                        
                        echo '<a href="profile.php" style="float:right; width:4.7%;text-decoration: none; color:black; font-size: 1.3vw; font-family: Impact, sans-serif; border-right: 1px solid black">';
                                echo '<img src="pictures/profile.png" alt="home" width=85% height=85% title="My Profile">&nbspProfile <!-- https://www.needpix.com/photo/964711/house-svg-vector-free-vector-graphics-->
                                </a>';

                        echo '<a href="myStats.php" style="float:right; width:4.7%;text-decoration: none; color:black; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:0.5%; border-right: 1px solid black">';
                        echo '<img src="pictures/stats.png" alt="home" width=75% height=75% title="My Stats">&nbsp&nbspStats <!-- https://publicdomainvectors.org/en/free-clipart/Bar-graph-icon/78794.html -->
                        </a>';

                        echo '<a href="myMatches.php" style="float:right; width:4.7%;text-decoration: none; color:black; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:1%; border-right: 1px solid black">';
                        echo '<img src="pictures/football.png" alt="home" width=85% height=85% title="My Matches">Matches <!-- https://freesvg.org/vector-drawing-of-soccer-ball-pictogram -->
                        </a>';
                            
                        echo '<a href="index.php?success=search" style="float:right; width:4.7%;text-decoration: none; color:black; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:1%; border-right: 1px solid black">';
                        echo '<img src="pictures/search.png" alt="home" width=85% height=85% title="Search">&nbspSearch <!-- https://publicdomainvectors.org/en/free-clipart/Vector-illustration-of-black-search-ideogram/33055.html -->
                        </a>';
                    }
                    else{
                        echo '<a href="signup.php" style="float:right; width:4.7%;text-decoration: none; color:black; font-size: 1.2vw; font-family: Impact, sans-serif;margin-right: 0.5%;margin-left: 0.5%;">
                        <img src="pictures/signup.png" alt="home" width=85% height=85% title="Sign Up">&nbsp&nbspSign Up <!-- https://publicdomainvectors.org/en/free-clipart/Vector-illustration-of-black-search-ideogram/33055.html --></a>
                        </div>';
                    }
                ?>
            <div class='header-login'>
                <?php
                    if(isset($_SESSION['userId'])){
                        
                    }
                    else{
                        echo '<form action="includes/login.php" method="post">
                        <input type="text" name="mailuid" placeholder="email...">
                        <input type="password" name="pwd" placeholder="password...">
                        <button type="submit" name="login-submit">Login</button>
                        </form>';
                    }
                ?>
            </div>
        </header>
    </body> 
<html>
    
