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
<?php 
    EStructure::view("topbar");
    //EStructure::view("categories_sidebar", $data[0]);
    $categoriesTabs = new CategoriesTabs();
    $categoriesTabs->CategoryFilter(); 
?>

<div class="col-md-7 col-md-offset-2 col-sm-offset-3">
    <ol class="breadcrumb">
  <li><a href="#">Category 1</a></li>
</ol>

</div>
 <div class="col-md-7 col-md-offset-2 col-sm-offset-3">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Page Heading
                    <small>Secondary Text</small>
                    <?php //echo $data[1]["ocs"]["meta"]["totalitems"];
                    //echo "number of pages: ".ceil($data[1]["ocs"]["meta"]["totalitems"]/$data[1]["ocs"]["meta"]["itemsperpage"]);?>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Project One -->
        <?php EStructure::view("applist", $data[1]) ?>


        <hr>

        <!-- Pagination -->
        <?php $pager1= new Pager("home","v1/content/data/"); //?pagesize=1"
        $pager1->pagination(); ?>

        <hr>
 </div> <!-- .col-md-7 col-md-offset-2 col-sm-offset-3-->
  

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/plasmastore/js/jquery.js"></script>
    <script src="/plasmastore/js/bootstrap.js"></script>
    
    <script src="/plasmastore/js/uploadbox.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/plasmastore/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>

