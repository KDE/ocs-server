	<?php EStructure::view("header"); ?>
		<h3>Step 3: setting admin password</h3>
        <p>Set the password for the admin panel access!</p>
        
        <form action="/admin/steps/step3/save" method="post">
			<table border="0">
				<tr><td>Password</td><td><input type="password" name="pass" value="<?=$data[0]['pass']?>"></td></tr>
				<tr><td>Repeat:</td><td><input type="password" name="pass2" value="<?=$data[0]['pass']?>"></td></tr>
				<tr><td colspan="2"><input style="float:right" type="submit" value="Save password"></td></tr>
			</table>
        </form>
		<hr>
		
	<?php EStructure::view("footer"); ?>
