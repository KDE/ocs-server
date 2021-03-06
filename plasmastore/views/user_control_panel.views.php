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
            <h1 class=\"page-header\">Hi ".$data[0]["ocs"]["data"]["person"][0]["firstname"]."</h1>
            <h3>These are your account info:</h2>
            <h4>First Name: ".$data[0]["ocs"]["data"]["person"][0]["firstname"]."</h4>
            <h4>Last Name:  ".$data[0]["ocs"]["data"]["person"][0]["lastname"]."</h4>
            <h4>User Name:  ".$data[0]["ocs"]["data"]["person"][0]["personid"]."</h4>
            <h4>Email: ".$data[0]["ocs"]["data"]["person"][0]["email"]."</h4> ";
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
                    <a class="btn btn-success" id="open-uploadapp-box">Upload a new app</a>
                </div>
                <div class="row" id="post-uploadapp-box" style="display:none">
                    <div class="col-md-12">
                        <form class="form-horizontal" enctype="multipart/form-data" action="/plasmastore/userpanel/upload" method="post">
                            <div class="text-right">
                                <a class="btn btn-danger btn-sm" href="#" id="close-uploadapp-box" style="display:none; margin-right: 10px;">Cancel</a>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="inputTitle" class="col-sm-2 control-label">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="inputTitle" placeholder="Title">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="type" class="col-sm-2 control-label">Category</label>
                                <div class="col-sm-10">
                                    <select name="type">
                                        <?php  foreach($data[2]["ocs"]["data"]["category"] as $category) {
                                            echo "<option value=".$category["id"].">".$category["name"]."</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDownloadName" class="col-sm-2 control-label">Download name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="inputDownloadName" placeholder="this label will be shown to download your app">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDownloadLink" class="col-sm-2 control-label">Download link</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="inputDownloadLink" placeholder="fill this only if you don't want to host your app on this website">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDownloadFile" class="col-sm-2 control-label">Download File</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="inputDownloadFile" id="inputDownloadFile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputScreenshot1" class="col-sm-2 control-label">Screenshot 1</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="inputScreenshot1" id="inputScreenshot1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputScreenshot2" class="col-sm-2 control-label">Screenshot 2</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="inputScreenshot2" id="inputScreenshot2">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputScreenshot1" class="col-sm-2 control-label">Screenshot 3</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="inputScreenshot3" id="inputScreenshot1">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputSummary" class="col-sm-2 control-label">Summary</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="inputSummary" placeholder="a very short description of your app to be shown in previews">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputDescription" class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
                                    <textarea rows="5" class="form-control" name="inputDescription" placeholder="a complete description of your app"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputVersion" class="col-sm-2 control-label">Version</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="inputVersion" placeholder="1.0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputChangelog" class="col-sm-2 control-label">Changelog</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="3" name="inputChangelog" placeholder="list the new changes/fixes have you made"></textarea>
                                </div>
                            </div>
                            <button class="btn btn-success btn-sm" type="submit">Upload!</button>
                        </form>
                    </div>
                </div> <!-- .div class="row" id="post-uploadapp-box" style="display:none" -->
                <?php 
                if (isset($data[1]["ocs"]["data"]["content"])){
                    $number="0";
                        echo "<table class=\"table table-striped\">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Delete</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>";
                    foreach($data[1]["ocs"]["data"]["content"] as $content){
                        //if(OCSUser::login()==$content["personid"]){
                                    $number=$number+1;
                                    echo "
                            <tr>
                              <td>$number</td>
                              <td><a href=\"/plasmastore/app_description/show/".$content["id"]."/".ERewriter::prettify($content["name"])."\">".$content["name"]."</td>
                              <td><a class=\"btn-sm btn-danger\" href=\"/plasmastore/home/delData/".$content["id"]."\">Delete <span class=\"glyphicon glyphicon-trash\"></span></a></td>
                              <td><a class=\"btn-sm btn-success open-editapp-box".$content["id"]."\" onclick=\"$(this).openEditBox(".$content["id"].");\" href=\"#\">Edit</a>

                                <div class=\"row post-editapp-box".$content["id"]."\" style=\"display:none\">
                                    <div class=\"col-md-12\">
                                        <form class=\"form-horizontal\" enctype=\"multipart/form-data\" action=\"/plasmastore/userpanel/edit/".$content["id"]."\" method=\"post\">
                                            <div class=\"form-group\">
                                                <label for=\"inputTitle\" class=\"col-sm-2 control-label\">Title</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"text\" class=\"form-control\" name=\"inputTitle\" value=\"".$content["name"]."\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"type\" class=\"col-sm-2 control-label\">Category</label>
                                                <div class=\"col-sm-10\">
                                                    <select name=\"type\">";
                                                         foreach($data[2]["ocs"]["data"]["category"] as $category) {
                                                            if ($category["id"]==$content["id"]){
                                                                echo "<option value=".$category["id"]." selected>".$category["name"]."</option>";
                                                            }
                                                            else {
                                                                echo "<option value=".$category["id"].">".$category["name"]."</option>";
                                                            }
                                                        }
                                                        echo "
                                                    </select>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputDownloadName\" class=\"col-sm-2 control-label\">Download name</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"text\" class=\"form-control\" name=\"inputDownloadName\" value=\"".$content["downloadname1"]."\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputDownloadLink\" class=\"col-sm-2 control-label\">Download link</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"text\" class=\"form-control\" name=\"inputDownloadLink\" value=\"".$content["downloadlink1"]."\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputDownloadFile\" class=\"col-sm-2 control-label\">Download File</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"file\" class=\"form-control\" name=\"inputDownloadFile\" id=\"inputDownloadFile\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputScreenshot1\" class=\"col-sm-2 control-label\">Screenshot 1</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"file\" class=\"form-control\" name=\"inputScreenshot1\" id=\"inputScreenshot1\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputScreenshot2\" class=\"col-sm-2 control-label\">Screenshot 2</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"file\" class=\"form-control\" name=\"inputScreenshot2\" id=\"inputScreenshot2\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputScreenshot1\" class=\"col-sm-2 control-label\">Screenshot 3</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"file\" class=\"form-control\" name=\"inputScreenshot3\" id=\"inputScreenshot1\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputSummary\" class=\"col-sm-2 control-label\">Summary</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"text\" class=\"form-control\" name=\"inputSummary\" value=\"".$content["summary"]."\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputDescription\" class=\"col-sm-2 control-label\">Description</label>
                                                <div class=\"col-sm-10\">
                                                    <textarea rows=\"5\" class=\"form-control\" name=\"inputDescription\">".$content["description"]."</textarea>
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputVersion\" class=\"col-sm-2 control-label\">Version</label>
                                                <div class=\"col-sm-10\">
                                                    <input type=\"text\" class=\"form-control\" name=\"inputVersion\" value=\"".$content["version"]."\">
                                                </div>
                                            </div>
                                            <div class=\"form-group\">
                                                <label for=\"inputChangelog\" class=\"col-sm-2 control-label\">Changelog</label>
                                                <div class=\"col-sm-10\">
                                                    <textarea class=\"form-control\" rows=\"3\" name=\"inputChangelog\">".$content["changelog"]."</textarea>
                                                </div>
                                            </div>
                                            <a class=\"btn btn-danger btn-sm close-editapp-box".$content["id"]."\" onclick=\"$(this).closeEditBox(".$content["id"].");\" href=\"#\" style=\"display:none; margin-right: 10px;\">Cancel</a>
                                            <button class=\"btn btn-success btn-sm\" type=\"submit\">Save changes</button>
                                        </form>
                                    </div>
                                </div> 
                                </td>
                        </tr>";
                    }
                        }
                    //}
                echo"</tbody>";
            ?>
                </table>
            </div>
        </div>
        <?php 
        $name = OCSUser::login();
        $pager1 = new Pager("userpanel","v1/content/data/?user=$name");
        $pager1->pagination(); ?>
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

