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
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/logo.css">
    <link rel="stylesheet" href="../css/app.css">
    <!--<link rel="stylesheet" href="css/responsive_preview.css">-->
  </head>

<body>
 <?php EStructure::view("topbar") ?>


 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php
            echo "
            <h1 class=\"page-header\">Hi ".$data[0]["ocs"]["data"]["person"]["firstname"]."</h1>
            <h3>These are your account info:</h2>
            <h4>First Name: ".$data[0]["ocs"]["data"]["person"]["firstname"]."</h4>
            <h4>Last Name:  ".$data[0]["ocs"]["data"]["person"]["lastname"]."</h4>
            <h4>User Name:  ".$data[0]["ocs"]["data"]["person"]["personid"]."</h4>
            <h4>Email: ".$data[0]["ocs"]["data"]["person"]["email"]."</h4> ";
            ?>
        </div>
    </div>
    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#my_apps" data-toggle="tab">My Applications</a></li>
        <li><a href="#my_desktop_stuff">My Desktop stuff</a></li>
        <li><a href="#my_firends">My Friends</a>
        <li><a href="#my_firends">My Messages</a>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade in active" id="my_apps">
            <div class="well">
                <div class="text-right">
                    <a class="btn btn-success" href="#" id="open-uploadapp-box">Upload a new app</a>
                </div>
            </div>
        </div>
    </div>

       

 </div> <!-- .col-md-7 col-md-offset-2 col-sm-offset-3-->
  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/sidebuttons.js"></script>
    <script src="../js/reviewbox.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

