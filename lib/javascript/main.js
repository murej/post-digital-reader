//@codekit-prepend "prefixfree.min.js"
//@codekit-prepend "hyphenator.min.js"
//@codekit-prepend "jquery.autosize.min.js"
//@codekit-prepend "waypoints.min.js"

function resizeRibbon() {
	var ribbonWidth = $("li#collector div").width();
	$("li#collector span").css("border-left-width",(ribbonWidth/16/2)+"em");
	$("li#collector span").css("border-right-width",(ribbonWidth/16/2)+"em");
}

function setParagraphNum(currParagraphNum, allParagraphNum) {
	$("li#loc div").html("&larr; "+currParagraphNum+"/"+allParagraphNum);
}

function writingMode(setting) {

	if(setting === true) {
		$("body").addClass("locked");
		$("div#dimmer").fadeIn(0);
		$("ul#nav").hide();
		$("ul#adding-options").hide();
		$("li.next-chapter").fadeTo(0,0);
		$("div#writer").show();
		$('html, body').animate({
			scrollTop: $("div#writer").offset().top - 128
		}, 250);
		$("div#writer form textarea").focus();
	}
	else if(setting === false) {
		$("body").removeClass("locked");
		$("ul#nav").show();
		$("ul#adding-options").show();
		$("li.next-chapter").fadeTo(0,1);
		$("div#writer").hide();
		$("div#dimmer").fadeOut(0);
	}
}

function shareMode(setting, obj) {

	if(setting === true) {
		$("div#dimmer").show();
		obj.show();
		obj.find("input").focus().select();
		$("body").addClass("locked");
		$("ul#nav").hide();
	}
	else if(setting === false) {
		$("div#dimmer").hide();
		obj.hide();
		$("body").removeClass("locked");
		$("ul#nav").show();
	}
}

$(document).ready(function() {

	var collectionCounter;
	var allParagraphNum;
	var currParagraphNum = 0;
		
	/* INITIALISE */
	resizeRibbon();
	allParagraphNum = $("ul#wrapper li p").length;
	setParagraphNum(0, allParagraphNum);
    $( "li#content ul.wrapper" ).sortable({ axis: "y", disabled: true });
    $("textarea").autosize();
    Hyphenator.run();
	/**************/
	
	
	// COVER EFFECT
	
	// enable cover slideout effect when cover present
	if( $('div#cover').length !== 0 ) {
		$("ul#wrapper").css("position","fixed");
	}
	
	$('div#cover').waypoint(function(direction) {
	
		if(direction === "down") {
			$("ul#wrapper").css("position","relative");
			$("ul#nav").removeClass("start");
		}
		else {
			$("ul#wrapper").css("position","fixed");
			$("ul#nav").addClass("start");
		}
		
	}, {
		offset: function() {
			return -$(this).height();
		}
	});
	

	// COLLECTING PARAGRAPHS
	$("div.paragraph-num a").click(function(e) {
		e.preventDefault();
		$(this).toggleClass("selected");
		
		collectionCounter = $("li#collector div").html();
		
		if( $(this).hasClass("selected") ) {
			collectionCounter++;
		} else {
			collectionCounter--;
		}
				
		$("ul#nav li#collector div").html(collectionCounter);
	});

	// SORTING PARAGRAPHS
	$("li#content ul.wrapper li.paragraph ul.more li:first-child").mousedown(function() {
		$( "li#content ul.wrapper" ).sortable( "option", "disabled", false );
	});
	$("li#content ul.wrapper li.paragraph ul.more li:first-child").mouseleave(function() {
		$( "li#content ul.wrapper" ).sortable( "option", "disabled", true );
	});
	
	// LINKING PARAGRAPHS
	$("ul.more li:last-child a").click(function(e) {
		e.preventDefault();
		shareMode(true, $(this).parent().parent().parent().children("div.link"));
	});
	
	$("div.link input").click(function() {
		$(this).select();
	});

	// COUNTING PARAGRAPHS
	$("li#content li.paragraph").waypoint(function(direction) {
		
		if(direction === "down") {
			currParagraphNum++;
		}
		else {
			currParagraphNum--;
		}
		
		setParagraphNum(currParagraphNum, allParagraphNum);
	}, { offset: '100%' });

	// location hud helper
	$("li#loc div").waypoint(function(direction) {
		
		if(direction === "down") {
			$(this).addClass("stuck");
		}
		else {
			$(this).removeClass("stuck");
		}
	},	{ offset: 'bottom-in-view' });

	// ADDING PARAGRAPHS
	$("a.write").click(function(e) {
		e.preventDefault();
		writingMode(true);
	});

	$("a.cancel").click(function(e) {
		e.preventDefault();
		writingMode(false);
	});

	// ON RESIZE
	$(window).resize(function() {
		resizeRibbon();
	});
	
	// DIMMER CLICK ACTION
	$("div#dimmer").click(function() {
	
		if( $("div.link").is(":visible") ) {
			shareMode(false, $("div.link").filter(":visible"));
		}
	});
	
	$("a.author").click(function(e) {
		e.preventDefault();
		$('html, body').animate({
			scrollTop: $("h3#author").offset().top - 128
		}, 1000);
	});
		
});