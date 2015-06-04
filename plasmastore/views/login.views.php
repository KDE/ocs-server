<?php

 if(!OCSUser::is_logged()){ echo "
      <form class=\"navbar-form navbar-right \" action=\"/plasmastore/home/index\" method=\"post\">
        <div class=\"form-group \">
            <input type=\"text\" class=\"form-control\" name=\"login\" placeholder=\"Username\"> 
            <input type=\"password\" class=\"form-control\" name=\"password\" placeholder=\"Password\">
        </div>
        <button type=\"submit\" class=\"btn btn-default\">Sign In</button>
      </form>"; } 
      else {echo "
      <ul class=\"nav navbar-nav navbar-right\">
      <li class=\"dropdown\">
          <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\"><span class=\"glyphicon glyphicon-user\"></span> Guest_User <span class=\"caret\"></span></a>
          <ul class=\"dropdown-menu\" role=\"menu\">
            <li><a href=\"/plasmastore/login/logout\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout</a></li>
            <li><a href=\"#\"><span class=\"glyphicon glyphicon-send\"></span>  My Messages</a></li>
            <li class=\"divider\"></li>
            <li><a href=\"#\">  My Account</a></li>
          </ul>
          </li>
          </ul>";}
          ?> 
