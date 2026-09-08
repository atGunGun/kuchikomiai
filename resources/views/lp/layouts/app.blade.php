<!DOCTYPE html>
<html lang="ja" dir="ltr">

<head>
<meta charset="UTF-8">
<title>Coel</title>
@yield('head')
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<meta name="format-detection" content="telephone=no">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Roboto:wght@100..900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('lp/css/default.css') }}">
<link rel="stylesheet" href="{{ asset('lp/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('lp/css/slick-theme.css') }}">
<link rel="stylesheet" href="{{ asset('lp/css/slick.css') }}">
<link rel="stylesheet" href="{{ asset('lp/css/validationEngine.jquery.css') }}">
<link rel="stylesheet" href="{{ asset('lp/css/jquery-ui.min.css') }}">

<link rel="shortcut icon" href="{{ asset('lp/img/common/favicon.png') }}">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ asset('lp/js/slick.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.6/ScrollMagic.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.5/plugins/debug.addIndicators.min.js"></script>
<script src="{{ asset('lp/js/jquery-ui.min.js') }}"></script>
</head>

<body id="pTop" class="website">

@include('lp.components.header')

@yield('content')

@include('lp.components.footer')
<script src="{{ asset('lp/js/script.js') }}"></script>
<script src="{{ asset('lp/js/jquery.validationEngine.js') }}"></script>
<script src="{{ asset('lp/js/jquery.validationEngine-ja.js') }}"></script>
<script src="{{ asset('lp/js/mail_send.js') }}"></script>
<!-- MV画像動き -->
<script>
const path = document.getElementById("blobPath");
const blob = document.querySelector(".blob_wrap");

const pointCount = 18;

const base = [];

for(let i=0;i<pointCount;i++){
    base.push(Math.PI*2/pointCount*i);
}

function draw(time){

    const rect = blob.getBoundingClientRect();

    const size = Math.min(rect.width, rect.height);

    const cx = size/2;
    const cy = size/2;

    const radius = size*0.46;

    let d="";
    const pts=[];

    base.forEach((angle,index)=>{

        const r =
            radius
            + Math.sin(time*0.0015+index*0.9)*12
            + Math.cos(time*0.0012+index*1.7)*8
            + Math.sin(time*0.0019+index*2.4)*5;

        pts.push({

            x:cx+Math.cos(angle)*r,
            y:cy+Math.sin(angle)*r

        });

    });

    for(let i=0;i<pts.length;i++){

        const p1=pts[i];
        const p2=pts[(i+1)%pts.length];

        const mx=(p1.x+p2.x)/2;
        const my=(p1.y+p2.y)/2;

        if(i===0){
            d=`M ${mx} ${my}`;
        }

        d+=` Q ${p2.x} ${p2.y} ${(p2.x+pts[(i+2)%pts.length].x)/2} ${(p2.y+pts[(i+2)%pts.length].y)/2}`;

    }

    d+=" Z";

    path.setAttribute("d",d);

    requestAnimationFrame(draw);

}

if (path && blob) {
    requestAnimationFrame(draw);
}
</script>
</body>
</html>