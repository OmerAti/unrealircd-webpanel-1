<!DOCTYPE html>
<head>
 <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.1/dist/jquery.slim.min.js"></script>

<!-- Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/unrealircd-admin.js"></script>
<title>UnrealIRCd Panel</title>
<link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>img/favicon.ico">
<link href="<?php echo BASE_URL; ?>css/unrealircd-admin.css" rel="stylesheet">
</head>
<body role="document">
<div class="container-fluid">
	
	<!-- Fixed navbar -->
	<nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top">
		<ul class="nav navbar-nav">
			<a class="navbar-brand" href="index.php"><img src="<?php echo BASE_URL; ?>img/favicon.ico" height="25" width="25"> UnrealIRCd Admin Panel</a>
<?php

$active_page = false;
/* Needs to be a separate step due to multiple matches */
foreach($pages as $name=>$page)
	if (str_ends_with($_SERVER['SCRIPT_FILENAME'], $page))
		$active_page = $page;

foreach($pages as $name=>$page)
{
	$class = "class=\"nav-item\"";
	if ($page == $active_page)
		$class = str_replace("\"nav-item\"", "\"nav-item active\"", $class);
	
	echo "			<li $class><a class=\"nav-link\" href=\"".BASE_URL.$page."\">$name</a></li> \n";
}
?>
	
		</ul>
	</nav><br>
</div>

<div class="container-fluid" role="main">
