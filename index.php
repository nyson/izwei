<?
    require_once("./lib/SQL.php"); // sql session management
    require_once("./lib/Upload.php"); // upload management
    require_once("./lib/Search.php"); // search management
    error_reporting(E_ALL);
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>I it is...</title>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <link rel="stylesheet" href="./css/form.css" />
</head>

<body>
    <div id="menu"><h2>iZwei</h2>

        <fieldset id="searchField"><legend>Search</legend>
            Enter a search term here... <br />
            <input type="search" name="search" />
            <input class="search" type="button" value="Go!" />
        </fieldset>

        <form action="./" method="post" enctype="multipart/form-data">
            <fieldset id="uploadField"><legend>Upload</legend>
                Upload by file...<br />
                <input type="hidden" name="MAX_FILE_SIZE"
                       value="<? echo MAX_FILE_SIZE;?>" />
                <input type="file" name="uploadImage" /><br />
                ...or enter an url!<br />
                <input type="text" name="ajaxUploadImage" /> <br />
                <input type="submit" name="submitImage" value="Go!" />
            </fieldset>
        </form>

    </div>
    <div id="content">
    <?
        // listen for incoming files
        $inc = new Upload();
        $inc->listen();

        $s = new Search();
        $s->search();
       

        // will be moved to design later on
        while($image = $s->get()) {
            echo "<div class='imageBlock'>"
                . "<a href='./images/$image->file'>"
                . "<img src='./thumbs/$image->file' alt='$image->name' /></a>"
                . "<div class='imageOperations'>"
                . "[0S] [=@] [/-] ['']</div></div>";
        }
    ?>
    </div>

</body>

</html>