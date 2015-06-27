<?php
//protecting whole website with auth and enabled
EUtility::protect();
?>
<html>
	<head>
		<?php EStructure::view("css"); ?>
	</head>
	<body>

		<div id="header">
			<h1>OCS Server admin panel</h1>
		</div>

		<div id="nav">
			<a href="/admin/status/index">Status</a><br>
			<a href="/admin/status/database">Database</a><br>
			<a href="/admin/status/categories">Categories</a><br>
			<a href="/admin/status/test">Sanity Test</a><br>
		</div>

		<div id="section">
