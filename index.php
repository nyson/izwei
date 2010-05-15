<?
    require_once("./lib/SQL.php"); // sql session management
    require_once("./lib/Upload.php"); // upload management
    require_once("./lib/Search.php"); // search management
    require_once("./lib/Design.php"); // html output
    require_once("./lib/Tags.php"); // tag management
    error_reporting(E_ALL);
    $inc = new Upload();
    $inc->listen();
    $site = new Design();
    $tags = new Tags();
    
    
?>

<?php $site->header(); ?>


<body>
    <div id="menu"><h2>iZwei</h2>

    <?
        echo $site->form(FORM_SIMPLESEARCH);
        echo $site->form(FORM_UPLOAD);
    ?>

    </div>
    <div id="content">
    <?
        // will be moved to header later on
        // listen for incoming files



        // will be moved to design later on
        $s = new Search();
        $s->order(SORT_NEWEST);
        $s->range(0, 12);
        $res = $s->search();

        $tags->connect(1,"tag,tokig");


        echo "<p>";
        while($image = $res->fetch_object())
        	echo $site->imageBlock($image);            

        echo "</p><p>";
        
 

    ?>
    </div>

</body>

<?  $site->footer();
?>