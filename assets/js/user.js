jQuery(document).ready(function(e){
  
	 $("#form_register").validate({
     rules:{
 		type:     "required", 
		fname:    "required",
		lname:    "required",	
		username: "required",
		address:  "required",
		email: {
				required: true,
				email: true
			},
		pass1: {
			required: true,
			minlength: 5
			}
    },
	messages: {
		fname:   "Please enter your firstname",
		lname:   "Please enter your lastname",
		username:"Please Enter Your Username",
		address :"Please Enter Your Address",
		email: "Please enter a valid email address",
		pass1: {
			required: "Please provide a password",
			minlength: "Your password must be at least 5 characters long"
		    },
	},				
	submitHandler: function(form) {
		//form.submit();
		user_insert();
    }   
  });

 
	
	/*-----------insert user as employee or client{PVR}----------*/
	function user_insert()
      { 
		var fname	        = jQuery('#fname').val();
		var lname	        = jQuery('#lname').val();
		var uname	        = jQuery('#uname').val();
		var email	        = jQuery('#email').val();
		var pass            = jQuery('#password').val();
		var postal_code	    = jQuery('#postal_code').val();
		var hour_rate	    = jQuery('#hour_rate').val();
		var radiousertype	= jQuery('input[name="radiousertype"]:checked').val();
		var address	        = jQuery('#address').val();
		var send_data ={
                       'fname': fname,'lname':lname,'uname':uname,'email':email,'pass':pass, 'postal_code':postal_code,'hour_rate':hour_rate,'radiousertype':radiousertype,'address':address,			
		               };
		$.ajax({
			type: "POST",
			url: 'includes/user-manage.php',
			dataType: "json",
			data:{type:'user_Insert',data:send_data},
			success: function(response){
				if(response.status == 0){
					new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					    });			
				}else if(response.status == 1){
					// console.log(response);
					window.location.href = 'userlist.php';
				}
			}
		});			
		return false;	
	}
	
	//edit user js validation
	$("#edit_user").validate({
     rules:{
		edt_fname:    "required",
		edt_lname:    "required",	
		edt_uname: 	  "required",
		edt_address:  "required",
		usert_type: "required",
		edt_email: {
				required: true,
				email: true
			},
		edt_password: {
			required: true,
			minlength: 5
			},
		hour_rate: {
			required: true,
			maxlength: 10
			}
    },
	messages: {
		edt_fname:   "Please enter your firstname",
		edt_lname:   "Please enter your lastname",
		edt_uname:"Please Enter Your Username",
		edt_address :"Please Enter Your Address",
		usert_type: "Please Choose User Type",
		edt_email: "Please enter a valid email address",
		edt_password: {
			required: "Please provide a password",
			minlength: "Your password must be at least 5 characters long"
		    },
		hour_rate: {
			required: "Please provide a hour rate",
			minlength: "Your hour rate must be with in 10 characters"
		    },
	},				
	submitHandler: function(form) {
		//form.submit();
		user_edit_popup();
    }   
  });

	
	/*-----------edit popup{PVR}----------*/
	jQuery('.popup_edit').click(function(){
		var id = jQuery(this).attr('data-id');
        var send_data = {'id':id}; 
        $.ajax({
            type:"POST",
            url:'includes/user-manage.php',
            dataType: "json",
            data:{type:'sel_edit_data',data:send_data},
            encode          : true,
            success: function(response){
				
				 console.log(response);	
				
				$('#edit_user')[0].reset();
                jQuery('#edt_type').val(response[0].user_type);
                jQuery('#hid_id').val(response[0].user_id); 
                jQuery('#edt_fname').val(response[0].first_name);
                jQuery('#edt_lname').val(response[0].last_name);
                jQuery('#edt_uname').val(response[0].user_name);
                jQuery('#edt_email').val(response[0].user_email);
                jQuery('#edt_password').val(response[0].user_password);
                jQuery('#edt_country').val(response[0].country);
                jQuery('#edt_city').val(response[0].city);
				jQuery('#hour_rate').val(response[0].hour_rate);
				
				if(response[0].user_type == '1')
					jQuery('#radiousertype_staff').prop('checked', true);
				
				if(response[0].user_type == '3')
					jQuery('#radiousertype_attorney').prop('checked', true);
				
				console.log(jQuery('#radiousertype_attorney'));
					
                jQuery('#edt_address').val(response[0].address);
                jQuery('#edt_postal_code').val(response[0].postal_code);
              }
           
        });    		 
	});
	
	
	/*-----------update data{PVR}----------*/
	jQuery('.popup_delete').click(function(){
	  var id = jQuery(this).attr('data-id');
        var send_data = {'id':id}; 
         console.log(send_data);
         $.confirm({
			title: 'Delete User Confirmation',
			content: 'Are you sure to delete this user?',
			buttons: {	
		    confirm: function () {
				 $.ajax({
				 url:'includes/user-manage.php',
				 method:'POST',
				 dataType:"json",
				 data:{type:'delete_user',data:send_data},
				 success:function(response)
                         {
							 console.log(response);
							 if(response.status == 0){
								new PNotify({
								title: "error",
								text: response.message,
								type: 'error',						
								});			 
							    }else if(response.status == 1){
								 jQuery('.users'+id).remove();
								 new PNotify({
								title: "success",
								text: response.message,
								type: 'success',						
								});	
						        }		 
					     }
				});	 
			 }, 
			  cancel: function () {
				  //close
			  },
		  } 
		});		
	});
	
	/*-----------update data{PVR}----------*/
	function user_edit_popup(){
		var id              =jQuery('#hid_id').val();
		var fname	        = jQuery('#edt_fname').val();
		var lname	        = jQuery('#edt_lname').val();
		var uname	        = jQuery('#edt_uname').val();
		var email	        = jQuery('#edt_email').val();
		var pass	        = jQuery('#edt_password').val();
		var postal_code	    = jQuery('#edt_postal_code').val();
		var hour_rate	    = jQuery('#hour_rate').val();
		var radiousertype	= jQuery('input[name="usert_type"]:checked').val();
		var address	        = jQuery('#edt_address').val();
		
		var send_data ={
							'id'		:id,
							'fname'		: fname,
							'lname'		:lname,
							'uname'		:uname,	
							'email'		:email,
							'pass'		:pass, 
							'postal_code':postal_code, 
							'hour_rate':hour_rate,
							'radiousertype':radiousertype,
							'address':address,			
		              };
					 
					  $.ajax({
						type: "POST",
						url: 'includes/user-manage.php',
						dataType: "json",
						data:{type:'user_edit',data:send_data},
						success: function(response){
							

				        if(response.status == 0){
					    new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					    });							
				          }else if(response.status == 1){
							$('#myModal').modal('hide'); 
						 new PNotify({
							title: "success",
							text: response.message,
							type: 'success',						
						});	
					//console.log(response);
					//window.location.href = 'userlist.php';
				}
			}
		});			
	 return false;	
	}
		
	// add clio user 
	jQuery("#add-clio-user").submit(function(e) {

		var send_data={
						'clio_user_id' : $('#clio_user_id').val(),
						'user_email' : $('#user_email').val(),
						'first_name' : $('#first_name').val(),				
						'last_name'	 : $('#last_name').val()				
						};
		//console.log(send_data)				
		//ajax call for loagin.
		$.ajax({
			type: "POST",
			url: 'includes/login-manage.php',
			dataType: "json",
			data:{type:'add_clio_user',data:send_data},
			encode          : true,
			success: function(response){
				
				// console.log(response);
				
				if(response.status == 1)
				{
					$('#myModal').modal('hide');
					// jQuery.notify({message: response.message},{type: 'success'});									 
					
					new PNotify({
						title: "success",
						text: response.message,
						type: 'success',						
					});				
					
					window.setTimeout(function(){ window.location.href = response.redirectURL ; }, 2000);				 
				}
				else 
				{					
					$('#myModal').modal('hide');
					// jQuery.notify({message: response.message},{type: 'danger'});							 	
					
					new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					});														
				}
			}
		});
		e.preventDefault();
	});
	
	// add clio client
	jQuery("#add-clio-client").submit(function(e) {
		var username = [];
		username = $("#add-clio-client").serializeArray();
		var send_data = {
						'id' : $('#clio-client').val(),
						'username'	 : username,
						'user_email' : $('#client_email').val(),
						'first_name' : $('#client_first_name').val(),				
						'last_name'	 : $('#client_last_name').val(),
						'name'		 : $('#client_name').val(),
						'user_ids'	 : $('#user_ids').val(),
                        'contact'  	 : $('#client_number').val(),											
					};
				//console.log(send_data);		
		//ajax call for loagin.
		$.ajax({
			type: "POST",
			url: 'includes/login-manage.php',
			dataType: "json",
			data:{type:'add_clio_client',data:send_data},
			encode          : true,
			success: function(response)
			{
				if(response.status == 1)
				{
					$('#ModalClient').modal('hide');					
					new PNotify({
						title: "success",
						text: response.message,
						type: 'success',						
					});					
					window.setTimeout(function(){ window.location.href = response.redirectURL ; }, 2000);				 
				}
				else 
				{					
					$('#ModalClient').modal('hide');				
					new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					});														
				}
			}
		});
		e.preventDefault();
	});
	
	jQuery(document).on("click","#client_next", function(e){
		$('#hidematter').hide();
		$('#client_next').hide();
		$('#client_matter').show();
		var user_id = [];
		var user_name = [];
		$.each($(".multiusers option:selected"), function(){            
            user_id.push($(this).val());
			user_name.push($(this).text());
        });
		var client_id = jQuery('#clio-client').val();
		var send_data = {'client_id':client_id,
						'user_id':user_id,
						'user_name':user_name};
		$.ajax({
			type: "POST",
			url: 'includes/login-manage.php',
		    dataType: "json",
			data:{type:'matter_user_client',data:send_data},
			beforeSend: function() {
              $("#loading-image").show();
           },
			success: function(response)
			{
					$("#loading-image").hide();
					if(response.status == 1 )
					{
						$('#client_matter').show();
						$('#addclientbtn').show();
						$("#client_matter").html(response.data);
					} 
			}
		});		
		e.preventDefault();
	});
	
	//selected client get matter {DAB} 5-10-17 {MAP}
	jQuery(document).on("change","#clientID", function(e){
		
		var client_id = jQuery(this).val();
		var send_data = {'client_id':client_id};
		$.ajax({
			type: "POST",
			url: 'includes/login-manage.php',
		    dataType: "json",
			data:{type:'clio_client_matter',data:send_data},
			success: function(response)
			{
					if(response.status == 1 )
					{
						$('#matters-data').show();
						$("#matters-data").html(response.data);
					}
			}
		});		
		e.preventDefault();
	});
	
	
	jQuery(document).on("click","#closeMatter", function(){
		$('#matters-data').hide();
		$('#add_pick_matter')[0].reset();
		$('#matters-data').html('');
	});
	
	//{MAP}
	jQuery(document).on("click",".checkMatter", function(){
		  var data = $(this).attr('data');
		  
		   if ($(this).is(":checked")) {
				$(document).find(".userlist_"+data).attr("disabled",false);
		   }else{
				$(document).find(".userlist_"+data).attr("disabled",true);
		   }
	});
	
	// selected client and matter insert {DAB} 5-10-17
	jQuery("#add_pick_matter").submit(function(e){
		var username = [];
		username = $("#add_pick_matter").serializeArray();
		
		send_data = {	
						'userlist':username,
						'client_id':$("#clientID").val()	
					};
	
		$.ajax({
			type: "POST",
			url: 'includes/login-manage.php',
			dataType: "json",
			data:{type:'add_pick_matter',data:send_data},
			encode          : true,
			beforeSend:function(){ 
			jQuery('.picmatter').prop('disabled', true);
			},
				
			success: function(response){
				if(response.status == 1)
				{
					$('#ModalClient').modal('hide');					
					new PNotify({
						title: "success",
						text: response.message,
						type: 'success',						
					});						
							
					window.setTimeout(function(){ window.location.href =  window.location.href ; }, 2000);
				}
				else 
				{					
					$('#ModalClient').modal('hide');				
					new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					});														
				}
			},
			
		});
		e.preventDefault();
	});
	
});

function changeStatus(pro_id, pro_status, pro_type, skip_status)
{
	var send_data = {						
						'pro_id' 		: pro_id,				
						'pro_status' 	: pro_status,				
						'pro_type' 		: pro_type,
						'skip_status'	: skip_status	
					};

	var pro_last_step = 0;	
	$.each(total_step, function (index, value) {		
		if(pro_type == index)
			pro_last_step = value;	  		
	});	
	
	// if((pro_type == 1 && pro_status == 7 && skip_status == 0) || (pro_type == 2 && pro_status == 8 && skip_status == 0))
	if(pro_last_step == pro_status && skip_status == 0)
	{		
		$.confirm({
			title: 'Warning',
			content: 'are you sure complete this project',
			buttons: {	
		    confirm: function () {				
				//ajax call for loagin.
				$.ajax({
					type: "POST",
					url: 'includes/user-manage.php',
					dataType: "json",
					data:{type:'change_status',data:send_data},
					encode : true,
					success: function(response)
					{
						if(response.status == 1)
						{				
							new PNotify({
								title: "success",
								text: response.message,
								type: 'success',						
							});							
							updateProjectGraph(response);											
							// window.setTimeout(function(){ window.location.href = response.redirectURL ; }, 1000);				 
						}
						else 
						{				
							new PNotify({
								title: "error",
								text: response.message,
								type: 'error',						
							});														
						}
					}
				});		
			}, 
			cancel: function () {
			},
		  } 
		});			
	}
	else
	{
		//ajax call for loagin.
		$.ajax({
			type: "POST",
			url: 'includes/user-manage.php',
			dataType: "json",
			data:{type:'change_status',data:send_data},
			encode : true,
			success: function(response)
			{
				if(response.status == 1)
				{				
					new PNotify({
						title: "success",
						text: response.message,
						type: 'success',						
					});					
					updateProjectGraph(response);
				}
				else 
				{				
					new PNotify({
						title: "error",
						text: response.message,
						type: 'error',						
					});														
				}
			}
		});	
	}
}

jQuery(document).on("click",".descbtn", function(){	
	data = {
			tab_id 			: $('.nav-tabs .active').text(), 
			order_by 		: jQuery(this).attr('data-id'),
			order_by_nm 	: jQuery(this).attr('data-name')
		}			
	updateProjectGraph(data);	
});
	
jQuery(document).on("click",".ascbtn", function(){	
	data = {
			tab_id 			: $('.nav-tabs .active').text(), 
			order_by 		: jQuery(this).attr('data-id'),
			order_by_nm 	: jQuery(this).attr('data-name')
			
		}	
	updateProjectGraph(data);		
});

function updateProjectGraph(data)
{
	console.log(data);		
	
	//ajax call for loagin.
	$.ajax({
		type: "POST",
		url: 'getAjaxProjectGraph.php',
		data:{data:data},		
		success: function(response){			
			$(".tab-content").html(response);
			
		}
	});

	if(data.tab_id == "Complete")
		$('.nav-tabs a[href="#section_complete"]').tab('show');
	
	if(data.tab_id == "Prospective")
		$('.nav-tabs a[href="#section_prospective"]').tab('show');
		
	if(data.tab_id == "Active")
		$('.nav-tabs a[href="#section_active"]').tab('show');

	if(data.tab_id == "All")
		$('.nav-tabs a[href="#section_all"]').tab('show');	
}

jQuery('.popup_clients_details').click(function(){
		//alert("hfh");
		var id = jQuery(this).attr('data-id');
		
		$(".panel").css("display", "none");

		
        var send_data = {'id':id}; 
		 
		$.ajax({
            type:"POST",
            url:'includes/user-manage.php',
            dataType: "json",
            data:{type:'view_data',data:send_data},
            success: function(response){
				if(response.status == 1){
                 console.log(response);
                 console.log(response);
				//Person
                jQuery('#edt_fname').val(response.data['name']);
                jQuery('#edt_lname').val(response.data['type']);
                jQuery('#edt_city').val(response.data['avatar_url']);
				//address  all
                jQuery('#addresses_id').val(response.data['addresses_0_id']);
                jQuery('#addresses_created_at').val(response.data['addresses_0_created_at']);
                jQuery('#addresses_updated_at').val(response.data['addresses_0_updated_at']);
                jQuery('#addresses_name').val(response.data['addresses_0_name']);
                jQuery('#addresses_street').val(response.data['addresses_0_street']);
                jQuery('#addresses_city').val(response.data['addresses_0_city']);
                jQuery('#addresses_postal_code').val(response.data['addresses_0_postal_code']);
                jQuery('#addresses_province').val(response.data['addresses_0_province']);
                jQuery('#addresses_country').val(response.data['addresses_0_country']);
				//address  all
				 jQuery('#phone_numbers').val(response.data['phone_numbers_0_id']);
                jQuery('#phone_numbers_created_at').val(response.data['phone_numbers_0_created_at']);
                jQuery('#phone_numbers_updated_at').val(response.data['phone_numbers_0_updated_at']);
                jQuery('#phone_numbers_name').val(response.data['phone_numbers_0_name']);
                jQuery('#phone_numbers_number').val(response.data['phone_numbers_number']);
                jQuery('#phone_numbers_default_number').val(response.data['phone_numbers_0_default_number']);
                jQuery('#phone_numbers_id').val(response.data['phone_numbers_1_id']);
               //Email all
			   
			   jQuery('#clio_connect_email').val(response.data['clio_connect_email']);
              
                }
				else if(response.status == 0)
				{
					
                jQuery('#edt_fname').val('');
                jQuery('#edt_lname').val('');
                jQuery('#edt_city').val('');
				//address  all
                jQuery('#addresses_id').val('');
                jQuery('#addresses_created_at').val('');
                jQuery('#addresses_updated_at').val('');
                jQuery('#addresses_name').val('');
                jQuery('#addresses_street').val('');
                jQuery('#addresses_city').val('');
                jQuery('#addresses_postal_code').val('');
                jQuery('#addresses_province').val('');
                jQuery('#addresses_country').val('');
				//address  all
				 jQuery('#phone_numbers').val('');
                jQuery('#phone_numbers_created_at').val('');
                jQuery('#phone_numbers_updated_at').val('');
                jQuery('#phone_numbers_name').val('');
                jQuery('#phone_numbers_number').val('');
                jQuery('#phone_numbers_default_number').val('');
                jQuery('#phone_numbers_id').val('');
               //Email all
			   
			   jQuery('#clio_connect_email').val('');
				}
              }
           
        });    		 
	});
	jQuery(document).on("click","#client-feed-show", function(){	

	$(this).addClass("active");
    $(".client-feed").toggle();
});