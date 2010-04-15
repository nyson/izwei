<?
    require_once("./lib/SQL.php"); // sql session management
    require_once("./lib/Upload.php"); // upload management
    
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

        $inc = new Upload();
        $inc->listen();


        $dh = opendir("./thumbs/");
        while($file = readdir($dh)) {
            if($file[0] != '.' && true)
                echo "<div class='imageBlock'>"
                    . "<a href='./images/$file'>"
                    . "<img src='./thumbs/$file' alt='$file' /></a>"
                    . "<div class='imageOperations'>"
                    . "There will be text</div></div>";
        }


        $tags = $taglinks = $images = array();

        $res = SQL::query("SELECT * FROM tags");
        while($temp = $res->fetch_object())
            $tags[] = $temp;

        $res = SQL::query("SELECT * FROM images");
        while($temp = $res->fetch_object())
            $images[] = $temp;

        $res = SQL::query("SELECT * FROM taglinks");
        while($temp = $res->fetch_object())
            $taglinks[] = $temp;

    ?>
    </div>

</body>

</html>