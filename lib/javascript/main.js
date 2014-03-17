/* LOAD DEPENDENCIES FOR CODEKIT */
//@codekit-prepend "prefixfree.min.js"
//@codekit-prepend "hyphenator.min.js"
//@codekit-prepend "waypoints.min.js"
//@codekit-prepend "jquery.cookie.js"
//@codekit-prepend "jquery.autosize.min.js"

jQuery(document).ready(function($) {

/** ********* ****************************************************************/
/** FUNCTIONS ****************************************************************/
/** ********* ****************************************************************/

	function getUrlQueryParameter(name) {
	    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	        results = regex.exec(location.search);
	    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	
	function moveElement(array, fromIndex, toIndex) {
		array.splice(toIndex, 0, array.splice(fromIndex, 1)[0] );
		return array;
	}
	
	
	function resizeRibbon() {
		var ribbonWidth = $("#collector button").outerWidth();
		$("#collector span").css("border-left-width",(ribbonWidth/16/2)+"em");
		$("#collector span").css("border-right-width",(ribbonWidth/16/2)+"em");
	}
	
	function handleCover() {
	
		if( $("div#cover").length !== 0 ) {
	
			if( $("body").scrollTop() === 0 ) {
				$("body").removeClass("start");
			}
			else {
	/*			$("body").removeClass("start");	*/
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
			$("div#writer form textarea").val("");
			$("div#dimmer").fadeOut(0);
		}
	}
	
	function shareMode(setting, obj) {
	
		if(setting === true) {
	
			obj.addClass("active");
			obj.find("input").focus().select();
		}
		else if(setting === false) {
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
	
	function updateCollector(num) {
		$("#collector button").html(num);
	}
	
	function getCollection() {
	
		// if already saved
		if( $.cookie('myCollection') !== undefined ) {
			
			// get what was saved
			return $.cookie('myCollection');
		}
		else {
		
			// 
			return [];
		}
	}
	
	function highlightCollectedPars(collection) {
	
		var i;
		for (i = 0; i < collection.length; ++i) {
			
			// if paragraph exists
			if( $("li#paragraph-"+collection[i]).length !== 0 ) {
				// mark as selected
				$("li#paragraph-"+collection[i]).children("div.paragraph-num").children("a").addClass("selected");
			}
		}
	}
	
	function saveCollection(collection) {
	
		// turn array into a string
		var string = collection;
		
		// save collection to cookie
		$.cookie('myCollection', string, {
			expires: 3650, // in 10 years
			path: "/"
		});
	}
	
	function hideParagraph(id) {
	
		$("li#paragraph-"+id).animate({
			opacity: 0
		}, 150).animate({ height: 0 }, 150);
		
		setTimeout(function() { $("li#paragraph-"+id).hide(); }, 300);
	}
	
	function handleCollection(id) {
	
		var collection = getCollection();
	
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
	
			// hide
			if( getUrlQueryParameter("edition") === "-1" ) {
				hideParagraph(id);
			}
		}
	
		// show collected amount
		updateCollector(collection.length);
	
		// if collection still exists
		if( collection.length > 0 ) {
			// save the updated collection
			saveCollection(collection);
		}
		// if not
		else {
			// wipe it
			$.removeCookie('myCollection', { path: "/" });
		}
	
	}
	
	function updateSortOrder(movedParagraph, start_pos, end_pos) {
	
		// get paragraphIDs from cookie
		var collection = getCollection();
	
		// get moving paragraph ID
		var parID = movedParagraph.attr("id").replace( /^\D+/g, '');
		var parPos = $.inArray(parID, collection);
	
		// get point of reference ID
		var refID = movedParagraph.next().attr("id");
	
		// if its the last paragraph
		if( refID === undefined ) {
			// move to the end of paragraphIDs
			moveElement(collection, parPos, collection.length-1);
		}
		// if not
		else {
	
			// extract reference ID
			refID = refID.replace( /^\D+/g, '');
	
			// find point of reference in paragraphIDs
			var refPos = $.inArray(refID, collection);
	
			// if moved down
			if( start_pos < end_pos ) {
				// change sort order
				moveElement(collection, parPos, refPos-1);
			}
			// if moved up
			else {
				// change sort order
				moveElement(collection, parPos, refPos);
			}
		}
	
		// save changes back to cookie
		saveCollection(collection);
	}
	
/** ************* ************************************************************/
/** END FUNCTIONS ************************************************************/
/** ************* ************************************************************/

	var currParagraphNum = 0;
	$.cookie.json = true;

	/* INITIALISE */
	resizeRibbon();
	handleCover();
	highlightCollectedPars( getCollection() );
	updateCollector( getCollection().length );
	$("div#edition-selector").tabs();
	var allParagraphNum = $("ul#wrapper li p").length;

	setParagraphNum(0, allParagraphNum);

    $( "li#content ul.wrapper" ).sortable({
        start: function(event, ui) {
            var start_pos = ui.item.index();
            ui.item.data('start_pos', start_pos);
        },
        update: function(event, ui) {
            var start_pos = ui.item.data('start_pos');
            var end_pos = $(ui.item).index();
            //alert(start_pos + ' -> ' + end_pos);
            updateSortOrder(ui.item, start_pos, end_pos);
        },
		axis: "y",
		disabled: true
	});

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
			$("li.introduction input").focus();
			$("li.introduction input").val($("li.introduction input").val());
		}
		else {
			$("body").addClass("cover");
			$("li.introduction input").blur();
		}

	}, {
		offset: function() {
			return -$(this).height()+$("ul#nav li").height();
		}
	});

	// INTRODUCTION
	$("li.introduction input").keydown(function(event){
		if(event.keyCode === 13) {
			event.preventDefault();
			return false;
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

		handleCollection( $(this).parent().parent().attr('id').replace( /^\D+/g, '') );

	});

	$("li#collection-info button.clear").click(function() {
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
	$("ul.more li.share a").click(function(e) {
		e.preventDefault();
		shareMode(true, $(this).parent().parent());
	});

	$("ul.more li.link input").click(function() {
		$(this).select();
	});

	$("ul.more li.link input").blur(function() {
		$(this).parent().parent().parent().removeClass("active");
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

	$("div#writer form textarea").blur(function() {
		writingMode(false);
	});

	$('div#writer form textarea').keypress(function(e) {
		
		if (e.which === 13) {
		
			e.preventDefault();
			$(this).parent().submit();
			//saveParagraph( $(this).val(), $("li.chapter").attr("id").replace( /^\D+/g, '') );
			//writingMode(false);
		}
	});
	
	// DELETING PARAGRAPHS
	$("li.paragraph div.paragraph-num a[href=#delete]").click(function(e) {
	
		e.preventDefault();
				
		ajax_object.paragraphID = $(this).parent().parent().attr('id').replace( /^\D+/g, '');
		
		var data = {
			action: 'delete_paragraph',
			paragraphID: ajax_object.paragraphID
		}

		$.post(ajax_object.ajax_url, data, function(response) {
			
			//alert('Got this from the server: ' + response);
		});
		
	});

	// PUBLISHING
	$("li#subscribe").waypoint(function(direction) {

		if(direction === "down") {
			$("ul#nav li:not(#collector)").stop().fadeTo(100, 0);
			$("li#collector").addClass("hover");
			$("li#collection-info input.title").focus();
		}
		else {
			$("ul#nav li").stop().fadeTo(250, 1);
			$("li#collector").removeClass("hover");
			$("li#collection-info input.title").blur();
		}
	}, { offset: function() {
			return -$(this).height() / 2;
		}
	});

/*
	$("li#collector").click(function() {
		if( $("body.publish-mode").length !== 0 ) {
			publishMode(false);
		} else {
			publishMode(true);
		}
	});
*/

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

	//
	$("li#collector").hover(
		function() {
			$("div.paragraph-num").addClass("hover");
		},
		function() {
			$("div.paragraph-num").removeClass("hover");
		}
	);

	// ON RESIZE
	$(window).resize(function() {
		resizeRibbon();
	});


});