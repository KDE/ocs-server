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
 <div class="container-fluid">
      <?php EStructure::view("categories_sidebar", $data[0]) ?>
  </div>
  <div class="col-md-7 col-md-offset-2 col-sm-offset-3">

        <!-- Page Heading -->
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">These are the search results for:
                <small>Secondary Text</small>
            </h1>
        </div>
    </div>
    <?php EStructure::view("applist", $data[1]) ?>
</div>
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/plasmastore/js/jquery.js"></script>
    <script src="/plasmastore/js/bootstrap.js"></script>
    <script src="/plasmastore/js/sidebuttons.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="/plasmastore/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>