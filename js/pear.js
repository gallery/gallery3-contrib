var skimimg = 0;
var hash="";
var bgcolor="black";
var detailViewMode=false;
var savedHeight = 0;
var savedWidth = 0;

$(window).resize(function (e) {
	if (window.innerHeight == savedHeight && 
		window.innerWidth == savedWidth) { e.stop(); }
	savedHeight = window.innerHeight;
	savedWidth = window.innerWidth;
	mosaicResize();
});

function swatchSkin(intSkin){
	setCookie('swatchSkin',intSkin,1);
	$('#black').removeClass().addClass("swatch");
	$('#dkgrey').removeClass().addClass("swatch");
	$('#ltgrey').removeClass().addClass("swatch");
	$('#white').removeClass().addClass("swatch");
	switch(intSkin) {
			// dkgrey
		case 'dkgrey' :
		case 1 :
			$('div.gallery-thumb-round').css('backgroundPosition' , "-200px 0px");
			$('#mosaicTable').css('backgroundColor' , "#262626");
			$('p.giTitle').css("color", "#a9a9a9");
			$("#dkgrey").addClass("dkgrey sel dkgrey-with-sel-with-swatch");
			bgcolor="dkgrey";
			break
			// ltgrey
		case 'ltgrey' :
		case 2 :
			$('div.gallery-thumb-round').css('backgroundPosition' , "-400px 0px");
			$('#mosaicTable').css('backgroundColor' , "#d9d9d9");
			$('p.giTitle').css("color", "#333333");
			$("#ltgrey").addClass("ltgrey sel ltgrey-with-sel-with-swatch");
			bgcolor="ltgrey";
			break;
			// white
		case 'white' :
		case 3 :
			$('div.gallery-thumb-round').css('backgroundPosition' , "-600px 0px");
			$('#mosaicTable').css('backgroundColor' , "#ffffff");
			$('p.giTitle').css("color", "#444444");
			$("#white").addClass("white sel white-with-sel-with-swatch");
			bgcolor="white";
			break;
		// Black
		case 'black' :
		case 0 :
		default:
			$('div.gallery-thumb-round').css('backgroundPosition' , "0px 0px");
			$('#mosaicTable').css('backgroundColor' , "#000");
			$('p.giTitle').css("color", "#a3a3a3");
			$("#black").addClass("black sel black-with-sel-with-swatch");
			bgcolor="black";
			break;
	}
	updateHash();
}

function scaleIt(v,sliding){
	//if(maxSize<100)maxSize=150;

	// Remap the 0-1 scale to fit the desired range
	//v=.26+(v*(1.0-.26));
	size = (mosaicView) ? v/2 : v;

	toggleReflex(true);
	$(".p-photo").each(function (i) {
		$(this).attr({height: size+'px',width: size + 'px'});
		$(this).css({height: size+'px',width: size+'px'});});
	if(!mosaicView && !sliding)
		toggleReflex(false);
}
function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value)+
		((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function getCookie(c_name)
{
	if (document.cookie.length>0)
	{
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1)
		{ 
			c_start=c_start + c_name.length+1; 
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		} 
	}
	return "";
}

function checkCookie()
{
	var co=getCookie('slider');
	if (co!=null && co!="")
	{
		$('#imgSlider').slider("value", co);
	}
	co=getCookie('swatchSkin');
	if (co!=null && co!="")
	{
		swatchSkin(co);
	}
	else
	{swatchSkin('black');}
}
var iRatio = iWidth = iHeight=0;
//Set a updating timer so users can't update before the image has appeard..
function swatchImg(imageId)
{
	if( imageId < 0 || imageId >= slideshowImages.length) return;
	currentImg=imageId;

	iWidth=parseFloat(slideshowImages[imageId][2].replace(/,/gi, "."));
	iHeight=parseFloat(slideshowImages[imageId][3].replace(/,/gi, "."));
	iRatio=iWidth/iHeight;
	if(isNaN(iRatio)) iRatio=1.3333;

	if( mosaicView ) 
	{
		$('#mosaicDetail').hide();
		$('#imageTitle').html("<h2>"+slideshowImages[imageId][4]+"</h2>");

		$('#mosaicImg').attr('src',  slideshowImages[imageId][0]);
		$('#mosaicImg').css('cursor', "pointer");
		$('#mosaicDetail').show("slow");
	}
	mosaicResize();
	
	/* Set controls for hover view. */
	(currentImg==0) ? $('#prev_detail').addClass('prev_detail_disabled') : $('#prev_detail').removeClass('prev_detail_disabled');	
	(currentImg!=0) ? $('#prev_detail').addClass('prev_detail') : $('#prev_detail').removeClass('prev_detail');	
	(currentImg==slideshowImages.length-1)  ? $('#next_detail').addClass('next_detail_disabled') : $('#next_detail').removeClass('next_detail_disabled');	
	(currentImg!=slideshowImages.length-1)  ? $('#next_detail').addClass('next_detail') : $('#next_detail').removeClass('next_detail');	
	/* Update image and title in focus view */
	$('#img_detail').attr('src', slideshowImages[currentImg][0]);
	$('#imageTitleLabel').html("<h2>"+slideshowImages[imageId][4]+"</h2>");
	if( detailViewMode )
	{
		//Image count.
		$.get(slideshowImages[currentImg][6]);
	}
	updateHash();
	$('#info_detail').attr('href', slideshowImages[currentImg][1]);
}

function updateHash()
{
	viewMode = detailViewMode ? "detail" : (mosaicView ? "mosaic" : "grid");
	hash = "#img=" + currentImg + "&viewMode=" + viewMode + "&bgcolor=" + bgcolor;
	window.location.hash = hash;
}
function getAlbumHash(img)
{
	viewMode = detailViewMode ? "detail" : (mosaicView ? "mosaic" : "grid");
	return "#img=" + img + "&viewMode=" + viewMode + "&bgcolor=" + bgcolor;
}

var currentImg=0;
var mosaicView=false;

function mosaicResize()
{
	if($('#mosaicGridContainer').length == 0) return; //no element found
	var myWidth = 0, myHeight = 0;
	if( typeof( window.innerWidth ) == 'number' ) {
		//Non-IE
		myWidth = window.innerWidth;
		myHeight = window.innerHeight;
	} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		//IE 6+ in 'standards compliant mode'
		myWidth = document.documentElement.clientWidth;
		myHeight = document.documentElement.clientHeight;
	} else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		//IE 4 compatible
		myWidth = document.body.clientWidth;
		myHeight = document.body.clientHeight;
	}
	if($('#pearImageFlow').length != 0)
		$('#pearImageFlow').css({'height' : (myHeight-87)+'px', 'width': myWidth+'px', 'minHeight': ((myHeight-70)*0.9)+'px'});
	if($('#imageflow').length != 0)
		$('#imageflow').css({'height': (myHeight-53)+'px', 'width': (((myWidth*0.5)<(myHeight-53)) ? myWidth : ((myHeight-65)*2)) +'px'});
	$('#detailImageView').css({'height': myHeight-165+"px"});
	(iRatio>(myWidth/(myHeight-165))) ? $('#img_detail').css({'height': myWidth/iRatio+"px",'width': myWidth+"px"}) : $('#img_detail').css({'height': myHeight-165+"px",'width': (myHeight-165)*iRatio+"px"});
	if(iHeight<(myHeight-165)&&iWidth<myWidth)
		$('#img_detail').css({'height': iHeight+"px", 'width':iWidth+"px"});

	myWidth=myWidth-7;
	($('#paginator').length != 0) ? myHeight-=165: myHeight-=138;
	myHeight = myHeight - $('#g-site-status').outerHeight(true);
	$('#g-header').css('top', $('#gsNavBar').outerHeight(true)+$('#g-site-status').outerHeight(true)-4);

	if ( !mosaicView )
	{
		$('#mosaicGridContainer').css({'height': (myHeight+33)+"px", 'width': myWidth+"px"});
	}
	else
	{
		$('#mosaicDetail').css('width', Math.floor(myWidth*0.65)+"px");
		$('#mosaicGridContainer').css({'height': (myHeight+33)+"px", 'width': Math.floor(myWidth*0.35)+"px"});

		//Resize the image..
		myWidth=myWidth*0.65;
		(iRatio>(myWidth/myHeight)) ? $('#mosaicImg').attr({height: myWidth/iRatio,width: myWidth}) : $('#mosaicImg').attr({height: myHeight,width: myHeight*iRatio});
		if(iHeight<myHeight&&iWidth<myWidth) $('#mosaicImg').attr({height:iHeight, width:iWidth});
	}
	if($('#conf_imageflow').length) refresh();
}

function bodyLoad(viewMode, bgcolor) {
	/* Parse hash */
	hash = window.location.hash;
	var h = $.parseQuery(hash.substring(1));
	if(h.img != undefined)
		currentImg = parseInt(h.img);
	if(h.bgcolor != undefined)
		swatchSkin(h.bgcolor);
	if(h.viewMode == 'detail')
		focusImage(currentImg, h.redirected);
	else
		viewMode = h.viewMode;
	/* end parse hash */
	
	if(navigator.appName == "Microsoft Internet Explorer") $('.track').each(function(s){$(this).css('top', '-16px');}); //Fix for IE's poor page rendering. 
/*	
58.5 225
32.5 125
*/
	$('#imgSlider').slider({ min: 75, max: 250, step: 2, value: 125,
		slide: function(event, ui) { scaleIt(ui.value); },
		change: function(event, ui) { scaleIt(ui.value); setCookie('slider', ui.value, '1'); } });

	if(!$('#mosaicGridContainer').length){ $('#loading').hide();maxSize=0; return; }
	//Set event for Thumb Click.
	$('.p-photo').each(function(index){ $(this).click(function(){ 
		mosaicView ? swatchImg(index) : focusImage(index) ;}); });
	$('#slideshow').click(function(){ startSlideshow(); });
	$('#mosaicDetail').click(function(){ focusImage(currentImg); });
	$('#prev_detail').click(function(){ swatchImg(currentImg-1); });
	$('#next_detail').click(function(){ swatchImg(currentImg+1); });
	
	co=getCookie('swatchSkin');
	if (co==null || co=="")
		swatchSkin(bgcolor);

	if(!slideshowImages.length) 
		viewMode='grid';

	switch (viewMode) {
		case 'grid':
			switchToGrid();
			break;
		case 'mosaic':
			switchToMosaic();
			break;
		default:
			mosaicResize();
			checkCookie();
	}
	$('#loading').hide();
	window.setTimeout("preFetch()", 500);
	setKeys();
}

function switchToGrid()
{
	toggleReflex(true);
	$('#pearImageFlow').hide();
	$('#mosaicTable').show();
	if(!$('#mosaicGridContainer').length) return;
	mosaicView=false;
	maxSize=225;
	checkCookie();
	$('#mosaicDetail').hide();
	$('#mosaicGridContainer').show();
	$('p.giTitle,div.giInfo').each(function(s){$(this).show();});
	switchMode('grid');
	mosaicResize();
	updateHash();
}

function switchToMosaic()
{
	toggleReflex(false);
	$('#pearImageFlow').hide(); //.hide(); 
	$('#mosaicTable').show();
	if(!$('#mosaicGridContainer').length) return;
	mosaicView=true;
	maxSize=125;
	checkCookie();
	$('#mosaicDetail').show();
	$('#mosaicGridContainer').show();
	$('p.giTitle,div.giInfo').each(function(s){$(this).hide();});
	switchMode('mosaic');
	swatchImg(currentImg);
	mosaicResize();
}
var slideShowMode=false
function startSlideshow()
{
	slideShowMode=true;
	$('#play_detail').hide();
	$('#pause_detail').show();
	$('#detailView').fadeIn('slow');
	hideHoverV = setTimeout("hideHoverView()",3000);
	slideShowId=currentImg;
	slideShowId=0;
	togglePlayPause();
}
var slideShow=null;
var slideShowId;
function slideShowUpdate(id)
{
	if(id > slideshowImages.length)
	{
		id = 0;	
	}
	swatchImg(id);
	slideShow = setTimeout("slideShowUpdate("+ (id+1) +")", 1000);
}
function togglePlayPause()
{
	if(slideShow == null)//We are paused
	{
		$('#play_detail').hide();
		$('#pause_detail').show();
		slideShow = setTimeout("slideShowUpdate("+ (slideShowId+1) +")", 1000);
	}
	else //We are playing
	{
		$('#pause_detail').hide();
		$('#play_detail').show();
		clearTimeout(slideShow);
		slideShow=null;
	}
}
function focusImage(id, redirected)
{
	currentImg=id;
	$('#imageTitleLabel').html("<h2>"+slideshowImages[id][4]+"</h2>");
	$('#play_detail').hide();
	$('#pause_detail').hide();
	swatchImg(id);
	$('#detailView').fadeIn('slow');
	hideHoverV = setTimeout("hideHoverView()",3000);
	detailViewMode=true;
	updateHash();
	//Image count.
	if(!redirected)
		$.get(slideshowImages[currentImg][6]);
	$('#info_detail').attr('href', slideshowImages[currentImg][1]);
}
var pearCarousel;
function startImageFlow()
{
	$('#mosaicTable').hide();

	$('#pearImageFlow').show();

	toggleReflex(true);

	for (var i = 0; i < slideshowImages.length; i++) {
		var img = '<div class="item"><img class="content" src="'+slideshowImages[i][0]+'"/><div class="caption">'+$('#mosaicGridContainer img').eq(i).attr('alt')+'"</div></div>';
		var img = '<img src="'+slideshowImages[i][0]+'" longdesc="'+i+'" width="'+slideshowImages[i][2]+'" height="'+slideshowImages[i][3]+'" alt="'+slideshowImages[i][4]+'" style="display: none;">';
		console.log(img);
		$('#pearImageFlow').append(img); 
	}
	if(!pearCarousel){
	pearCarousel = new ImageFlow();
	pearCarousel.init({ImageFlowID: 'pearImageFlow', aspectRatio: 2.4, imagesHeight: 0.6, opacity: true, reflections: false, startID: currentImg, onClick: function() {focusImage($(this).attr('longdesc'));}, startAnimation: true, xStep: 200, imageFocusM: 1.7, imageFocusMax: 4, opacityArray: [10, 9, 6, 2], percentOther: 130, captions: false, slider: false});
	}
/*
	current=(currentImg)*-xstep;
	caption_id=currentImg;
	refresh(true);

	iShow(conf_images);
	iShow(conf_scrollbar);
	initMouseWheel();
	initMouseDrag();
	mosaicResize();

	moveTo(current);
	glideTo(current, caption_id);
*/
	switchMode('carousel');
}
function setKeys()
{
	/* Fixes the back button issue */
/*	window.onunload = function()
	{
		document = null;
	}
*/	$(document).keydown(function(e)
	{
		var charCode = (e.keyCode ? e.keyCode: e.which);
		switch (charCode)
		{
			case 32: /* Space */
				if( slideShowMode) togglePlayPause();
			case 39: /* Right arrow key */
			case 78: /* N */
				swatchImg(currentImg+1); 
			//	if($('imageflow')) handle(-1);
				break;
			case 80: /* P */
			case 37: /* Left arrow key */
				swatchImg(currentImg-1); 
			//	if($('imageflow')) handle(1);
				break;
		}
	});
}
function showHoverView(){
	if(hideHoverV != null) clearTimeout(hideHoverV);
	$('#hoverView').show();
	hideHoverV = setTimeout("hideHoverView()",3000);
}
function hideHoverView(){
	if(!hovering) $('#hoverView').fadeOut();
	hideHoverV = null;
}
var hideHoverV=null;
var hovering=false;
function switchMode(mode){
	$('#mosaic,#grid,#carousel').removeClass("sel sel-with-viewSwitcher");
	$('#'+mode).addClass("sel sel-with-viewSwitcher");
}

function preFetch()
{
/*	for (var i = 0; i < slideshowImages.length; i++) {
		var tempImage = new Element('img', {'src': slideshowImages[i][0]});
	}*/
}

function toggleReflex(hide)
{
	if(hide) {
	//	$$('.Fer').each(function(s) { cvi_reflex.remove(s); });
		$('mosaicGridContainer').select('img[class="Fer"]').each(function(s,index){ Event.observe(s, 'click', function(){ mosaicView ? swatchImg(index) : focusImage(index) ;}); });
	}
	else {
	//	$$('.Fer').each(function(s) { cvi_reflex.add(s, {height: 20, distance: 0 }); });
		$('mosaicGridContainer').select('canvas[class="Fer"]').each(function(s,index){ Event.observe(s, 'click', function(){ mosaicView ? swatchImg(index) : focusImage(index) ;}); });
	}
}

function hideDetailView()
{
	$('#detailView').hide();
	slideShowMode=detailViewMode=false; 
	if(slideShow!=null) 
		clearTimeout(slideShow);
	slideShow=null;
	updateHash();
}
