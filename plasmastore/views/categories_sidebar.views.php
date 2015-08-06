<div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar sidebuttons">
            <?php
                foreach($data[0]["ocs"]["data"]["category"] as $category){
                    echo "<li><a href=\"/plasmastore/home/category/".$category["id"]."\">".$category["name"]."</a></li>";
                }
            ?>
          </ul>
        </div>
      </div>
  </div>