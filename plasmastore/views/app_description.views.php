
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
    <!--<link rel="stylesheet" href="css/responsive_preview.css">-->
  </head>

<body>
 <?php EStructure::view("topbar"); ?>

<div class="container-fluid">
      <?php EStructure::view("categories_sidebar", $data[0]) ?>
  </div>
<div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <ol class="breadcrumb">
        <?php 
        echo "
        <li><a href=\"#\">Category 1</a></li>
        <li><a href=\"#\">Subcategory a</a></li>
        <li class=\"active\">".$data[1]["ocs"]["data"]["content"][0]["name"]."</li>
        ";
        ?>
</ol>

</div>
 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <?php  if (!empty($data[1]["ocs"]["data"]["content"][0]["preview2"])){ echo"D'OH";}?>
    <div class="thumbnail">
        
    <div id="img_carousel" class="carousel slide" data-ride="carousel">
            <!--indicators -->
            <ol class="carousel-indicators">
                <li data-target="#img_carousel" data-slide="0" class="active"></li>
                <?php
                if (isset($data[1]["ocs"]["data"]["content"][0]["preview2"])){
                    echo "<li data-target=\"#img_carousel\" data-slide=\"1\" class=\"active\"></li>";
                    if (!empty($data[1]["ocs"]["data"]["content"][0]["preview3"])){
                        echo "<li data-target=\"#img_carousel\" data-slide=\"2\" class=\"active\"></li>";
                    }
                }
                ?>
            </ol>
            
            <!-- wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php echo "
                <div class=\"item active\">
                    <img src=\"".$data[1]["ocs"]["data"]["content"][0]["preview1"]."\">
                </div>";
                if (isset($data[1]["ocs"]["data"]["content"][0]["preview2"])){
                    echo "
                        <div class=\"item\">
                            <img src=\"".$data[1]["ocs"]["data"]["content"][0]["preview2"]."\">
                        </div>";
                    if (!empty($data[1]["ocs"]["data"]["content"][0]["preview3"])){
                        echo "
                            <div class=\"item\">
                                <img src=\"".$data[1]["ocs"]["data"]["content"][0]["preview3"]."\">
                            </div>";
                        }
                    }
                ?>
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
            <?php
            echo "<h4><a href=\"#\">".$data[1]["ocs"]["data"]["content"][0]["name"]."</a></h4>";
            echo "<p>".$data[1]["ocs"]["data"]["content"][0]["description"]."</p>";
            ?>
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
            <?php
            echo "<a class=\"btn btn-success\" href=\"".$data[1]["ocs"]["data"]["content"][0]["downloadlink1"]."\"><span class=\"glyphicon glyphicon-download-alt\"></span> Download it!</a>";
            ?>
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
                        <?php
                        if(OCSUser::is_logged()){
                        echo "<a class=\"btn btn-success\" href=\"#reviews-anchor\" id=\"open-review-box\"> Leave a Review</a>";
                        }
                        else {echo "<h4>Login to leave a review</h4>";}
                        ?>
                    </div>
                    <div class="row" id="post-review-box" style="display:none">
                        <div class="col-md-12">
                            <?php 
                            echo "<form accept-charset=\"UTF-8\" action=\"/plasmastore/app_description/leaveComment/".$data[1]["ocs"]["data"]["content"][0]["id"]."\" method=\"post\">";
                            ?>
                                <input id="ratings-hidden" name="rating" type="hidden">
                                <input type="text" class="form-control" name="inputSubject" placeholder="review subject">
                                <textarea class="form-control animated" cols="50" id="new-review" name="inputMessage" placeholder="Enter your review here..." rows="5"></textarea>

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
                    <?php
                    if(isset($data[2]["ocs"]["data"]["comment"])){
                        foreach($data[2]["ocs"]["data"]["comment"] as $comment){
                            echo "
                            <div class=\"row\">
                            <div class=\"col-md-12\">
                            <b>".$comment["subject"]."</b>
                            <p>".$comment["text"]."
                            <p>left by".$comment["user"]."
                            <span class=\"pull-right\">".$comment["date"]."</span>
                            <hr>";
                        }
                    } else {echo "no comments found";}
                    ?>
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
    <script src="/plasmastore/js/jquery.js"></script>
    <script src="/plasmastore/js/bootstrap.js"></script>
    <script src="/plasmastore/js/sidebuttons.js"></script>
    <script src="/plasmastore/js/reviewbox.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/plasmastore/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

