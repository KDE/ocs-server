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
      <a class="navbar-brand" href="/plasmastore/home/"><span><img class="logo" src="/plasmastore/img/plasma.png"/></span> PlasmaStore</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="#">Link <span class="sr-only">(current)</span></a></li>
        <li><a href="#">Link</a></li>
        </ul>



        <!--div class="col-md-4 col-md-offset-2 col-sm-3" !-->
          <form class="navbar-form navbar-center navbar-input-group" accept-charset="UTF-8" action="/plasmastore/search/search" method="post">
              <input type="text" class="form-control" name="searchInput" placeholder="Search">
            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span>  Go!</button>
            
          </form>
        <!--/div!-->
    <?php EStructure::view("login")?>

    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>