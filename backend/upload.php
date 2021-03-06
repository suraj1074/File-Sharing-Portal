<?php
session_start();
if(isset($_SESSION["username"]))
{
	$dir = dirname(__FILE__).'/../uploads/'.$_SESSION["username"]."/";
	$linkname = "http://localhost/File-Sharing-Portal/uploads/".$_SESSION["username"]."/";
}
else
{
	$dir = dirname(__FILE__).'/../uploads/';
	$linkname = "http://localhost/File-Sharing-Portal/uploads/";
}
$count = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	// loop all files
	foreach ( $_FILES['files']['name'] as $i => $name )
	{
		// if file not uploaded then skip it
		if ( !is_uploaded_file($_FILES['files']['tmp_name'][$i]) )
			{
				continue;
			}

		// now we can move uploaded files
	    if( move_uploaded_file($_FILES["files"]["tmp_name"][$i], $dir . $name) )
	    	$count++;

	    $ext = pathinfo($_FILES["files"]["name"][$i], PATHINFO_EXTENSION);
	   
	    if($ext == "zip")
	    {
	    	$zip = new zipArchive();
	    	$fileToOpen = $dir.$_FILES["files"]["name"][$i];
	    	if($zip->open($fileToOpen) === TRUE)
	    	{
	    		$zip->extractTo($dir);
	    		$zip->close();
	    	}
	    	else
	    	{
	    		echo "failed";
	    	}
	    }

	    // for compressing the file and adding the password
	   	if(isset($_POST["check"]) || isset($_POST["password"]))
	   	{
	   		$zip = new ZipArchive();
	   		$zippedFilePath = $dir.$name.".zip";
	   		$zip->open($zippedFilePath,ZipArchive::CREATE);

	   		$zip->addFile($dir.$name,$name);

	   		$zip->close();
	   	}
	}
}
echo json_encode(array('count' => $count));
header("location:../index.php");

?>