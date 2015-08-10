<?php   
include "../gfx3/lib.php"; //including gfx3 library
$client = new OCSClient();
$categories = $client->get("/gamingfreedom.org/v1/content/categories");

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
    <!--<link rel="stylesheet" href="css/responsive_preview.css">-->
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
          <ul class="nav nav-sidebar sidebuttons">
            <?php
                foreach($categories["ocs"]["data"]["category"] as $category){
                    echo "<li><a href=\"\">".$category["name"]."</a></li>";
                }
            ?>
            <li><a href="#category1" data-toggle="collapse">Category 1<span class="sr-only">(current)</span></a></li>
            <div class="collapse" id="category1">
                <a class="list-group-item" id="category1"href="#">subcategory a</a>
            </div>
            <li><a href="#">Category 2</a></li>
            <li><a href="#">Category 3</a></li>
            <li><a href="#">Category 4</a></li>
            <li><a href="">Category 5</a></li>
            <li><a href="">Category 6</a></li>
            <li><a href="">Category 7</a></li>
            <li><a href="">Category 8</a></li>
            <li><a href="">Category 9</a></li>
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
 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <div class="thumbnail">
        
        <div id="img_carousel" class="carousel slide" data-ride="carousel">
            <!--indicators -->
            <ol class="carousel-indicators">
                <li data-target="#img_carousel" data-slide="0" class="active"></li>
                <li data-target="#img_carousel" data-slide="1" class="active"></li>
                <li data-target="#img_carousel" data-slide="2" class="active"></li>
            </ol>
            <!-- wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <div class="item active">
                    <img src="http://placehold.it/800x300">
                </div>
                <div class="item">
                    <img src="http://placehold.it/800x300">
                </div>
                <div class="item">
                    <img src="http://placehold.it/800x300">
                </div>
            </div>
            <!-- carousel arrows -->
            <a class="left carousel-control" href="#img_carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"> </span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#img_carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"> </span>
                <span class="sr-only">Next</span>
            </a>
        </div> <!-- .carousel-->


        <!-- App description -->
        <div class="caption-full">
            <h4 class="pull-right">Free</h4>
            <h4><a href="#">App Name</a></h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
        </div> <!-- .App description -->
        <div class="ratings">
            <p class="pull-right">3 reviews </p>
            <p>
                <span class="glyphicon glyphicon-star"></span>
                <span class="glyphicon glyphicon-star"></span>
                <span class="glyphicon glyphicon-star"></span>
                <span class="glyphicon glyphicon-star"></span>
                <span class="glyphicon glyphicon-star-empty"></span>
                4.0 stars
            </p>
        </div> <!-- .ratings -->
        <div class="text-left">
            <a class="btn btn-success" href="#"><span class="glyphicon glyphicon-download-alt"></span> Download it!</a>
        </div>
        <br>

    </div> <!-- .thumbnail-->
    <!--div class="well"-->
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#reviews" data-toggle="tab">Reviews</a></li>
            <li><a href="#comments" data-toggle="tab">Comments</a></li>
            <li><a href="#author" data-toggle="tab">About the author</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active" id="reviews">
                <div class="well">
                    <div class="text-right">
                        <a class="btn btn-success" href="#reviews-anchor" id="open-review-box"> Leave a Review</a>
                    </div>
                    <div class="row" id="post-review-box" style="display:none">
                        <div class="col-md-12">
                            <form accept-charset="UTF-8" action="" method="post">
                                <input id="ratings-hidden" name="rating" type="hidden">
                                <textarea class="form-control animated" cols="50" id="new-review" name="comment" placeholder="Enter your review here..." rows="5"></textarea>

                                <div class="text-right">
                                    <div class="stars starrr" data-rating="0">
                                        <a class="btn btn-danger btn-sm" href="#" id="close-review-box" style="display:none; margin-right: 10px;">
                                            <span class="glyphicon glyphicon-remove"></span>Cancel</a>
                                            <button class="btn btn-success btn-sm" type="submit">Save</button>
                                    </div>
                                </div>
                            </form>
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
                            <p>aaaaaaa</p>
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
                            <p>bbb</p>
                        </div>
                    </div>
                </div> <!-- .well -->
         </div>

            <div class="tab-pane fade" id="comments">
                <div class="well">
                    <div class="text-right">
                        <a class="btn btn-success">Leave a comment</a>
                    </div>
                    <hr>
                </div>
             </div>
        </div> <!-- .tab content -->
    <!--/div--> <!-- .well -->
 </div> <!-- .col-md-7 col-md-offset-2 col-sm-offset-3-->
  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/sidebuttons.js"></script>
    <script src="js/reviewbox.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

