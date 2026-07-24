//modal
$(function(){
	$('.menu-trigger').on("click", function(){
		if ($('.menu-trigger').hasClass('active')) {
			$('.menu-trigger').removeClass('active');
		} else {
			$('.menu-trigger').addClass('active');
		}
	});
});		


$(function(){
	$('.menu-trigger').on("click", function(){
		if ($('.gnav').hasClass('active')) {
			$('.gnav').removeClass('active');
		} else {
			$('.gnav').addClass('active');
		}
	});
});
		
$(function(){
	$('.menu-trigger').on("click", function(){
		if ($('body').hasClass('open')) {
			$('body').removeClass('open');
		} else {
			$('body').addClass('open');
		}
	});
});

//ページ内リンククリック時にメニューを閉じる 
$(function(){
	$('.gnav a[href*="#"]').on('click', function() {  
		$('body').removeClass('open');
		$('.gnav').removeClass('active');
		$('.menu-trigger').removeClass('active');
	  });
	});


// スクロールするとheader変更
$(function () {
	$(window).on("scroll", function () {
	  const sliderHeight = 30;
	  if (sliderHeight - 30 < $(this).scrollTop()) {
		$("header").addClass("headerScroll");
	  } else {
		$("header").removeClass("headerScroll");
	  }
	});
  });


//slider
$('.img_slider').slick({
    infinite:true,
    autoplay:true,
    autoplaySpeed:2500,
    speed:800,
    fade:true,
    arrows:false,
    dots:false,
    cssEase:'linear'

});

$('.co4_slider').slick({
	slidesToShow: 3,
	slidesToScroll: 1,
	autoplay: true,
	autoplaySpeed: 3000,
    arrows: false,
    dots: true,
	responsive: [
		{
			breakpoint: 769,
			settings: {
				slidesToShow: 1,
				centerMode: true,
				centerPadding: "20%",
			}
		},
		{
			breakpoint: 592,
			settings: {
				slidesToShow: 1,
				centerMode: true,
				centerPadding: "10%",
			}
		}
	  ]
});




// tab
$(".qanda_list dd").hide();
$(".qanda_list dl").on("click", function(e){
    $('dd',this).slideToggle('fast');
    if($(this).hasClass('open')){
        $(this).removeClass('open');
    }else{
        $(this).addClass('open');
    }
});


$(function() {
	class ScrollFadeIn {
	 constructor() {
	  let box = document.querySelectorAll('.anm:not(.active)');
	
	  if (box.length === null) {
	   return;
	  }
	  let controller = new ScrollMagic.Controller();
	  for (let i = 0; i < box.length; i++) {
	  let scene = new ScrollMagic.Scene({
	   triggerElement: box[i],
	   triggerHook: 'onEnter',
	   reverse: false,
	   offset: 300,
	  })
	   .addTo(controller);
	   scene.on('enter', () => {
		box[i].classList.add('active');
	   });
	  }
	 }
	}
	new ScrollFadeIn();
});

// side_bnr
$(function(){

	let sideClosed = false;

	function sideBannerPosition(){

		// 閉じたら何もしない
		if(sideClosed){
			return;
		}

		const $side = $('.side_bnr_area');
		const $footer = $('footer');

		const scrollTop = $(window).scrollTop();
		const windowH = $(window).height();

		const footerTop = $footer.offset().top;

		// ===== footer付近では非表示 =====
		if(scrollTop + windowH > footerTop){

			$side.hide();
			return;

		}

		// ===== SPのみ =====
		if(window.innerWidth <= 768){

			const mvBottom = $('.mv_point').offset().top + $('.mv_point').outerHeight();

			if(scrollTop > mvBottom){
				$side.show();
			}else{
				$side.hide();
			}

		}else{

			// PCは常に表示
			$side.show();

		}

	}

	sideBannerPosition();

	$(window).on('scroll resize', function(){
		sideBannerPosition();
	});

	$('.side_close_btn').on('click', function(){

		sideClosed = true;
		$('.side_bnr_area').hide();

	});

});