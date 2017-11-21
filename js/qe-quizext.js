jQuery(document).ready(function ($) {

  
  // Main AJAX URL
  var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';
		
  // URL to check code
  var check_code_url = wpajax_url + '?action=qe_run_code';
  
  var qe_code;
  var qe_lang;
  var qe_input, qe_output;
  var qe_code_cpp, qe_code_pascal, qe_code_csharp, qe_code_python2, qe_code_python3, qe_code_java;
  
  var qe_container;
  
  //
  // Get user preferences
  //
  if (typeof(Storage) !== "undefined") {
    // Code for localStorage/sessionStorage
    if (localStorage.getItem("qe_preferred_lang")) {
      window.qe_preferred_lang = localStorage.getItem("qe_preferred_lang");
    } else {
      window.qe_preferred_lang = "qe_code_cpp";
    }	
  } else {
	 window.qe_preferred_lang = "qe_code_cpp";
  }

  
  //
  // Simple jQuery plugin to transform select elements into Select2-powered elements to query for Groups via AJAX
  // @param    obj   options  options passed to Select2
  //         each default option will pulled from the elements data-attributes
  // @return   void
  //
	jQuery.fn.llmsGroupsSelect2 = function( options ) {

		var self = this,
			options = options || {},
			defaults = {
				allow_clear: true,
//				enrolled_in: '',
				multiple: false,
//				not_enrolled_in: '',
				placeholder: 'Выберите группу',
				width: '100%',
			};

		jQuery.each( defaults, function( setting ) {
			if ( self.attr( 'data-' + setting ) ) {
				options[ setting ] = self.attr( 'data-' + setting );
			}
		} );

		options = jQuery.extend( defaults, options );


 if ( typeof this.llmsSelect2 !== "undefined" ) {
   
    console.log ( 'this.llmsSelect2' );
    
		this.llmsSelect2({
			allowClear: options.allow_clear,
			ajax: {
				dataType: 'JSON',
				delay: 250,
				method: 'POST',
				url: window.ajaxurl,
				data: function( params ) {
					return {
						_ajax_nonce: wp_ajax_data.nonce,
						action: 'qe_query_groups',
						page: params.page,
//						not_enrolled_in: params.not_enrolled_in || options.not_enrolled_in,
//						enrolled_in: params.enrolled_in || options.enrolled_in,
						term: params.term,
					};
				},
				processResults: function( data, params ) {
					return {
						results: jQuery.map( data.items, function( item ) {

							return {
								text: item.name,
								id: item.id,
							};

						} ),
						pagination: {
							more: data.more
						}
					};

				},
			},
			cache: true,
			placeholder: options.placeholder,
			multiple: options.multiple,
			width: options.width,
		});
  
 } // if ( typeof this.llmsSelect2 !== "undefined" )

		return this;

	};
  
  jQuery( '#llms-groups-ids-filter' ).llmsGroupsSelect2( {
				multiple: true,
				placeholder: 'Фильтровать по группе(ам)'
	} );
  
  
  //
  // Fix bug with disappearing youtube player (in Chrome)
  //
  if ( jQuery(".center-video>iframe").length ) {
	console.log ("Refreshing video");
	setTimeout(function() {
		var qe_iframe = jQuery(".center-video>iframe").first();
	  	qe_iframe.attr("style", "float: left !important").hide();
		console.log ("Refreshing video end 1");
	}, 100)
	
	setTimeout(function() {
		var qe_iframe = jQuery(".center-video>iframe").first();
	    qe_iframe.attr("style", "float: right !important").show();
		console.log ("Refreshing video end 2");
	}, 850)
  }
    
  //
  // Set default input if required (for empty inputs)
  //
  jQuery(".qe_input").each(function (index, value) { 
    window.qe_input = this;  
	if ( window.qe_input.value == "" ) {
      window.qe_input.value = "10\n20\n30";
    }  
  });
  

  //
  // Set default output value
  //
  jQuery(".qe_output").each(function (index, value) { 
    window.qe_output = this;  
	window.qe_output.innerHTML = "Waiting...";
  });

  
  //
  // Use preferred (if defined) or default language
  //
  if ( jQuery(".qe_tablinks."+window.qe_preferred_lang).length ) {
  	qe_openCode(null, window.qe_preferred_lang);
  } else {
    if ( jQuery("#qe_defaultOpen").length ) {
      document.getElementById("qe_defaultOpen").click();
    }
  }
  
  //
  // Handle click event for the Run buttons
  //
  jQuery(".qe_run_button:not(.qe_bound)").addClass('qe_bound').click( function (e) {

    e.preventDefault();
     
    console.log ( 'Run' );
    
    // Get ID
    qe_id = this.id;
    qe_id = qe_id.replace("qe_run_button", "");
    console.log ( qe_id );
	
    /* $form = jQuery(this); */
    window.qe_container = jQuery( "#qe_code_area" );

    // Switch to required set of code editors
    qe_highlightSyntax(qe_id);
	
    // Get code from the ACE editor
    qe_code = window.qe_code_cpp;
    if (window.qe_lang == 'pascal') {
      qe_code = window.qe_code_pascal;
    } else if (window.qe_lang == 'csharp') {
      qe_code = window.qe_code_csharp;
    } else if (window.qe_lang == 'python2') {
      qe_code = window.qe_code_python2;
    } else if (window.qe_lang == 'python3') {
      qe_code = window.qe_code_python3;
    } else if (window.qe_lang == 'java') {
      qe_code = window.qe_code_java;
    }
    jQuery("input[id=qe_code_string" + qe_id + "]").val(qe_code.getValue());

    // Get INPUT from the textarea
    window.qe_input = jQuery("#qe_input" + qe_id).val();
    jQuery("input[id=qe_input_string" + qe_id + "]").val( window.qe_input );

    // Serialize data (encode a set of form elements as a string for submission)
    // var form_data = $form.serialize();
    var form_data = { 
          qe_quiz: jQuery("input[id=qe_id" + qe_id + "]").val(), 
          qe_code_string: jQuery("input[id=qe_code_string" + qe_id + "]").val(), 
          qe_input_string: jQuery("input[id=qe_input_string" + qe_id + "]").val(),
          qe_lang_string: jQuery("input[id=qe_lang_string" + qe_id + "]").val()
        };
    
    // Show message	
    if ( jQuery("#qe_output" + qe_id).length ) {
      window.qe_output = jQuery("#qe_output" + qe_id);
      window.qe_output.text("Executing... Please wait.");
    }
    
    
    jQuery.ajax({
      method : 'post',
      url : check_code_url,
      data : form_data,
      dataType : 'json',
      cache : false,
      success : function ( data, textStatus ) {

        qe_toggle_loader( 'hide', '', qe_id  );
        
        if (data.status == 1) {
      //		  $form[0].reset();
      //		  alert("SUCCESS: " + data.message);
            jQuery("#qe_output" + qe_id).text(qe_format_rextester_output(data.message));

        } else {
          alert(data.message);		  
        }
      },
      error : function ( jqXHR, textStatus, errorThrown ) {
      },
      beforeSend: function () {

				qe_toggle_loader( 'show', 'Выполняем код...', qe_id );

			}

    });

    // Prevent further submission
    return false;

  });
  
  
  //
  // Manage questions in the group
  //
  
  // Add a new question on question click
  jQuery( '#qe_add_new_question' ).on( 'click', function( e ) {
    e.preventDefault();
    qe_add_new_question( $( this ) );
  } );
  
  // add sorting ability
  if ( jQuery( '.qe_question-list' ).length ) {
  
    jQuery( '.qe_question-list' ).sortable( {

      axis: 'y',
      cursor: 'move',
      forcePlaceholderSize: true,
      placeholder: 'placeholder',
      items: '.list_item',

      helper: function( e, tr ) {

        var $originals = tr.children(),
          $helper = tr.clone();

        $helper.children().each( function( i ) {

          jQuery( this ).width( $originals.eq( i ).width() );

        } );

        return $helper;
	   },
  
	   start: function( e, ui ) {
  
       ui.item.data( 'start_pos', ui.item.index() );
  
	   }

  	} );
      
  }
  
  //
  // Retrieve the Question ID from any element inside a tr.llms-question element
  // @example      var id = $( '.element' ).getQuestionId();
  // @return int
  //
	jQuery.fn.qe_getQuestionId = function() {

		var $q = this.closest( 'tr.llms-question' );

		if ( ! $q.length ) {
			return 0;
		}

		return $q.attr( 'data-question-id' );

	};


  
  //
  // Initialize select2 on select elements
  // @return obj
  //
	jQuery.fn.qe_llmsSelect2ify = function() {

		this.llmsSelect2({
			allowClear: false,
			ajax: {
				dataType: 'JSON',
				delay: 250,
				method: 'POST',
				url: window.ajaxurl,
				data: function( params ) {
					return {
						term: params.term,
						page: ( params.page ) ? params.page - 1 : 0, // 0 index the pages to make it simpler for the database query
						action: 'query_quiz_questions',
						post_id: self.quiz_id,
					};
				},
				processResults: function( data, params ) {
					return {
						results: $.map( data.items, function( item ) {

							return {
								text: item.name,
								id: item.id,
							};

						} ),
						pagination: {
							more: data.more
						}
					};

				},
			},
			cache: true,
			placeholder: 'Select a Question',
			multiple: false,
			width: '100%',
		});

		return this;

	};
  
  var $delegate = jQuery( '#qe_llms-single-options' );
  
  // setup all existing (php loaded) questions as select2 elements
  if ( jQuery( '.llms-question select' ).length ) {
  	$delegate.find( '.llms-question select' ).qe_llmsSelect2ify();
  }

  //
  // When a select item changes update an HTML data-attr with the ID of the question
  // Allows easy access to the question id from any element inside a question tr
  //
  $delegate.on( 'change', '.llms-question-select', function( ) {

    var $el = jQuery( this );
    $el.closest( 'tr.llms-question' ).attr( 'data-question-id', $el.val() );

  } );
  
  // handle click event for the edit icon
  $delegate.on( 'click', '.llms-fa-edit', function( e ) {

    e.preventDefault();
    window.open( self.qe_get_question_edit_link( jQuery( this ).qe_getQuestionId() ) );

  } );

  
  // Delete question
  $delegate.on( 'click', '.llms-remove-question', function( e ) {

    jQuery( this ).closest( 'tr.llms-question' ).remove();

  } );

  
  
} (jQuery));


//
// Retrieve the URL to edit a question post type
// @param  int    question_id   WP Post ID of the question
// @return string
//
function qe_get_question_edit_link ( question_id ) {

  var link = window.llms.admin_url + 'post.php?action=edit&post=' + question_id;

  return link;

}


//
// Handle the click event for adding a new question
// @param obj   $btn   jQuery selector of the clicked button
//
function qe_add_new_question ( $btn ) {

  var $html = qe_get_question_html();

  $html.find( 'select' ).qe_llmsSelect2ify( );

  $html.appendTo( '#qe_llms-single-options .qe_question-list tbody' ).hide().fadeIn( 300 );

}

function qe_get_question_html () {

  return jQuery( jQuery( '#qe_llms-single-question-template' ).html() ).find( 'tr' );
  
}


function qe_format_rextester_output(str) {
  	
  // Parse a JSON string, constructing the JavaScript value or object 
  // described by the string  
  var obj = JSON.parse(str);
  
    var qe_res_str = "";
    if(obj.Warnings != null)
    {
        qe_res_str += ("Warnings:\n" + obj.Warnings.replace(/\r/g, "") + '\n');
    }
    if(obj.Errors != null)
    {
        qe_res_str += ("Errors:\n" + obj.Errors.replace(/\r/g, "") + '\n');
    }
    if(obj.Result != null)
    {
        qe_res_str += ("Program output is:\n" + obj.Result.replace(/\r/g, ""));
    }
    if(obj.Files != null)
    {
        for (var key in obj.Files) 
        {             
            var qe_img_div = jQuery(document.createElement('div'));
            var qe_img = jQuery(document.createElement('img'));
            qe_img.attr('src', "data:image/png;base64," + obj.Files[key]).height(600).width(700);
            qe_img.appendTo(qe_img_div);
            qe_img_div.appendTo(jQuery('#Files'));
        }
    }
    return qe_res_str;
}


function qe_openCode(evt, codeName) {
    var i, tabcontent, tablinks;
    if (codeName == "qe_code_cpp") {
        window.qe_lang = "cpp";
    } else if (codeName == "qe_code_pascal") {
        window.qe_lang = "pascal";
    } else if (codeName == "qe_code_csharp") {
        window.qe_lang = "csharp";
    } else if (codeName == "qe_code_python2") {
        window.qe_lang = "python2";
    } else if (codeName == "qe_code_python3") {
        window.qe_lang = "python3";
    } else if (codeName == "qe_code_java") {
        window.qe_lang = "java";
    }
//    jQuery("input[id=qe_lang_string]").val(window.qe_lang);
    jQuery(".qe_lang_string").val(window.qe_lang);
  
   	/* Set user preferences */
  	window.qe_preferred_lang = codeName;
	if (typeof(Storage) !== "undefined") {
	  // Code for localStorage/sessionStorage
	  localStorage.setItem("qe_preferred_lang", codeName);
	}

    
    tabcontent = document.getElementsByClassName("qe_tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
  
    tabcontent = document.getElementsByClassName("qe_tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
	  if (tabcontent[i].id.indexOf(codeName) >= 0) {
	  	tabcontent[i].style.display = "block";
	  }
    }

    tablinks = document.getElementsByClassName("qe_tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
      tablinks[i].style.backgroundColor = "#f1f1f1";
      tablinks[i].style.color = "#222";
      tablinks[i].style.fontWeight = "normal";
	  
	  if (tablinks[i].className.indexOf(codeName) >= 0) {
		tablinks[i].className += " active";
		tablinks[i].style.backgroundColor = "#4a3768";
		tablinks[i].style.color = "#ffffff";
		tablinks[i].style.fontWeight = "bold";
	  }
    }
  
    qe_highlightSyntax ();
  
    if ( jQuery(".qe_output").length ) {
      jQuery(".qe_output").text('Waiting...');
    }
}


function qe_highlightSyntaxSingle ( codeObj, modeStr ) {
  
        codeObj.setTheme("ace/theme/textmate");         // выбираем тему оформления для подсветки синтаксиса
        codeObj.getSession().setMode(modeStr);          // говорим что код надо подсвечивать как C++ код
        codeObj.setShowPrintMargin(false);              // опционально: убираем вертикальную границу в 80 сиволов
        codeObj.setOptions({
            maxLines: Infinity,                         // опционально: масштабировать редактор вертикально по размеру кода
            fontSize: "10pt",                           // опционально: размер шрифта ставим побольше
        });
        codeObj.$blockScrolling = Infinity;             // отключаем устаревшие, не поддерживаемые фишки редактора
}


function qe_highlightSyntax ( qe_id ) {
  // Default value = ""
  qe_id = qe_id || "";
  
  // If no ID, search by substring at the beginning
  if (qe_id == "") {
	qe_search = "^";
  } else {
	qe_search = "";
  }
  
  jQuery("div[id" +qe_search+ "=qe_code_cpp" +qe_id+ "]").each(function (index, value) { 
//	console.log('div' + index + ':' + jQuery(this).attr('id')); 
    window.qe_code_cpp = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_cpp, "ace/mode/c_cpp");
  });
  jQuery("div[id" +qe_search+ "=qe_code_pascal" +qe_id+ "]").each(function (index, value) { 
    window.qe_code_pascal = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_pascal, "ace/mode/pascal");
  });
  jQuery("div[id" +qe_search+ "=qe_code_csharp" +qe_id+ "]").each(function (index, value) { 
    window.qe_code_csharp = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_csharp, "ace/mode/csharp");
  });
  jQuery("div[id" +qe_search+ "=qe_code_python2" +qe_id+ "]").each(function (index, value) { 
    window.qe_code_python2 = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_python2, "ace/mode/python");
  });
  jQuery("div[id" +qe_search+ "=qe_code_python3" +qe_id+ "]").each(function (index, value) { 
    window.qe_code_python3 = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_python3, "ace/mode/python");
  });
  jQuery("div[id" +qe_search+ "=qe_code_java" +qe_id+ "]").each(function (index, value) { 
    window.qe_code_java = ace.edit(this);  
    qe_highlightSyntaxSingle (window.qe_code_java, "ace/mode/java");
  });


}


// Show or hide the "loading" spinner with an option message
// @param    string   display  show|hide
// @param    string   msg      text to display when showing
// @return   void
function qe_toggle_loader ( display, msg, qe_id ) {
  // Default value = ""
  qe_id = qe_id || "";
  
  window.qe_container = jQuery( "#qe_code_area" + qe_id );

  if ( 'show' === display ) {

    msg = msg || 'Loading...';

    // this.$container.empty();
    LLMS.Spinner.start( window.qe_container );
    window.qe_container.append( '<div class="qe_loading_area">' + LLMS.l10n.translate( msg ) + '</div>' );

  } else {

    LLMS.Spinner.stop( window.qe_container );
    window.qe_container.find( '.qe_loading_area' ).remove();
  }

}