<?
    require_once("./lib/SQL.php"); // sql session management
    require_once("./lib/Upload.php"); // upload management
    require_once("./lib/Search.php"); // search management
    require_once("./lib/Design.php"); // html output
    require_once("./lib/Tags.php"); // tag management
    //error_reporting(E_ALL ^ E_NOTICE ^ E_USER_NOTICE);
    $inc = new Upload();
    $inc->listen();
    $site = new Design();
    $tags = new Tags();
    
    
?>

<?php $site->header(); ?>


<body>

	<div id="dialog">
		<div class='content'></div>
	</div>
    <div id="menu"><h2>iZwei</h2>
    <?
        echo $site->form(FORM_SIMPLESEARCH);
        echo $site->form(FORM_UPLOAD);
    ?>
    </div>
    <div id="content">
    <?
        // will be moved to design later on
        $s = new Search();
        $s->order(SORT_NEWEST);
        $s->range(0, 12);
        $res = $s->search();

        while($image = $res->fetch_object())
        	echo $site->imageBlock($image);            

        
    ?>
    </div>
</body>

<?  $site->footer();
?>