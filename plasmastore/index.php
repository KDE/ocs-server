<?php
include "../gfx3/lib.php"; //including gfx3 library

$client = new OCSClient();

$categories = $client->get("v1/content/categories");
?>
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
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/logo.css">
    <link rel="stylesheet" href="css/app.css">
  </head>

<body>
 <nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><span><img class="logo" src="img/plasma.png"/></span> PlasmaStore</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        </ul>



        <div class="col-md-3 col-md-offset-2 col-sm-2">
          <form class="navbar-form navbar-left" role="search">
            <div class="form-group">
              <input type="text" class="form-control" placeholder="Search">
            </div>
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>  Go!</button>
          </form>
        </div>
    

      <ul class="nav navbar-nav navbar-right">
        <li><a href="http://www.google.com">Link</a></li>
        <li class="dropdown">
          <!-- USER -->
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> Guest_User <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="login/login.html"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            <li><a href="#"><span class="glyphicon glyphicon-send"></span>  My Messages</a></li>
            <li class="divider"></li>
            <li><a href="#">  My Account</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href="#">Category 1<span class="sr-only">(current)</span></a></li>
            
            <?php
			
			foreach($categories["ocs"]["data"]["category"] as $category){
				echo "<li><a href=\"\">".$category["name"]."</a></li>";
			}
			
            ?>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">Category 5</a></li>
            <li><a href="">Category 6</a></li>
            <li><a href="">Category 7</a></li>
            <li><a href="">Category 8</a></li>
            <li><a href="">Category 9</a></li>
          </ul>
          <ul class="nav nav-sidebar">
            <li><a href="">Category 10</a></li>
            <li><a href="">Category 11</a></li>
            <li><a href="">Category 12</a></li>
          </ul>
        </div>
      </div>
  </div>
<div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <ol class="breadcrumb">
  <li><a href="#">Category 1</a></li>
  <li><a href="#">Subcategory a</a></li>
  <li class="active">App Name</li>
</ol>

</div>
<div class="container-fluid"> <!--e sto coso lo lascio o lo tolgo? informarsi  UPDATE fino a che non metto qualcosa a dx tenerlo-->
 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">

                <div class="thumbnail">
                    <img class="img-responsive" src="http://placehold.it/800x480" alt="">
                    <div class="caption-full">
                        <h4 class="pull-right">Free</h4>
                        <h4><a href="#">App Name</a>
                        </h4>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
                    </div>
                    <div class="ratings">
                        <p class="pull-right">3 reviews</p>
                        <p>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star-empty"></span>
                            4.0 stars
                        </p>
                    </div>
                    <div class="text-left">
                        <a class="btn btn-success"><span class="glyphicon glyphicon-download-alt"></span> Download it!</a>
                    </div>
                </div>

                <div class="well"> <!-- guardati i navtabs-->

                    <div class="text-right">
                        <a class="btn btn-success">Leave a Review</a>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star-empty"></span>
                            Anonymous
                            <span class="pull-right">3 days ago</span>
                            <p>Taaaa</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star-empty"></span>
                            Anonymous
                            <span class="pull-right">10 days ago</span>
                            <p>jcfjxgfj</p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star"></span>
                            <span class="glyphicon glyphicon-star-empty"></span>
                            Anonymous
                            <span class="pull-right">20 days ago</span>
                            <p>hdhdxhjxfjt</p>
                        </div>
                    </div>

                </div>

            </div>

    </div>

  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

