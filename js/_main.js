var canvas = document.getElementById('canvas');
var img1 = new Image();
var ctx = canvas.getContext('2d');

var keyword = document.getElementById("qoutetxt").value.replace(/(\r\n|\n|\r)/gm, " ").split(' ').sort(function(a, b){ return b.length - a.length; });
$.get('index.php?q='+keyword.join(","), function(data, status){
    console.log(data);
    img1.src = data;
  });
// -- on image load
img1.onload = function () {
    // -- image
    ctx.drawImage(img1, 0, 0,img1.width,img1.height);
    // -- overlay
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
    ctx.fillStyle = colors[Math.floor(Math.random()*colors.length)];
    var dynaheight;
    var top_b = 600;
    if(document.getElementById("qoutetxt").value.length > 100)
    {
        dynaheight = canvas.height-1200;
    }else if(document.getElementById("qoutetxt").value.length > 60)
    {
        dynaheight = (document.getElementById("qoutetxt").value.length*3+200)*2;
    }else if(document.getElementById("qoutetxt").value.length > 40)
    {
        dynaheight = (document.getElementById("qoutetxt").value.length*2+130)*2;
    }else{
        top_b = canvas.height/3;
        dynaheight = 500;//(document.getElementById("qoutetxt").value.length*2+130)*2;
    }

    ctx.fillRect(
        400,
        top_b,
        canvas.width-800,
        dynaheight
       );

    // -- text
    ctx.fillStyle = '#fff';
    var fonts_array = ["arial","comic sans ms","times new roman"];
    var randomIndex = Math.floor(Math.random()*fonts_array.length);
    ctx.font = "120px '"+fonts_array[randomIndex]+"'";
    var fontHeight = 130;
    ctx.textAlign = "center";
    text_title = document.getElementById("qoutetxt").value.replace(/(\S+\s*){4}/g, "$&\n").replace(/,/g,'\n').split("\n")

    if(document.getElementById("qoutetxt").value.length < 40)
    {
        ctx.fillText(text_title[0], canvas.width/2 , top_b+200);
        for (var i = 1; i<text_title.length; i++){
            ctx.fillText(text_title[i], canvas.width/2 , top_b+200 + (i*fontHeight) );
        }   
    }else{
        ctx.fillText(text_title[0], canvas.width/2 , (canvas.height/3));
        for (var i = 1; i<text_title.length; i++){
            ctx.fillText(text_title[i], canvas.width/2 , (canvas.height/3) + (i*fontHeight) );
        }
    }

    
    var imgData = canvas.toDataURL("image/png");
    $.ajax({
      type: "POST",
      url: "index.php",
      data: {
         dbimguplod: imgData,
         delfile: img1.src
      }
    }).done(function(o) {
      console.log(o);
    });
};