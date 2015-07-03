	<?php EStructure::view("header"); ?>
		<h3>Step 1: configuring your database</h3>
        Please define your database configuration:<br><br>
        <?=$data[0]['notification']?>
        <form action="
        <?php if(!$data[0]['working']){ echo '/wizard/steps/step1'; } else { echo '/wizard/steps/step2'; }?>
        " method="post">
			<table border="0">
				<tr><td>Name:</td><td><input type="text" name="name" value="<?=$data[0]['name']?>" placeholder="database name"></td></tr>
				<tr><td>Host:</td><td><input type="text" name="host" value="<?=$data[0]['host']?>" placeholder="host"></td></tr>
				<tr><td>User:</td><td><input type="text" name="user" value="<?=$data[0]['user']?>" placeholder="user"></td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" value="<?=$data[0]['pass']?>" placeholder="password"></td></tr>
				<tr><td>Repeat password:</td><td><input type="password" name="password2" value="<?=$data[0]['pass2']?>"  placeholder="repeat password"></td></tr>
				
				<tr><td colspan="2"><input style="float:right" type="submit" value="<?php if(!$data[0]['working']){ echo 'Try configuration'; } else { echo 'Next step ->'; } ?>"></td></tr>
			</table>
        </form>
		
	<?php EStructure::view("footer"); ?>
