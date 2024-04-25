var full_url_js=(("https:"==document.location.protocol?"https://":"http://")+window.location.hostname+("81"==document.location.port?":81":""));

(function() {
    const idleDurationSecs = 1200;
    //Local Setting
    const redirectUrl = full_url_js+'/exam_latest/public/admin/logout_time';
    //Live EDMS
    // const redirectUrl = full_url_js+'/edms/public/admin/logout_time';
    //Staging EDMS
    // const redirectUrl = full_url_js+'/stageedms/public/admin/logout_time';
    
    let idleTimeout;

    const resetIdleTimeout = function() {
        if(idleTimeout) clearTimeout(idleTimeout);
        idleTimeout = setTimeout(() => location.href = redirectUrl, idleDurationSecs * 1000);
    };

    // Init on page load
    resetIdleTimeout();
    window.onmousemove = resetIdleTimeout;
    window.onkeypress = resetIdleTimeout;
    window.click = resetIdleTimeout;
    window.onclick = resetIdleTimeout;
    window.touchstart = resetIdleTimeout;
    window.onfocus = resetIdleTimeout;
    window.onchange = resetIdleTimeout;
    window.onmouseover = resetIdleTimeout;
    window.onmouseout = resetIdleTimeout;
    window.onmousemove = resetIdleTimeout;
    window.onmousedown = resetIdleTimeout;
    window.onmouseup = resetIdleTimeout;
    window.onkeypress = resetIdleTimeout;
    window.onkeydown = resetIdleTimeout;
    window.onkeyup = resetIdleTimeout;
    window.onsubmit = resetIdleTimeout;
    window.onreset = resetIdleTimeout;
    window.onselect = resetIdleTimeout;
    window.onscroll = resetIdleTimeout;

})();


function callAjax(obj,url,divId,callback){
	$('#'+divId).html('Please Wait......');
    $.get(url,
    {
        id: obj.value
    },
    function(data, status){
        if(status=="success")
		{
			$('#'+divId).html(data);
			if(obj.getAttribute('no-add-form')!="true")
				callJqueryDefault(divId);
		 
			if(obj.getAttribute('multiselect-form')=="true"){
				$("#"+divId).find('.multiselect').selectpicker();
			}
			if(obj.getAttribute('duallistbox')=="true"){
				$("#"+divId).find('.duallistbox').bootstrapDualListbox();
			}
			if(callback)
				callback();
			if(obj.getAttribute('data-table'))
				{
					stateSave: true;
									
				$("#"+obj.getAttribute('data-table')).DataTable({
					'iDisplayLength': 10, 
				});				
				  // Add event listener for opening and closing details
				   
				
			}
			if(obj.getAttribute('data-table-excel'))
				{
					stateSave: true;
									
				$("#"+obj.getAttribute('data-table-excel')).DataTable({
					'iDisplayLength': 10,
					"bFilter": false,
					dom: 'Bfrtip',
                        buttons: [
                        'excel',
                        ]
				}); 
				
			}
			if(obj.getAttribute('data-table-excel-2'))
				{
					stateSave: true;
									
				$("#"+obj.getAttribute('data-table-excel-2')).DataTable({
					'iDisplayLength': 10,
					"bFilter": false,
					dom: 'Bfrtip',
                        buttons: [
                        'excel','print',
                        ]
				}); 
				
			}
			
				

			
			if(obj.getAttribute('button-click'))
			{	
			var myStr = obj.getAttribute('button-click');
        	var strArray = myStr.split(",");
        
	        	for(var i = 0; i < strArray.length; i++){
	        		$("#"+strArray[i]).click();
	       		 }
			}
			if(obj.getAttribute('button-click-by-class'))
			{	
			var myStr = obj.getAttribute('button-click-by-class');
        	var strArray = myStr.split(",");
        
	        	for(var i = 0; i < strArray.length; i++){
	        		$("."+strArray[i]).click();
	       		 }
			}
			if(obj.getAttribute('success-popup')){
				var response=data.msg; 
				if (response!=null) {
					if (data.status==1) {
						successMsg(response);	
					}
					else if(data.status==0){
						errorMsg(response);
					}
					 
				}
				
			
				// callSuccessPopup(response);
			}
			if(obj.getAttribute('select-triger')){
				
				 var myStr = obj.getAttribute('select-triger');
        	    var strArray = myStr.split(",");
        
	        	for(var i = 0; i < strArray.length; i++){
	        		$("#"+strArray[i]).trigger('change');
	       		 }
			}
			if(obj.getAttribute('check-all')){
				  // Add event listener for opening and closing details
				  $("#"+obj.getAttribute('check-all')).on('click', '#checkAll', function () {
					   alert('test');

				  });
			}			 
			if(obj.getAttribute('redirect-to'))
			{
				var redirect=obj.getAttribute('redirect-to');
				setTimeout(window.location.replace(redirect), 3000);
				;
			}
			if(obj.getAttribute('select2'))
			{  
				$(".select2").select2();
			}
			if(obj.getAttribute('multiselect'))
			{  
			   $('.pre-selected-options').multiSelect();
			}
			if(obj.getAttribute('daterangepicker'))
			{  
			     $('input[name="daterange"]').daterangepicker({
				     autoUpdateInput: true,
				       
				  });
			}
			if(obj.getAttribute('data-table-new'))
			{  
				$("#ajax_data_table").DataTable({
			      "responsive": true, "lengthChange": false, "autoWidth": false,
			      "buttons": ["excel", "colvis"]
			    }).buttons().container().appendTo('#ajax_data_table_wrapper .col-md-6:eq(0)');
			}
			if(obj.getAttribute('data-table-new-without-pagination'))
			{  
				$("#ajax_data_table").DataTable({
			      "responsive": true, "lengthChange": false, "autoWidth": false, 'paging':   false,
			      "buttons": ["excel", "colvis"]
			    }).buttons().container().appendTo('#ajax_data_table_wrapper .col-md-6:eq(0)');
			}
			if(obj.getAttribute('data-table-new-without-pagination_2'))
			{  
				$("#ajax_data_table_2").DataTable({
			      "responsive": true, "lengthChange": false, "autoWidth": false, 'paging':   false,
			      "buttons": ["excel", "colvis"]
			    }).buttons().container().appendTo('#ajax_data_table_2_wrapper .col-md-6:eq(0)');
			}

			if(obj.getAttribute('text-editor')!=""){
				$('.'+obj.getAttribute('text-editor')).summernote({
					toolbar: [
					     
					     ['font', ['bold', 'italic', 'underline']], 
					     ['fontsize', ['fontsize']],
					     ['color', ['color']],
					     ['para', ['ul', 'ol', 'paragraph']],
					     ['height', ['height']], 		      
					     ['insert', ['link', 'picture','video']],
					  
					   ],

					 placeholder: 'write here...',
					 height: 100
				}); 
							
			}
			if(obj.getAttribute('editor_question')!="")
			{  
				numberOfOption=obj.getAttribute('editor_question');	
			 CKEDITOR.config.toolbar_Full =
			     [
			     { name: 'document', items : [ 'Source'] },
			     { name: 'clipboard', items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
			     { name: 'editing', items : [ 'Find'] },
			     { name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
			     { name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight'] }
			     ];
			  	for(var i = 1; i <= numberOfOption; i++){
		  			CKEDITOR.replace('option_'+i, { height: 100 });
		  			CKEDITOR.plugins.addExternal('divarea', '../extraplugins/divarea/', 'plugin.js'); 
		  			CKEDITOR.replace('option_'+i, {
		  			     extraPlugins: 'base64image,divarea,ckeditor_wiris',
		  			     language: 'en'
		  			}); 
		 		}    
			 
			}
			else if(obj.getAttribute('data-table-excel'))
			{
			$("#"+obj.getAttribute('data-table-excel')).DataTable({
				"pageLength": 2,
                        "bFilter": false,
                        dom: 'Bfrtip',
                        buttons: [
                        'excel',
                        ]
			});
			}
			else if(obj.getAttribute('data-table-with-pagination'))
			{
			$("#"+obj.getAttribute('data-table-with-pagination')).DataTable({
				'paging':   true,
				dom: 'Bfrtip',
					buttons: [
						'copy', 'csv', 'excel', 'pdf', 'print'
					]
			});
			}
			else if(obj.getAttribute('data-table-without-pagination-disable-sorting'))
			{
			$("#"+obj.getAttribute('data-table-without-pagination-disable-sorting')).DataTable({
				'paging':   false,
				"aaSorting": [],
				dom: 'Bfrtip',
					buttons: [
						'copy', 'csv', 'excel', 'pdf', 'print'
					]
			});
			}
			 
				 
		}
    });

}
function callAjaxUrl(url,divId,callback){
	$('#'+divId).html('Please Wait......'); 
    $.get(url,{},
    function(data, status){
        if(status=="success")
		{
			$('#'+divId).html(data);	
			callJqueryDefault(divId);
			if(callback)
				callback();
		}
    }); 
}
 

function callDataTable(url,divId,tableId){
	$('#'+divId).html('Please Wait......'); 
    $.get(url,{},
    function(data, status){
        if(status=="success")
		{
			$('#'+divId).html(data);
			if ( ! $.fn.DataTable.isDataTable("#"+tableId) ) 
			{
			$("#"+tableId).DataTable({
				'iDisplayLength': 10,
			});
			}
			callJqueryDefault(divId);
		}
    });

}

function callSuccessPopup(msg){
	$('#success-popup').modal("show"); 
	$('.modal-body').html(msg); 
	$('#success-popup-content-id').html(msg); 
}

function callPopupLarge(obj,url){
	$('#ModalLargeId').modal("show"); 
	var divId='ModalLargeContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('multiselect-form')=="true"){
				$("#"+divId).find('.multiselect').selectpicker();
			}
			if(obj.getAttribute('duallistbox')=="true"){
				$("#"+divId).find('.duallistbox').bootstrapDualListbox();
			} 
			if(obj.getAttribute('button-click'))
			{ 
			var myStr = obj.getAttribute('button-click');
        	var strArray = myStr.split(",");
        
	        	for(var i = 0; i < strArray.length; i++){
	        		$("#"+strArray[i]).click();
	       		 }
			}

			if(obj.getAttribute('datatable-view')=="true")

				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
				{
					 
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
				});
				 
			}
			if(obj.getAttribute('select-triger')){
				
				 var myStr = obj.getAttribute('select-triger');
        	    var strArray = myStr.split(",");
        
	        	for(var i = 0; i < strArray.length; i++){
	        		$("#"+strArray[i]).trigger('change');
	       		 }
			}
			if(obj.getAttribute('select2'))
			{  
				$(".select2").select2();
			}

			if(obj.getAttribute('data-table-new'))
			{  
				$("#popup_data_table").DataTable({
			      "responsive": true, "lengthChange": false, "autoWidth": false,
			      "buttons": ["excel", "colvis"]
			    }).buttons().container().appendTo('#popup_data_table_wrapper .col-md-6:eq(0)');
			}
			
			if(obj.getAttribute('my_camera'))
			{  
			Webcam.set({
			   width: 250,
			   height: 250,
			   image_format: 'jpeg',
			   jpeg_quality: 90
			});
			// $("#my_camera").select2();
			Webcam.attach( '#my_camera' )
			}
			if(obj.getAttribute('crop-image'))
			{  
				var name = obj.getAttribute('crop-image')
				$uploadCrop=$('#'+name).croppie({
				    enableExif: true,
				    viewport: {
				        width: 200,
				        height: 250,         
				    },
				    boundary: {
				        width: 210,
				        height: 260
				    }
				});
			}	
				
			}
			if(obj.getAttribute('text-editor')!=""){
				$('.'+obj.getAttribute('text-editor')).summernote({
					toolbar: [
					     
					     ['style', ['style']],
					     ['font', ['bold', 'underline', 'clear']],
					     ['fontname', ['fontname']],
					     ['fontsize', ['fontsize']], 
					     ['height', ['height']], 
					     ['color', ['color']],
					     ['para', ['ul', 'ol', 'paragraph']],
					     ['table', ['table']],
					     ['insert', ['link', 'picture', 'video']],
					     ['view', ['fullscreen', 'codeview', 'help']] 

					  
					   ],

					 placeholder: 'write here...',
					 height: 100,
					  fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24','28','30','32', '36', '48' , '64', '82', '150']
				}); 
							
			}
    });

}

function callPopupsm(obj,url){
	$('#ModalSmId').modal("show"); 
	var divId='ModalSmContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('datatable-view')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
			});
			}
				
			}
    });

}

function callPopupMd(obj,url){
	$('#ModalMdId').modal("show"); 
	var divId='ModalMdContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('datatable-view')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
			});
			}
				
			}
    });

}


function callPopupLevel2(obj,url){
	$('#Modallevel2').modal("show"); 
	var divId='Modallevel2ContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('datatable-view')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
			});
			}
			if(obj.getAttribute('multi-select')=="true")
				{
					$("#"+divId).find('.multiselect').multiselect();
				}
				
			}
    });

}

function callPopupLevel3(obj,url){
	$('#Modallevel3').modal("show"); 
	var divId='Modallevel3ContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('datatable-view')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
			});
			}
				
			}
    });

}

function callPopupLevel4(obj,url){
	$('#Modallevel4').modal("show"); 
	var divId='Modallevel4ContentId';
	$('#'+divId).html('<div align="center"><img src="'+full_url_js+'/img/loader.gif" align="center" width="150px" style="padding-top:200px"></div>'); 
    $.get(url,{},
    function(response, status){
        if(status=="success"){
			$('#'+divId).html(response);
			callJqueryDefault(divId);
			if(obj.getAttribute('datatable-view-without-pagination')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'paging':   false,
				});
			}
			if(obj.getAttribute('datatable-view')=="true")
				if ( ! $.fn.DataTable.isDataTable('.datatablepopup') ) 
			{
				$("#"+divId).find('.datatablepopup').DataTable({
				'iDisplayLength': 10,
			});
			}
				
			}
    });

}


function changeUserType(obj,url){
	
    $.get(url,{
        id: obj.value
    },
    function(data, status){
        if(status=="success")
		{
			if(data.status==1)
				window.location.reload();
		}
    });

}
function changeJurisdiction(obj){ 
	if(obj=="National"){
	$('#state').prop('disabled', 'disabled');
	$('#city').prop('disabled', 'disabled'); 
	}
	if(obj=="State"){
		$('#state').prop('disabled', false); 
		$('#city').prop('disabled', 'disabled'); 
	}
	if(obj=="Municipal"){
		$('#state').prop('disabled', false); 
		$('#city').prop('disabled', false); 
	}

}

function changeAt(obj){  
	$('.pre-disable').attr("readonly","readonly");
	if (obj==null) {
		console.log('empty')
	}else{
		$.each(obj, function( index, value ) {  	 
    	$('#'+value).removeAttr("readonly"); 
    	$('#edit_remark').removeAttr("readonly"); 
	}); 
	}
	
}

function showHideDiv(val,divName)
{  
	if (val==0)
	 {
	 	$('#'+divName).hide(400); 
	 }
	if (val==1)
	 {
	 	$('#'+divName).show(400);
	}

}

function updateType(obj){  
	 
	 if (obj=='correction') {
	 	
	 	$('#notification_date').removeAttr('required');
	 	$('#notification_no').removeAttr('required');
	 	$('#notification').hide(500);
	 }else{	 		 	 
	 	$('#notification_date').attr("required", "true");
	 	$('#notification_no').attr("required", "true");
	 	$('#notification').show(500);
	 }
	
}

function successMsg(msg){  
toastr.success(msg); 
 	// $.toast({
 	// 	heading: msg, 
 	// 	position: 'top-right',
 	// 	loaderBg:'#f0c541',
 	// 	icon: 'success',
 	// 	hideAfter: 3500, 
 	// 	stack: 6
 	// }); 
}

function errorMsg(msg){  
  toastr.error(msg); 
 	// $.toast({
 	// 	heading: msg, 
 	// 	position: 'top-right',
 	// 	loaderBg:'#f0c541',
 	// 	icon: 'error',
 	// 	hideAfter: 3500, 
 	// 	stack: 6
 	// }); 
}
function callErrorPopup(msg){
	$('#error-popup').modal("show"); 
	$('#error-popup-content-id').html(msg); 
}
 


function callchildTable(url,divId,tableId){
	$('#'+divId).html('Please Wait......'); 
    $.get(url,{},
    function(data, status){
        if(status=="success")
		{
			$('#'+divId).html(data);
			if ( ! $.fn.DataTable.isDataTable("#"+tableId) ) 
			{
				var table = $("#"+tableId).DataTable({});

						  // Add event listener for opening and closing details
						  $("#"+tableId).on('click', 'td.details-control', function () {
							  var tr = $(this).closest('tr');
							  var row = table.row(tr);

							  if (row.child.isShown()) {
								  // This row is already open - close it
								  row.child.hide();
								  tr.removeClass('shown');
							  } else {
								  // Open this row
								  row.child(format(tr.data('child-value'))).show();
								  tr.addClass('shown');
							  }
						  });
						    
			}
			
			callJqueryDefault(divId);
		}
    }); 
} 

 function imageBind(obj) {
 	var reader = new FileReader();
 	     reader.onload = function (e) {
 	       $uploadCrop.croppie('bind', {
 	         url: e.target.result
 	       }).then(function(){
 	         console.log('jQuery bind complete');
 	       });
 	     }
 	     reader.readAsDataURL(obj.files[0]);
 } 
 function imageUpload(url,btnId) { 	
 	  $uploadCrop.croppie('result', {
 	    type: 'canvas',
 	    size: 'viewport'
 	  }).then(function (resp) {
 	    $.ajax({
 	      url: url,
 	      type: "POST",
 	      data: {"image":resp},
 	      success: function (data) {   
 	      successMsg("Image Save Successfully")     
 	       var myStr = btnId;
           	var strArray = myStr.split(",");
           
   	        	for(var i = 0; i < strArray.length; i++){
   	        		$("#"+strArray[i]).click();
   	       		 }
 	      }
 	    });
 	  }); 	 
 }
 $.ajaxSetup({
 headers: {
     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
 }
 });

 
 function fetchNotifications() { 
     var page = $('.endless-pagination').data('next-page'); 
     
     if(page !== null) {
     	$('#notification_wait').show();

         clearTimeout( $.data( this, "scrollCheck" ) );

         $.data( this, "scrollCheck", setTimeout(function() {

             var scroll_position_for_posts_load = $('#notification_list').height() + $('#notification_list').scrollTop() + 100;

             if(scroll_position_for_posts_load >= $('#notification_list').height()) {            	
                 $.get(page, function(data){
                     $('.notifications').append(data.notifications);
                     $('.endless-pagination').data('next-page', data.next_page);
                     $('#notification_wait').hide();
                 });
             }
         }, 500))

     }
 } 

function searchForm2(formObj){   
      event.preventDefault(); //prevent default action 
      statusbarStart()
      
      
     var pleaseWait=$("<div>please wait.......</div>");
     var uploadProgress=$("<div class='upload-progress'><div class='progress-bar'></div></div>");
     pleaseWait.insertAfter(formObj);
       var post_url = formObj.getAttribute('search-url'); //get form action url 
       var request_method = 'POST'; //get form GET/POST method
       var form_data = new FormData(formObj); //Encode form elements for submission
       $(formObj).find(".alert").remove();
       $.ajax({
           url : post_url,
           type: request_method,
           data : form_data,
           contentType: false,
           processData:false,
           async:true,
           xhr: function(){
           //upload Progress
           var xhr = $.ajaxSettings.xhr();
           if (xhr.upload) {
         
         pleaseWait.remove();
         //update progressbar
         uploadProgress.insertAfter(formObj);
         //console.log(5);
               xhr.upload.addEventListener('progress', function(event) {
                   var percent = 0;
                   var position = event.loaded || event.position;
                   var total = event.total;
                   if (event.lengthComputable) {
                       percent = Math.ceil(position / total * 100);
                   }
           //console.log(2);
           uploadProgress.css("width", + percent +"%");
           //console.log(3);
               }, true);
           }
           return xhr;
       }
       }).done(function(response){ //
     
     pleaseWait.remove();
     uploadProgress.remove();
     
     if(response.status==0){
       if(formObj.getAttribute('import')=="true"){
         errorMsg(response.msg)
         //$('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button> <strong>'+response.msg+'</strong>'+response.data+'</div>').insertAfter(formObj);
         formObj.reset();
       }
       else{
         if(formObj.getAttribute('error-id')){
           $('#'+formObj.getAttribute('error-id')).html(response.msg);
         }else{
           errorMsg(response.msg)
           
           //$(formObj).append($('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button><strong>Warning!</strong> '+response.msg+'</div>'));
         }
       }
     }else if(response){
       console.log(response);
       $('#adv_filter_content').html(response);
       if(formObj.getAttribute('data-table-without-pagination'))
           {
             $('#'+formObj.getAttribute('data-table-without-pagination')).DataTable({
             'paging':   false,
             dom: 'Bfrtip',
               buttons: [
                 'copy', 'csv', 'excel', 'pdf', 'print'
               ]
           });
           }
           
       
       else{
         $(formObj).append($('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button><strong>Success!</strong> '+response.msg+'</div>'));
       }

       if(formObj.getAttribute('content-refresh') && response.status==1)
       { 
         var myStr = formObj.getAttribute('content-refresh');
             var strArray = myStr.split(",");
           
             for(var i = 0; i < strArray.length; i++){
               $("#"+strArray[i]).load(location.href + ' #'+strArray[i]);
              }
       }

       if(formObj.getAttribute('no-reset')!="true"){
         formObj.reset();
         $(formObj).find('.multiselect').selectpicker("refresh");
         $(formObj).find('.summernote').summernote("reset");
       }
         
     }
       });
   }
 function fetchNotificationsAll() {
 	 
     var page = $('.endless-pagination2').data('next-page');
     var hasPage = $('.endless-pagination2').data('has-page');
     
  
     if(page !== null && hasPage == 1) {
 	$('#message_wait').show();
         clearTimeout( $.data( this, "scrollCheck" ) );

         $.data( this, "scrollCheck", setTimeout(function() {
             var scroll_position_for_posts_load = $(window).height() + $(window).scrollTop() + 100;

             if(scroll_position_for_posts_load >= $(document).height()) {
             	       
                 $.get(page, function(data){
                                        	 
                     $('.notificationItmes').append(data.notifications);
                     
                     $('.endless-pagination2').data('next-page', data.next_page);
                      $('#message_wait').hide();	 

                 });
             }
         }, 350))

     }
 }

