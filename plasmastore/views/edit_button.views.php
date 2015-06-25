<?php
echo "<a class=\"btn btn-success\" href=\"#\" id=\"open-uploadapp-box\">Edit</a>

                <div class=\"row\" id=\"post-uploadapp-box\" style=\"display:none\">
                    <div class=\"col-md-12\">
                        <form class=\"form-horizontal\" accept-charset=\"UTF-8\" action=\"/plasmastore/userpanel/upload\" method=\"post\">
                            <div class=\"form-group\">
                                <label for=\"inputTitle\" class=\"col-sm-2 control-label\">Title</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"text\" class=\"form-control\" name=\"inputTitle\" placeholder=\"Title\">
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputDownloadName\" class=\"col-sm-2 control-label\">Download name</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"text\" class=\"form-control\" name=\"inputDownloadName\" placeholder=\"this label will be shown to download your app\">
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputDownloadLink\" class=\"col-sm-2 control-label\">Download link</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"text\" class=\"form-control\" name=\"inputDownloadLink\" placeholder=\"direct link to your app\">
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputSummary\" class=\"col-sm-2 control-label\">Summary</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"text\" class=\"form-control\" name=\"inputSummary\" placeholder=\"a very short description of your app to be shown in previews\">
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputDescription\" class=\"col-sm-2 control-label\">Description</label>
                                <div class=\"col-sm-10\">
                                    <textarea rows=\"5\" class=\"form-control\" name=\"inputDescription\" placeholder=\"a complete description of your app\"></textarea>
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputVersion\" class=\"col-sm-2 control-label\">Version</label>
                                <div class=\"col-sm-10\">
                                    <input type=\"text\" class=\"form-control\" name=\"inputVersion\" placeholder=\"1.0\">
                                </div>
                            </div>
                            <div class=\"form-group\">
                                <label for=\"inputChangelog\" class=\"col-sm-2 control-label\">Changelog</label>
                                <div class=\"col-sm-10\">
                                    <textarea class=\"form-control\" rows=\"3\" name=\"inputChangelog\" placeholder=\"list the new changes/fixes have you made\"></textarea>
                                </div>
                            </div>
                            <a class=\"btn btn-danger btn-sm\" href=\"#\" id=\"close-uploadapp-box\" style=\"display:none; margin-right: 10px;\">Cancel</a>
                            <button class=\"btn btn-success btn-sm\" type=\"submit\">Upload!</button>
                        </form>
                    </div>
                </div>"; 
                ?>