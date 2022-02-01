<?php
set_time_limit(0);
define("ACCESS_TOKEN_API","__");
//define("ACCESS_TOKEN_API","__");

/*
--------------
*/

function grab_image($url,$saveto){

    // -- Revised Code 1
    file_put_contents($saveto, file_get_contents(str_replace(" ","%20",$url)));
    
    /*
    --- if still make invalid image comment code 1 and active code 2
    */

    // -- revised code 2

    /*$fp = fopen ($saveto, 'wb+');
    $ch = curl_init(str_replace(" ","%20",$url));
    curl_setopt($ch, CURLOPT_TIMEOUT, 200);
    curl_setopt($ch, CURLOPT_FILE, $fp); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_exec($ch); 
    curl_close($ch);
    fclose($fp);*/
}
// -- compress
function compress($source, $destination, $quality) {

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);

    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);

    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);

    imagejpeg($image, $destination, $quality);

    return $destination;
}

// -- upload
function upload_drop_box($imgdata)
{
    $dest = time()."-img.jpg";
    $path = compress($imgdata,$dest,60);
    $fp = fopen($path, 'rb');
    $size = filesize($path);

    $cheaders = array('Authorization: Bearer '.ACCESS_TOKEN_API,
                      'Content-Type: application/octet-stream',
                      'Dropbox-Api-Arg: '.json_encode(array("path"=>"/pexel/postmakers/".$dest)));
    $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, $size);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    echo $response;
    curl_close($ch);
    fclose($fp);
    unlink($imgdata);
    unlink($dest);
}

//--- search dropbox
function search_dropbox($q)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/files/search');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"path\":\"/pexel/images/\",\"query\": \"".$q."\"}");
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = array();
	$headers[] = 'Authorization: Bearer '.ACCESS_TOKEN_API;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);
	return $result;
}

// -- pick image shareablelink to download link
function make_dl_link_dropbox($link_dir){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"path\": \"$link_dir\"}");
	curl_setopt($ch, CURLOPT_POST, 1);
	$headers = array();
	$headers[] = 'Authorization: Bearer '.ACCESS_TOKEN_API;
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$res = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $res;
}

?>
