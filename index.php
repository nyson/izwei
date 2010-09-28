<?

    require_once("./lib/SQL.php"); // sql session management
    require_once("./lib/Upload.php"); // upload management
    require_once("./lib/Search.php"); // search management
    require_once("./lib/Design.php"); // html output
    require_once("./lib/Tags.php"); // tag management
    if(isset($_GET['debug'])) {
		echo "Debug mode on!";
		error_reporting(E_ALL);
	}else
    	error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);

    $inc = new Upload();
    $inc->listen();
    $site = new Design();
    
$site->header(); ?>

<head>
	<?php $site->head(); ?>
</head>



<body>
<?php 
	$site->menu();
	$site->content();
?>
</body>