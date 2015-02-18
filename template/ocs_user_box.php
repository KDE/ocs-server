<?php

//loading EUser

if(!EUser::logged()){
	$output = "
	<li class=\"dropdown\">
    <a href=\"#\"
    class=\"dropdown-toggle\"
    data-toggle=\"dropdown\">
    Account
    <b class=\"caret\"></b>
    </a>
    <ul class=\"dropdown-menu\">
    <li><a data-toggle=\"modal\" href=\"#loginModal\" id=\"loginButton\" class=\"modalButton\" target=\"/loginModal.php\">Login</a></li>
    <li class=\"divider\"></li>
    <li><a data-toggle=\"modal\" href=\"#registerModal\" id=\"registerButton\" class=\"modalButton\" target=\"/registerModal.php\">Register</a></li>
    <li><a href=\"#\">Forgot password</a></li>
    </ul>
    </li>
    
    <div class=\"modal hide\" id=\"loginModal\">
    </div>
    
    <div class=\"modal hide\" id=\"registerModal\">
    </div>
    ";
} else {
	
	$output = "
	<li class=\"dropdown\">
    <a href=\"#\"
    class=\"dropdown-toggle\"
    data-toggle=\"dropdown\">
    Welcome ".EUser::nick()."!
    <b class=\"caret\"></b>
    </a>
    <ul class=\"dropdown-menu\">
    <li><a href=\"/gamelist.php/user/".EUser::nick()."\">My games</a></li>
    <li><a data-toggle=\"modal\" href=\"#addGameModal\" class=\"modalButton\" target=\"/addGameModal.php\">Add game</a></li>
    <li class=\"divider\"></li>
    <li><a data-toggle=\"modal\" href=\"#viewSelfProfileModal\" class=\"modalButton\" target=\"/viewProfileModal.php?id=".EUser::id()."\">View profile</a></li>
    <li class=\"divider\"></li>
    <li><a href=\"/logout.php\">Logout</a></li>
    </ul>
    </li>
    
    <div class=\"modal hide\" id=\"viewSelfProfileModal\"></div>
    <div class=\"modal hide\" id=\"addGameModal\"></div>
    
	";
}

echo "<div class=\"modal hide\" id=\"genericModal\"></div>";

echo $output;
?>
