<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="ocs-server webclient">
    <meta name="author" content="woffy">
    <title>PlasmaStore</title>
    <link rel="stylesheet" href="/plasmastore/css/bootstrap.css">
    <link rel="stylesheet" href="/plasmastore/css/dashboard.css">
    <link rel="stylesheet" href="/plasmastore/css/logo.css">
    <link rel="stylesheet" href="/plasmastore/css/app.css">
    <link rel="stylesheet" href="/plasmastore/css/navbar-center.css">
    <!--<link rel="stylesheet" href="css/responsive_preview.css">-->
  </head>

<body>
 <?php EStructure::view("topbar") ?>


 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            echo "
            <h1 class=\"page-header\">Public profile of ".$data[0]["ocs"]["data"]["person"][0]["personid"]."</h1>
            <h4>First Name: ".$data[0]["ocs"]["data"]["person"][0]["firstname"]."</h4>
            <h4>Last Name:  ".$data[0]["ocs"]["data"]["person"][0]["lastname"]."</h4>
            <h4>Email: ".$data[0]["ocs"]["data"]["person"][0]["email"]."</h4> 
            <a class=\"btn btn-success\" href=\"/plasmastore/publicprofile/addFriend/".$data[0]["ocs"]["data"]["person"][0]["personid"]."\">Send friendship request</a>";
            ?>
            
        </div>
    </div>
 </div> <!-- .col-md-7 col-md-offset-2 col-sm-offset-3-->
  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/plasmastore/js/jquery.js"></script>
    <script src="/plasmastore/js/bootstrap.js"></script>
    <script src="/plasmastore/js/sidebuttons.js"></script>
    <script src="/plasmastore/js/uploadbox.js"></script>
    <script src="/plasmastore/js/editbox.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/plasmastore/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

