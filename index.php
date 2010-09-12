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
    $tags = new Tags();
    
?>

<?php $site->header(); ?>

<head>
	<?php $site->head(); ?>
</head>



<body>
    <div id="colMenu">
    	<h2>i&sup2;</h2>
    	
    	<br />
    	<div class=menuIcon>
	        <div id="popupBubbleSearch" class="menuPopupContainer">
		    	<div class="menuPopupWing"></div>
	    		<div class="menuPopup"><?php 
echo       "<strong>Search!</strong><br/>
       <a href='javascript:searchHelp();' title='HALP'>Help!</a>
		<input type=\"text\" class=\"iWantFocus\" id=\"quickSearchText\" />
           <input class=\"search\" id='quickSearchExecute' type=\"button\" value=\"Go!\" />
            <br /><div id='searchRules'></div>";	    		
	    		?></div>
			</div>
			
	    	<a class="menuIconLink" href="javascript:bubble('search');" title="Search (s)">
	    		<img src="./design/icons/find.png" alt="Search (s)" />
	    	</a>
		</div>	    	
    	
    	<br />
    	<div class=menuIcon>
	        <div id="popupBubbleUpload" class="menuPopupContainer">
		    	<div class="menuPopupWing"></div>
	    		<div class="menuPopup"><?php 
echo "<form action=\"./\" method=\"post\" enctype=\"multipart/form-data\">
     			<strong>Upload by file...</strong><br />
                <input type=\"hidden\" name=\"MAX_FILE_SIZE\"
                       value=\"". MAX_FILE_SIZE . "\" />
                <input type=\"file\" size='10' id='uploadImage' name=\"uploadImage\" /><br />
                ...or enter an url!<br />
                <input type=\"text\" class=\"iWantFocus\" id=\"uploadURL\" /> <br />
                <label id='uploadLabel' class='statusMessage'></label>
                <input type=\"submit\" id=\"submitImage\" onclick='return validateUpload();' name=\"submitImage\" value=\"Go!\" />
                
        </form>"; 	    		
	    		?></div>
			</div>    	
    	
	    	<a class="menuIconLink" href="javascript:bubble('upload');" title="Upload (u)">
	    		<img src="./design/icons/image_add.png" alt="Upload (u)"  />
	    	</a>
    	</div>

    </div>
    <div id="thumbnails">
    
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
    <div id="modal"></div>
    <div id="modalContent"> </div>
</body>

<?  $site->footer();
?>
