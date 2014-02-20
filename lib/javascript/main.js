//@codekit-prepend "prefixfree.min.js"
//@codekit-prepend "hyphenator.min.js"
//@codekit-prepend "waypoints.min.js"
//@codekit-prepend "jquery.cookie.js"
//@codekit-prepend "jquery.autosize.min.js"

function resizeRibbon() {
	var ribbonWidth = $("#collector div").width();
	$("#collector span").css("border-left-width",(ribbonWidth/16/2)+"em");
	$("#collector span").css("border-right-width",(ribbonWidth/16/2)+"em");
}

function handleCover() {
	
	if( $("div#cover").length !== 0 ) {
	
		if( $("body").scrollTop() === 0 ) {
			$("body").removeClass("start");
		}
		else {
/* 			$("body").removeClass("start"); */
		}
	}
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

function publishMode(setting) {
	
	if(setting === true) {
		
		var animationTime = 0;
		
		if( $('body').scrollTop() !== 0 ) {
			
			animationTime = 500;
			
			$('html, body').animate({
				scrollTop: 0
			}, animationTime);
		}
		
		setTimeout(function() {
			$("body").addClass("publish-mode");
			$("ul#publish input.title").focus();
			$("body").addClass("locked");
		}, animationTime);
	}
	else if(setting === false) {
		$("body").removeClass("publish-mode");
		$("body").removeClass("locked");
	}
}

function setCollectorNum(num) {
	$("#collector div").html(num);
}

function highlightCollectedPars(collection) {

	var i;
	for (i = 0; i < collection.length; ++i) {
		if( $("li#paragraph-"+collection[i]).length !== 0 ) {
			$("li#paragraph-"+collection[i]).children("div.paragraph-num").children("a").addClass("selected");
		}
	}
}

function collectParagraphs(id) {
	
	var collection = [];
	
	// if already saved
	if( $.cookie('myCollection') !== undefined ) {
		// get what was saved
		collection = $.parseJSON( decodeURI( $.cookie('myCollection') ) );
	}
	
	// if paragraph id was given
	if( id !== undefined ) {
	
		// check if paragraph already collected
		var collectedKey = $.inArray(id, collection);
		
		// if not yet present
		if( collectedKey === -1 ) {
			// add to collection
			collection.push(id);
		}
		// if already present
		else {
			// remove from collection
			collection.splice(collectedKey,1);
		}
	
	}
	// if not
	else {
		// highlight all collected paragraphs
		highlightCollectedPars(collection);
	}

	// show collected amount
	setCollectorNum(collection.length);
	
	if( collection.length > 0 ) {
	
		// turn array into a string
		collection = encodeURI( JSON.stringify(collection) );
		
		// save collection to cookie
		$.cookie('myCollection', collection, {
			expires: 3650, // in 10 years
			path: "/",
			json: true
		});
	
	}
	else {
		
		$.removeCookie('myCollection', { path: "/" });
	}
	
}

/*
function changeHash(hash) {

	hash = hash.replace( /^#/, '' );
	var fx, node = $( '#' + hash );
	if ( node.length ) {
		node.attr( 'id', '' );
		fx = $( '<div></div>' )
				.css({
					position:'absolute',
					visibility:'hidden',
					top: $(document).scrollTop() + 'px'
				})
				.attr( 'id', hash )
				.appendTo( document.body );
	}
	document.location.hash = hash;
	if ( node.length ) {
		fx.remove();
		node.attr( 'id', hash );
	}
}
*/

$(document).ready(function() {

	var allParagraphNum;
	var currParagraphNum = 0;
		
	/* INITIALISE */
	resizeRibbon();
	handleCover();
	collectParagraphs();
	$("div#edition-selector").tabs();
	allParagraphNum = $("ul#wrapper li p").length;
	setParagraphNum(0, allParagraphNum);
    $( "li#content ul.wrapper" ).sortable({ axis: "y", disabled: true });
    $("textarea").autosize();
    Hyphenator.run();
	/**************/
	
	
	// COVER EFFECT
	
	// enable cover slideout effect when cover present
	if( $('div#cover').length !== 0 ) {
		$("ul#wrapper").addClass("cover");
	}
	
	$("body").waypoint(function() {
		$(this).removeClass("start");
	});
	
	$('div#cover').waypoint(function(direction) {
	
		if(direction === "down") {
			$("body").removeClass("start cover");
		}
		else {
			$("body").addClass("cover");
		}
		
	}, {
		offset: function() {
			return -$(this).height()+$("ul#nav li").height();
		}
	});
	
	
	// EDITION OPTIONS
	$("a.clear-edition").click(function() {
		$.cookie('clearedEditions', true, { expires: 3, path: "/" });
	});
	
	// EDITION SELECTION
	$("ul#nav a[href=#change]").click(function(e) {
	
		e.preventDefault();
				
		if( $("body.edition-select-mode").length !== 0 ) {
			$("body").removeClass("edition-select-mode locked");
		} else {
			$("body").addClass("edition-select-mode locked");
		}
	});

	$("div#edition-selector ul#tabs li a").click(function() {
		$(this).parent().parent().children("li").children("a").removeClass("selected");
		$(this).addClass("selected");
		$(this).blur();
	});
	
	$("h3#close span").click(function() {
		$("body").removeClass("edition-select-mode locked");
	});

	// COLLECTING PARAGRAPHS
	$("div.paragraph-num a").click(function(e) {
		e.preventDefault();
		$(this).toggleClass("selected");
		
		collectParagraphs( $(this).parent().parent().attr('id').replace( /^\D+/g, '') );
		
	});
	
	$("ul#publish button.clear").click(function() {
		//if($.cookie('myCollection') !== undefined) {
			$.removeCookie('myCollection', { path: "/" });
		//}
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
	
		//changeHash( $(this).attr("id") );
		
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

	// PUBLISHING
	$("li#collector").click(function() {
		if( $("body.publish-mode").length !== 0 ) {
			publishMode(false);
		} else {
			publishMode(true);
		}
	});
	
	$('div#writer form textarea').keypress(function(e) {
		if (e.which === 13) {
			e.preventDefault();
			$(this).parent().submit();
		}
	});
	
	// DIMMER CLICK ACTION
	$("div#dimmer").click(function() {
	
		if( $("div.link").is(":visible") ) {
			shareMode(false, $("div.link").filter(":visible"));
		}
		else if( $("body.publish-mode").length !== 0 ) {
			publishMode(false);
		}
	});
		
	$("a.author").click(function(e) {
		e.preventDefault();
		$('html, body').animate({
			scrollTop: $("h3#author").offset().top - 128
		}, 1000);
	});

	// ON RESIZE
	$(window).resize(function() {
		resizeRibbon();
	});
	
		
});