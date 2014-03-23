/* LOAD DEPENDENCIES FOR CODEKIT */
//@codekit-prepend "prefixfree.min.js"
//@codekit-prepend "jquery.scrollTo-1.4.3.1-min.js"
//@codekit-prepend "hyphenator.min.js"
//@codekit-prepend "waypoints.min.js"
//@codekit-prepend "jquery.cookie.js"
//@codekit-append "jquery.autosize.min.js"
//@codekit-append "sanitize.js"
//@codekit-append "isbn.js"
//@codekit-append "isbn-groups.js"

jQuery(document).ready(function($) {

/** ********* ****************************************************************/
/** FUNCTIONS ****************************************************************/
/** ********* ****************************************************************/

	function updateQueryStringParameter(uri, key, value) {

		var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";

		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
			return uri + separator + key + "=" + value;
		}
	}

	function placeCaretAtEnd(el) {

		el.focus();

		if (typeof window.getSelection !== "undefined" && typeof document.createRange !== "undefined") {
			var range = document.createRange();
			range.selectNodeContents(el);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		} else if (typeof document.body.createTextRange !== "undefined") {
			var textRange = document.body.createTextRange();
			textRange.moveToElementText(el);
			textRange.collapse(false);
			textRange.select();
		}
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

	function scrollToHash() {

		var target = window.location.hash;

		if (target) {

			setTimeout(function() {

					$(window).scrollTo(

						{ top: 0, left: 0 },

						{ onAfter: function() {

								if(target.indexOf("paragraph") !== -1) {

									$(target).addClass("linked");
									$(window).stop().scrollTo( $(target), 500, { offset: -200, easing:'easeInOutQuint', onAfter: function() { $(target).removeClass("linked"); } } );
								}
								else if (target === "#collection-info") {

									$(window).stop().scrollTo( $(target), 1500, {easing:'easeInOutQuint'} );
								}
								else if (target === "#writer") {

									writingMode(true);
								}
								else {

									if( $(target).length !== 0 ) {

										$(window).stop().scrollTo( $(target), 500, {easing:'easeInOutQuint'} );
									}
								}
							}
						}
					);
			}, 1);
		}

	}

	function resizeRibbon() {
		var ribbonWidth = $("#collector button").outerWidth();
		$("#collector span").css("border-left-width",(ribbonWidth/parseFloat($("#collector").css("font-size"))/2)+"em");
		$("#collector span").css("border-right-width",(ribbonWidth/parseFloat($("#collector").css("font-size"))/2)+"em");
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
			$(window).scrollTo( $("div#writer"), 250, { offset: -128, } );
			$("div#writer form div.textarea").get(0).focus();
		}
		else if(setting === false) {
			$("body").removeClass("locked");
			$("ul#nav").show();
			$("ul#adding-options").show();
			$("li.next-chapter").fadeTo(0,1);
			$("div#writer").hide();
			//$("div#writer form div.textarea").val("");
			$("div#dimmer").fadeOut(0);
		}
	}

	function getReferenceLink(link_or_isbn, callback) {

		//var link;

		var checkIfUrl = /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;

		var isbn = ISBN.parse(link_or_isbn);

		if( checkIfUrl.test(link_or_isbn) ) {

			callback( link_or_isbn );
		}
		else if( isbn !== null ) {

			$.getJSON("https://www.googleapis.com/books/v1/volumes?q=isbn:"+isbn.asIsbn10()+"&key=AIzaSyAy1a7DvHu0Ou7_I_9cf39zAK9S9MZNehw", function(data) {
				callback( data["items"][0]["accessInfo"]["webReaderLink"] );
			});
		}
		else {
			callback(false);
		}
	}

	function addReferenceToWriter() {

		var referenceNum = $("div#writer form div.textarea img").length + 1;

		var referenceForm = $("div#add-reference form");

		var template = referenceForm.children("input.reference-info[name=template]");

		var link = template.clone();
		var quote = template.clone();

		// verify or get ISBN link
		getReferenceLink( referenceForm.children("input[name=link-or-isbn]").attr("value"), function(linkUrl) {

			if( linkUrl !== false && linkUrl !== 'undefined' ) {

				// define link
				link.addClass("link")
				.attr("name","references["+(referenceNum-1)+"][link]")
				.attr("value", encodeURI( linkUrl ) );

				// define quote
				quote.addClass("quote")
				.attr("name","references["+(referenceNum-1)+"][quote]")
				.attr("value", encodeURI( referenceForm.children("div").children("textarea[name=quote]").val() ) );

				// save to writer form
				$("div#writer form").append(link, quote);

				// clear form
				referenceForm.children("input[name=link-or-isbn]").attr("value","");
				referenceForm.children("div").children("textarea[name=quote]").val("");

				// display reference
				//var referenceHTML = '&nbsp;<span id="reference-'+referenceNum+'" class="reference-num">'+referenceNum+'</span>&nbsp;';
				var referenceHTML = '<img id="reference-'+referenceNum+'" class="reference-num" src="http://placehold.it/30x17/ffff00/000000/&text='+referenceNum+'">';
				//var referenceHTML = '[reference id='+(referenceNum+1)+']';
				$("div#writer form div.textarea").append(referenceHTML);

				// hide #add-reference
				$("div#add-reference form").parent().hide();

				// focus writer
				$("div#writer form div.textarea").get(0).focus();
				placeCaretAtEnd( $("div#writer form div.textarea")[0] );
			}
			else {
				referenceForm.children("input[name=link-or-isbn]").addClass("error");
			}

		});
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

				$(window).animate({
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
	var sanitize = new Sanitize({
		elements: ['img'],
		attributes: {
			img: ['id', 'class', 'src']
		},
		protocols:  {}
	});
	$.cookie.json = true;

	/* INITIALISE */
	resizeRibbon();
	handleCover();
	highlightCollectedPars( getCollection() );
	updateCollector( getCollection().length );
	// $("div#edition-selector").tabs();
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

	//$("textarea").autosize();
	Hyphenator.run();
	scrollToHash();
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
			$("li.introduction span.what-is").get(0).focus();
			placeCaretAtEnd( $("li.introduction span.what-is")[0] );
		}
		else {
			$("body").addClass("cover");
			$("li.introduction span.what-is").get(0).blur();
		}

	}, {
		offset: function() {
			return -$(this).height()+$("ul#nav li").height();
		}
	});

	// INTRODUCTION
	$("li.introduction span.what-is").keydown(function(event){

		if(event.keyCode === 13) {
			event.preventDefault();
		}
		else if( $(this).html().length === 30 && event.keyCode !== 8 ) {
			event.preventDefault();
		}
	});

	// EDITION OPTIONS
/*
	$("a.clear-edition").click(function() {
		$.cookie('clearedEditions', true, { expires: 3, path: "/" });
	});
*/

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

	$("a[href=#writer]").click(function(e) {
		e.preventDefault();

		if( getUrlQueryParameter("edition") === "-1" ) {

			writingMode(true);
		}
		else {

			window.location.replace( updateQueryStringParameter(window.location.href, "edition", "-1") );
			// tuki bi se mogu #writer naštimat k se naloada
		}


	});

	$("div#writer form div.textarea").blur(function() {

		if( $("div#add-reference").is(":visible") !== true ) {

			$(this).get(0).focus();
		}
	});

	$('div#writer form div.textarea').keydown(function(e) {

		// if ENTER pressed
		if(e.keyCode === 13) {

			e.preventDefault();

			var content = $(this).text();

			// if something already written
			if( content.length > 3 ) {

				// replace reference placeholders with shortcodes
				content = $('div#writer form div.textarea').clone();

				content.children('img').each(function() {
					var refID = $(this).attr("id").replace( /^\D+/g, '');
					$(this).replaceWith("[reference id="+refID+"]");
				});

				// add hidden input field with value="content" to make post-able
				$(this).siblings("input[name=paragraph]").val( content.html() );

				// submit form
				$('div#writer form').submit();
			}
		}
		// if ESC pressed
		else if(e.keyCode === 27) {

			e.preventDefault();
			writingMode(false);
		}
	});

	// match formatting when pasting text
	$('div#writer form div.textarea').on('paste', function() {

		var obj = $(this);

		// wait for content to appear
		setTimeout(function() {

			// strip all HTML
			obj.html( sanitize.clean_node( obj[0] ) );
			placeCaretAtEnd( obj[0] );
		}, 1);
	});

	// add random
	$("a[href=#random]").click(function(e) {
		e.preventDefault();

		//var ajax_object;


		ajax_object.catID = $("li.chapter:not(.next-chapter)").attr('id').replace( /^\D+/g, '');
		ajax_object.collection = getCollection();

		var data = {
			action: 'insert_random_paragraph',
			catID: ajax_object.catID,
			collection: ajax_object.collection
		};

		$.post(ajax_object.ajax_url, data, function(response) {

			var paragraphID = $(response).attr("id").replace( /^\D+/g, '');

			handleCollection( paragraphID );

			if( getUrlQueryParameter("edition") === "-1" ) {

				window.location.href = "#paragraph-"+paragraphID;
				window.location.reload();
			}
			else {
				window.location.hash = "paragraph-"+paragraphID;
				window.location.replace( updateQueryStringParameter(window.location.href, "edition", "-1") );
			}
		});
	});

	// VOTING
	$("a.vote").click(function(e){

		e.preventDefault();

		var edition_id = $(this).attr('href').replace( /^\D+/g, '');

		$("a[href=#vote-"+edition_id+"]").each(function() {

			var voter = $(this).parent();
			var counter = voter.children("span.votes");

			var count = parseInt( counter.text() );

			// if already voted
			if( voter.hasClass("voted") ) {

				count--;

				counter.text(count);
				voter.removeClass("voted").attr("title","Like this edition.");
			}
			// if not yet voted
			else {

				count++;

				counter.text(count);
				voter.addClass("voted").attr("title","You like it!");
			}
		});

		var data = {

			action:	"vote_edition",
			nonce:	ajax_object.nonce,
			tag_id: edition_id
		};

		// Ajax call
		$.post(ajax_object.ajax_url, data, function(count){

			// update all shown vote occurances of voted edition
			$("a[href=#vote-"+edition_id+"]").siblings("span.votes").text(count);
		});

		return false;
	});

	// DELETING PARAGRAPHS
	$("li.paragraph div.paragraph-num a[href=#delete]").click(function(e) {

		e.preventDefault();

		//var ajax_object;

		ajax_object.paragraphID = $(this).parent().parent().attr('id').replace( /^\D+/g, '');

		var data = {
			action: 'delete_paragraph',
			paragraphID: ajax_object.paragraphID
		};

		$.post(ajax_object.ajax_url, data);

	});

	// PUBLISHING
	$("li#collection-info").waypoint(function(direction) {

		if(direction === "down") {

			if(getUrlQueryParameter("edition") !== "-1") {
				$("ul#nav li:not(#collector)").stop().fadeTo(100, 0);
			}
			$("li#collector").addClass("hover");
			$("li#collection-info input.title").focus();
		}
		else {
			$("ul#nav li").stop().fadeTo(250, 1);
			$("li#collector").removeClass("hover");
			$("li#collection-info input.title").blur();
		}
	}, { offset: function() {
			return $(this).height()/2;
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
		$(window).scrollTo( $("h3#author"), 1000, { offset: -128 } );
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

	$("li.next-chapter").hover(
		function() {
			$(this).children("div.dimmer").stop().fadeOut(100);
		},
		function() {
			$(this).children("div.dimmer").stop().fadeIn(250);
	});

	// ADDING REFERENCES
	$("a[href=#reference]").click(function() {

		$("div#writer div.textarea").get(0).blur();
		$("div#add-reference").show();
		$("div#add-reference input[name=link-or-isbn]").focus();
	});

	//
	$("textarea[name=quote]").keyup(function(e) {

		var form = $(this).parent().parent();

		form.css("margin-top", (-parseFloat(form.outerHeight())/16/2)+"em");

		if(e.keyCode === 27) {

			e.preventDefault();
			$("div#writer div.textarea").get(0).blur();
			$("div#add-reference").hide();
		}
	});

	$("div#add-reference form button[type=submit]").click(function() {

		addReferenceToWriter();
	});


	// TEXTAREA AUTO RESIZE
	$("textarea").keyup(function(){

		$(this).autosize().trigger('autosize.resize');
	});

	$("div#add-reference a[href=#cancel]").click(function(e) {

		e.preventDefault();

		$("div#add-reference").hide();
		$("div#writer div.textarea").get(0).focus();
	});

	$("div#add-reference").click(function(e) {

		e.preventDefault();

		$("div#add-reference").hide();
		$("div#writer div.textarea").get(0).focus();
	}).children().click(function() {
		return false;
	});
	
	
	function map(x, in_min, in_max, out_min, out_max) {
		return (x - in_min) * (out_max - out_min) / (in_max - in_min) + out_min;
	}
	
	// CLOSE COVER EFFECT
	$(window).keyup(function(e) {

		if(e.keyCode === 27 && $("body").hasClass("home")) {

			var vol = map( $(window).scrollTop(), 0, $(document).height(), 0, 1 );

			e.preventDefault();
			$(window).scrollTo(0, 1000, {easing:'easeInExpo'});
			setTimeout( function() {
				
				$("audio")[0].volume = vol;
				$("audio")[0].play();
			},975);
		}		
	});
});