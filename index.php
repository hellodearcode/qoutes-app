<?php
include_once("inc/_functions.php");

/* Qoute Text*/
if(isset($_REQUEST['text_line'])){ // get from url
    $qoute_text = filter_var($_REQUEST['text_line'],FILTER_SANITIZE_STRING);
}


/*---- Not Edit Below this ----*/

if(isset($_POST['delimg'])){
    $img = str_replace('data:image/png;base64,', '', $_POST['upimg']);
    $imgName = time()."-image.png";
    file_put_contents($imgName, base64_decode($img));
    /*upload dropbox*/
    //upload_drop_box($imgName);
    //unlink($_POST['delimg']);
    exit();
}

if(isset($_REQUEST["q"])){
		$result = search_dropbox($_REQUEST["q"]);
		$dataArray = json_decode($result);
		if(isset($dataArray->matches[0])){
			$link_dir = $dataArray->matches[rand(0,count($dataArray->matches)-1)]->metadata->path_lower;
			/*
            --- Fetch Download Link
            */
			$rust = json_decode(make_dl_link_dropbox($link_dir));
			if(isset($rust->url)) // new sharing Link
			{
				$fileName = '_temp/'.time()."-img.jpg";
				grab_image(str_replace("?dl=0", "?raw=1",$rust->url),$fileName);
				echo $fileName;
			}else if(isset($rust->error->shared_link_already_exists)){// already shared image
				$fileName = '_temp/'.time()."-img.jpg";
				grab_image(str_replace("?dl=0", "?raw=1",$rust->error->shared_link_already_exists->metadata->url),$fileName);
				echo $fileName;
			}

		}else{
			// -- if nothing found in dropbox
			$fileName = '_temp/'.time()."-img.jpg";
			grab_image("https://unsplash.it/1500/1500/?random",$fileName);
			echo $fileName;
		}
exit();
}
?>
<html>
    <head>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="js/html2canvas.js"></script>
	<script type="text/javascript">
	var img = new Image();
	// -- Object Image Generation
	function dataURLtoBlob(dataurl) {
        var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
            bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
        while(n--){
            u8arr[n] = bstr.charCodeAt(n);
        }
        return new Blob([u8arr], {type:mime});
    }
	 //------------------- Data Printing
	 function randomQuote() {
	     var postContent = "<?php if(strlen($qoute_text) > 2) { echo $qoute_text; }?>";
	     $("#quote-content").html(postContent.replace(/ *, */g, '<br>'));
	     // use 4 longest words as keywords for getting image
	     var keyword = postContent.split(' ').sort(function(a, b){
		 	return b.length - a.length;
	     });
	     keyword = keyword.slice(1,5).join(",");
	     randomBackground(keyword);
	     /* ---- 
	     COLORS CODES FOR OVERLAY BOX 
	     ----- */
	     var colors = ["rgba(35,132,213,0.4)","rgba(246,143,161,0.4)","rgba(153,66,17,0.4)",
	"rgba(28,213,105,0.4)","rgba(59,108,100,0.4)","rgba(36,58,214,0.4)",
	"rgba(156,32,232,0.4)","rgba(197,249,61,0.4)","rgba(182,11,176,0.4)",
	"rgba(52,27,174,0.4)","rgba(106,133,84,0.4)","rgba(129,115,70,0.4)",
	"rgba(14,90,120,0.4)","rgba(199,171,220,0.4)","rgba(228,127,102,0.4)",
	"rgba(46,70,38,0.4)","rgba(132,31,116,0.4)","rgba(122,7,226,0.4)",
	"rgba(124,132,132,0.4)","rgba(141,168,107,0.4)","rgba(15,73,30,0.4)",
	"rgba(62,87,17,0.4)","rgba(43,233,53,0.4)","rgba(228,235,72,0.4)",
	"rgba(35,193,253,0.4)","rgba(144,115,55,0.4)","rgba(95,173,132,0.4)",
	"rgba(87,53,234,0.4)","rgba(172,162,26,0.4)","rgba(235,162,236,0.4)",
	"rgba(220,205,189,0.4)","rgba(208,10,23,0.4)","rgba(201,236,125,0.4)",
	"rgba(246,106,85,0.4)","rgba(100,204,145,0.4)","rgba(126,176,29,0.4)",
	"rgba(146,28,218,0.4)","rgba(101,231,215,0.4)","rgba(25,121,253,0.4)",
	"rgba(204,252,68,0.4)","rgba(236,91,85,0.4)","rgba(117,83,63,0.4)",
	"rgba(57,137,160,0.4)","rgba(242,34,175,0.4)","rgba(10,254,152,0.4)",
	"rgba(177,189,142,0.4)","rgba(188,171,6,0.4)","rgba(94,170,40,0.4)",
	"rgba(135,122,79,0.4)","rgba(99,234,0,0.4)"];
	     $("#boxlay").css("background",colors[Math.floor(Math.random()*colors.length)])
	 }
	 
	 // random background using flickr api
	 function randomBackground(keyword) {
	 	var srcImg;
	 	// -- init background Image
	 	$.get('index.php?q='+keyword, function(data, status){
		    img.src = data;
		    srcImg = data;
		    //console.log(data);
		 });
	 	img.onload = function(){
	 		// -- rander Output image and upload to Dropbox
			var elementx = $("#html-content-holder"); // global variable
	 		elementx.css('background-image', "url('" + img.src + "')");
		    html2canvas(elementx, {
		    onrendered: function (canvas) {
	            var link = document.createElement("a");
		        var imgData = canvas.toDataURL("image/png");
				$.post("index.php", {delimg: srcImg,upimg: imgData}, function(result){
			      console.log(result);
			    });
	        	}
		    });
			
		}
	 }
	</script>
<html>
    <head>
	<script type="text/javascript" src="js/texture-font.js"></script>	
    </head>
    <body>
	<!-- center align -->
	<div id="html-content-holder" style="background-size:100% 100%;width: 1500px;height:1500px;position: relative;">
	    <div id="boxlay" style="padding: 30px;position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);">
		<h1 style="font-size: 90px;color:white;text-align: center;">
		    <span id="quote-content"></span>
		</h1>
	    </div>
	</div>
	<!-- <script type="text/javascript">
		setTimeout(function(){ window.location.href = window.location; }, 30000);
	</script> -->
    </body>
</html>

