//@codekit-prepend "prefixfree.min.js"
//@codekit-prepend "hyphenator.min.js"
//@codekit-prepend "waypoints.min.js"
//@codekit-prepend "jquery.cookie.js"
//@codekit-prepend "jquery.autosize.min.js"

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

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

function updateCollector(num) {
	$("#collector div").html(num);
}

function getCollection() {

	// if already saved
	if( $.cookie('myCollection') !== undefined ) {
		// get what was saved
		return $.parseJSON( decodeURI( $.cookie('myCollection') ) );
	}
	else {
		return [];
	}
}

function appendParagraph(paragraph) {
	
	if( getUrlQueryParameter("edition") === "-1" ) {
		
		var content = '<li id="paragraph-U1" class="pure-g paragraph unpublished"><div class="pure-u-1-12 paragraph-num system"><a href="" class="selected">&times; delete</a></div><div class="pure-u-1-6"></div><p class="pure-u-5-12 hyphenate">'+paragraph+'</p><div class="pure-u-1-4 collection-count system">(0x)</div><ul class="pure-u-1-12 more system"><li class="move"><span>&equiv; </span><a href="">MOVE</a></li><li class="share"><span>&infin; </span><a href="">SHARE</a></li></ul><div class="link"><div class="pure-u-1-6"></div><div class="pure-u-2-3 separator"></div><div class="pure-u-1-6"></div><div class="pure-u-1-4"></div><div class="pure-u-1-2 content"><h2>Link to paragraph #3</h2><form><input type="text" value="http://www.postdigitalreader.com/reactive-environments/?paragraph=3&edition=XXXXXXXX"></form></div> <!-- EDIT THIS!!!! --><div class="pure-u-1-4"></div><div class="pure-u-1-6"></div><div class="pure-u-2-3 separator"></div><div class="pure-u-1-6"></div></div></li>';
	
		
		$("li#content ul.wrapper").append(content);
	
	}
	
}

function addAndhighlightCollectedPars(collection) {

	var i;
	for (i = 0; i < collection.length; ++i) {
		
		// if value is paragraph id
		if( isNumber(collection[i]) ) {
			// if paragraph exists
			if( $("li#paragraph-"+collection[i]).length !== 0 ) {
				// mark as selected
				$("li#paragraph-"+collection[i]).children("div.paragraph-num").children("a").addClass("selected");
			}
		}
		// if value is paragraph object that is part of this chapter
		else if( $("li#chapter-"+collection[i].chapter_id).length !== 0 ) {
			
			// find previous paragraph
			
				// append unpublished paragraph
				appendParagraph(collection[i].content);

		}
	}
}

function saveCollection(array) {
	// turn array into a string
	var collection = encodeURI( JSON.stringify(array) );
	
	// save collection to cookie
	$.cookie('myCollection', collection, {
		expires: 3650, // in 10 years
		path: "/",
		json: true
	});
}

function saveParagraph(paragraph, chapterID) {

	var collection = getCollection();
	
	collection.push({ chapter_id: chapterID, content: paragraph });

	appendParagraph(paragraph);	
	updateCollector(collection.length);
	saveCollection(collection);
}

function deleteParagraph(paragraph) {
	
	var collection = getCollection();
	
	// find and remove paragraph in collection
	$.grep(collection, function(el, i) { return el.content === paragraph; });
	
	console.log("Jaz ga lahko samo občudujem. Japonci so sicer po konstituciji, načinu prehranjevanja in drugih elementih dalj časa mladeniči. Imajo tak način življenja. Če jih pogledate, se vam zazdi, da se postarajo šele pri 60 letih, pri 50 še nimajo gub. Nekaj je torej že v sami genetiki, zaradi česar lahko dlje vztrajajo. Seveda še bolj pomemben fenomen pa je, da je pri teh letih našel motivacijo za vrhunske dosežke. Kasai me je navdušil že lani v Planici, ko je padel. Zdravnik mu je odsvetoval, da bi še nastopal, a tega mu nihče ni mogel preprečiti. Pri teh letih ima motivacijo na zelo visoki ravni, kar mu omogoča tako vrhunske dosežke." === "Jaz ga lahko samo občudu­jem. Japonci so sicer po kon­sti­tu­ciji, načinu prehran­je­vanja in drugih el­e­men­tih dalj časa mladeniči. Imajo tak način živl­jenja. Če jih pogle­date, se vam zazdi, da se postarajo šele pri 60 letih, pri 50 še ni­majo gub. Nekaj je torej že v sami genetiki, zaradi česar lahko dlje vz­tra­jajo. Seveda še bolj pomem­ben fenomen pa je, da je pri teh letih našel mo­ti­vacijo za vrhunske dosežke. Kasai me je navdušil že lani v Planici, ko je padel. Zdravnik mu je odsve­to­val, da bi še nastopal, a tega mu nihče ni mogel preprečiti. Pri teh letih ima mo­ti­vacijo na zelo vi­soki ravni, kar mu omogoča tako vrhunske dosežke.");
	
	// remove from collection
	//collection.splice(pos, 1);
	
	// update cookie
	updateCollector(collection.length);
	saveCollection(collection);
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
			$("li#paragraph-"+id).animate({ opacity: 0 }, 150).animate({ height: 0 }, 150);
			setTimeout(function() { $("li#paragraph-"+id).hide(); }, 300);
		}
	}

	// show collected amount
	updateCollector(collection.length);
	
	if( collection.length > 0 ) {
	
		saveCollection(collection);
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

function updateSortOrder(movedParagraph, start_pos, end_pos) {
	
	// get paragraphIDs from cookie
	var paragraphIDs = getCollection();
	
	// get moving paragraph ID
	var parID = movedParagraph.attr("id").replace( /^\D+/g, '');
	var parPos = $.inArray(parID, paragraphIDs);

	// get point of reference ID
	var refID = movedParagraph.next().attr("id");
	
	// if its the last paragraph
	if( refID === undefined ) {
		// move to the end of paragraphIDs
		moveElement(paragraphIDs, parPos, paragraphIDs.length-1);
	}
	// if not
	else {
	
		// extract reference ID
		refID = refID.replace( /^\D+/g, '');
		
		// find point of reference in paragraphIDs
		var refPos = $.inArray(refID, paragraphIDs);
		
		// if moved down
		if( start_pos < end_pos ) {
			// change sort order
			moveElement(paragraphIDs, parPos, refPos-1);
		}
		// if moved up
		else {
			// change sort order
			moveElement(paragraphIDs, parPos, refPos);
		}
	}
	
	// save changes back to cookie
	saveCollection(paragraphIDs);
}

$(document).ready(function() {

	var allParagraphNum;
	var currParagraphNum = 0;
		
	/* INITIALISE */
	resizeRibbon();
	handleCover();
	addAndhighlightCollectedPars( getCollection() );
	updateCollector( getCollection().length);
	$("div#edition-selector").tabs();
	allParagraphNum = $("ul#wrapper li p").length;
	
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
		
		if( $(this).parent().parent().hasClass("unpublished") ) {
			deleteParagraph( $(this).parent().parent().children("p").html() );
		}
		
		handleCollection( $(this).parent().parent().attr('id').replace( /^\D+/g, '') );
		
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
/*
	$("a.cancel").click(function(e) {
		e.preventDefault();
		writingMode(false);
	});
*/
	$("div#writer form textarea").blur(function() {
		writingMode(false);
	});

	$('div#writer form textarea').keypress(function(e) {
		if (e.which === 13) {
			e.preventDefault();
			saveParagraph( $(this).val(), $("li.chapter").attr("id").replace( /^\D+/g, '') );
			writingMode(false);
		}
	});

	// PUBLISHING
	$("li#collector").click(function() {
		if( $("body.publish-mode").length !== 0 ) {
			publishMode(false);
		} else {
			publishMode(true);
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