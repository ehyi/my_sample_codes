
jQuery(document).ready(function($) {

    $("#survey").validate();

	$("#zip").live('keyup',function(){ 
        do_school_zip(); 
    });
	$('#zip').live('blur',function(){ 
        do_school_zip(); 
    });

	if($('#zip').val().length>4){
		do_school_zip();
		//alert('z');
		if($('#schidtemp').val()!=''){
			do_school_select($('#schidtemp').val());
		}
	}

    $('#school').live('change', function() {
		var school_select = $('#school option:selected').val();
		do_school_select(school_select);
	});    
    
	function do_school_zip(){
		if($('#zip').val().length>4){
	 		var selectval = $.get('/forms/i.php/school_by_zip', { zip: $('#zip').val() },  function( html ) { 
	 			$('#zip-load').html(html);
	 		});
	 	}
	}    
    
	function do_school_select(school_id){
		$.ajax({
				type: "GET",
				url: "/forms/i.php/school_selected",
				data: "pid="+school_id,
				cache: false,
				success: function(html){
					$('#phone-load').html(html);
					$('#continue-off-btn').hide();
					$('#continue-btn').show();
				},
				error: function(){
					// $('#school-load').html("Error");
				}
			});
	}

});

