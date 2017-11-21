jQuery(document).ready(function($) {

  //
  // QE - multiple choice question
  //

  $('#add_new_option_multiple_choice').click(function() {
  //	alert('Add new multiple choice option');
      single_course_template_multiple_choice();
    return false;
  });

  delete_option_multiple_choice();
  single_option_sortable_multiple_choice();

  //
  // QE - fill in the blank question
  //

  $('#add_new_option_fill_in_the_blank').click(function() {
  //	alert('Add new fill in the blank option');
      single_course_template_fill_in_the_blank();
    return false;
  });

  delete_option_fill_in_the_blank();
  single_option_sortable_fill_in_the_blank();

  //
  // QE - code question
  //

  $('#add_new_option_code').click(function() {
  //	alert('Add new code option');
      single_course_template_code();
    return false;
  });

  delete_option_code();
  single_option_sortable_code();

  
});


 /**
 * Generate multiple choice question template
 */
single_course_template_multiple_choice = function () {
	var order = (jQuery("#llms-single-options_multiple_choice tr").length);

	jQuery('<tr class="list_item" data-order="' + order + '" style="display: table-row;"> \
				<td> \
					<i class="fa fa-bars llms-fa-move-lesson"></i> \
					<i data-code="f153" class="dashicons dashicons-dismiss deleteBtn_multiple_choice single-option-delete"></i> \
					<input type="checkbox" name="correct_option" value="' + order + '"> \
					<label>Correct Answer</label> \
					<textarea name="option_text[]" class="option-text"></textarea> \
					<br> \
					<label>Description</label> \
					<textarea name="option_description[]" class="option-text"> </textarea> \
				</td> \
			</tr> \
				').appendTo('#llms-single-options_multiple_choice .dad-list_multiple_choice tbody').hide().fadeIn(300);

	delete_option_multiple_choice();
}

delete_option_multiple_choice = function() {
	jQuery('.deleteBtn_multiple_choice').click(function() {
	    var contentPanelId = jQuery(this).attr("class");
	    jQuery(this).parent().parent().remove();
	    order_single_options_multiple_choice();
	});
}

/**
 * Sortable function
 */
single_option_sortable_multiple_choice = function() {

    jQuery('.dad-list_multiple_choice').sortable({
    	items		: '.list_item',
    	axis 		: 'y',
    	placeholder : "placeholder",
    	cursor		: "move",
    	forcePlaceholderSize:true,
    	helper 		: function(e, tr) {
		    var jQueryoriginals = tr.children();
		    var jQueryhelper = tr.clone();
		    jQueryhelper.children().each(function(index)
		    {
		      jQuery(this).width(jQueryoriginals.eq(index).width())
		    });
		    return jQueryhelper;
		},
        start 		: function(event, ui) {
			var start_pos = ui.item.index();
			ui.item.data('start_pos', start_pos);
			var radio_checked= {};

            var radio_checked= {};
            jQuery('input[type="radio"]', this).each(function(){
                if(jQuery(this).is(':checked'))
                    radio_checked[jQuery(this).attr('name')] = jQuery(this).val();
                jQuery(document).data('radio_checked', radio_checked);
            });
		},
        update		: function(event, ui) {
            var start_pos = ui.item.data('start_pos');
            var end_pos = jQuery(ui.item).index();
            jQuery(ui.item).attr("data-order", end_pos);
        }
    }).bind('sortstop', function (event, ui) {
        var radio_restore = jQuery(document).data('radio_checked');
        jQuery.each(radio_restore, function(index, value){
        	jQuery('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
        });
        order_single_options_multiple_choice();
    });
}

order_single_options_multiple_choice = function() {
	jQuery("#llms-single-options_multiple_choice tr").each( function(index) {
		jQuery(this).attr("data-order", index);
		var option = jQuery(this).find('input[type="checkbox"]').val(index);
	});
}



 /**
 * Generate fill in the blank question template
 */
single_course_template_fill_in_the_blank = function () {
	var order = (jQuery("#llms-single-options_fill_in_the_blank tr").length);

	jQuery('<tr class="list_item" data-order="' + order + '" style="display: table-row;"> \
				<td> \
					<i class="fa fa-bars llms-fa-move-lesson"></i> \
					<i data-code="f153" class="dashicons dashicons-dismiss deleteBtn_fill_in_the_blank single-option-delete"></i> \
					<textarea name="option_text[]" class="option-text" rows="12"></textarea> \
					<br> \
					<label>Description</label> \
					<textarea name="option_description[]" class="option-text"> </textarea> \
				</td> \
			</tr> \
				').appendTo('#llms-single-options_fill_in_the_blank .dad-list_fill_in_the_blank tbody').hide().fadeIn(300);

	delete_option_fill_in_the_blank();
}

delete_option_fill_in_the_blank = function() {
	jQuery('.deleteBtn_fill_in_the_blank').click(function() {
	    var contentPanelId = jQuery(this).attr("class");
	    jQuery(this).parent().parent().remove();
	    order_single_options_fill_in_the_blank();
	});
}

/**
 * Sortable function
 */
single_option_sortable_fill_in_the_blank = function() {

    jQuery('.dad-list_fill_in_the_blank').sortable({
    	items	: '.list_item',
    	axis : 'y',
    	placeholder : "placeholder",
    	cursor : "move",
    	forcePlaceholderSize:true,
    	helper : function(e, tr) {
		    var jQueryoriginals = tr.children();
		    var jQueryhelper = tr.clone();
		    jQueryhelper.children().each(function(index)
		    {
		      jQuery(this).width(jQueryoriginals.eq(index).width())
		    });
		    return jQueryhelper;
		  },
      start : function(event, ui) {
        var start_pos = ui.item.index();
        ui.item.data('start_pos', start_pos);
        var radio_checked= {};

              var radio_checked= {};
              jQuery('input[type="radio"]', this).each(function(){
                  if(jQuery(this).is(':checked'))
                      radio_checked[jQuery(this).attr('name')] = jQuery(this).val();
                  jQuery(document).data('radio_checked', radio_checked);
              });
      },
      update : function(event, ui) {
              var start_pos = ui.item.data('start_pos');
              var end_pos = jQuery(ui.item).index();
              jQuery(ui.item).attr("data-order", end_pos);
      }
    }).bind('sortstop', function (event, ui) {
        var radio_restore = jQuery(document).data('radio_checked');
        jQuery.each(radio_restore, function(index, value){
        	jQuery('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
        });
        order_single_options_fill_in_the_blank();
    });
}

order_single_options_fill_in_the_blank = function() {
	jQuery("#llms-single-options_fill_in_the_blank tr").each( function(index) {
		jQuery(this).attr("data-order", index);
		var option = jQuery(this).find('input[type="checkbox"]').val(index);
	});

}


 /**
 * Generate code question template
 */
single_course_template_code = function () {
	var order = (jQuery("#llms-single-options_code tr").length);

	jQuery('<tr class="list_item" data-order="' + order + '" style="display: table-row;"> \
				<td> \
					<i class="fa fa-bars llms-fa-move-lesson"></i> \
					<i data-code="f153" class="dashicons dashicons-dismiss deleteBtn_code single-option-delete"></i> \
          <div><b>Тест # ' + order + '</b></div> \
          <div style="text-align: left; display: inline-block; width:49%;">Input</div><div style="text-align: left; display: inline-block; width:49%;">Output</div> \
          <textarea name ="option_text_input[]" class="option-text" style="width:49%" rows="3"></textarea> \
          <textarea name ="option_text_output[]" class="option-text" style="width:49%" rows="3"></textarea> \
					<br> \
				  <label>Description</label> \
				  <textarea name = "option_description[]" class="option-text"></textarea> \
				</td> \
		</tr> \
				').appendTo('#llms-single-options_code .dad-list_code tbody').hide().fadeIn(300);

	delete_option_code();
}

delete_option_code = function() {
	jQuery('.deleteBtn_code').click(function() {
	    var contentPanelId = jQuery(this).attr("class");
	    jQuery(this).parent().parent().remove();
	    order_single_options_code();
	});
}

/**
 * Sortable function
 */
single_option_sortable_code = function() {

    jQuery('.dad-list_code').sortable({
    	items : '.list_item',
    	axis : 'y',
    	placeholder : "placeholder",
    	cursor : "move",
    	forcePlaceholderSize : true,
    	helper : function(e, tr) {
		    var jQueryoriginals = tr.children();
		    var jQueryhelper = tr.clone();
		    jQueryhelper.children().each(function(index)
		    {
		      jQuery(this).width(jQueryoriginals.eq(index).width())
		    });
		    return jQueryhelper;
		  },
      start : function(event, ui) {
        var start_pos = ui.item.index();
        ui.item.data('start_pos', start_pos);
        var radio_checked= {};

              var radio_checked= {};
              jQuery('input[type="radio"]', this).each(function(){
                  if(jQuery(this).is(':checked'))
                      radio_checked[jQuery(this).attr('name')] = jQuery(this).val();
                  jQuery(document).data('radio_checked', radio_checked);
              });
      },
      update : function(event, ui) {
              var start_pos = ui.item.data('start_pos');
              var end_pos = jQuery(ui.item).index();
              jQuery(ui.item).attr("data-order", end_pos);
      }
    }).bind('sortstop', function (event, ui) {
        var radio_restore = jQuery(document).data('radio_checked');
        jQuery.each(radio_restore, function(index, value){
        	jQuery('input[name="'+index+'"][value="'+value+'"]').prop('checked', true);
        });
        order_single_options_code();
    });
}

order_single_options_code = function() {
	jQuery("#llms-single-options_code tr").each( function(index) {
		jQuery(this).attr("data-order", index);
		var option = jQuery(this).find('input[type="checkbox"]').val(index);
	});
}


