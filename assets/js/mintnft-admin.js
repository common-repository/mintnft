/* Custom Site Logo JS*/

jQuery( document ).ready(function ($) {

	 
$('#MintNFT_slideimage2_button').click(function(e){ 
		e.preventDefault();
		var MintNFT_uploader = wp.media({
			title: 'Select or upload a logo',
			button: { text: 'Select Logo' },
			multiple: false
		}).on('select', function(){
			var attachment = MintNFT_uploader.state().get('selection').first().toJSON();
			$('#MintNFT_slideimage2').val(attachment.url);
			$('#MintNFT_admin_preview').attr("src", attachment.url);
			$('#MintNFT_slideimage2_admin_hover_preview').attr("src",  attachment.url); /* Also update the preview image */
			$(".mc-preview-blocks").css("display","block");
			$(".mc-error-logo-url").css("display","none");
		}).open();
	});
 
$('#MintNFT_bgimage_button').click(function(e){ 
		e.preventDefault();
		var MintNFT_uploader = wp.media({
			title: 'Select or upload a logo',
			button: { text: 'Select Logo' },
			multiple: false
		}).on('select', function(){
			var attachment = MintNFT_uploader.state().get('selection').first().toJSON();
			$('#MintNFT_bgimage').val(attachment.url);
			$('#MintNFT_admin_preview').attr("src", attachment.url);
			$('#MintNFT_bgimage_admin_hover_preview').attr("src",  attachment.url); /* Also update the preview image */
			$(".mc-preview-blocks").css("display","block");
			$(".mc-error-logo-url").css("display","none");
		}).open();
	});

	
$('#MintNFT_logoimage_button').click(function(e){ 
	e.preventDefault();
	var MintNFT_uploader = wp.media({
		title: 'Select or upload a logo',
		button: { text: 'Select Logo' },
		multiple: false
	}).on('select', function(){
		var attachment = MintNFT_uploader.state().get('selection').first().toJSON();
		$('#MintNFT_logoimage').val(attachment.url);
		$('#MintNFT_admin_preview').attr("src", attachment.url);
		$('#MintNFT_logoimage_admin_hover_preview').attr("src",  attachment.url); /* Also update the preview image */
		$(".mc-preview-blocks").css("display","block");
		$(".mc-error-logo-url").css("display","none");
	}).open();
});
	

$('#MintNFT_bgimage2_button').click(function(e){ 
	e.preventDefault();
	var MintNFT_uploader = wp.media({
		title: 'Select or upload a logo',
		button: { text: 'Select Logo' },
		multiple: false
	}).on('select', function(){
		var attachment = MintNFT_uploader.state().get('selection').first().toJSON();
		$('#MintNFT_bgimage2').val(attachment.url);
		$('#MintNFT_admin_preview').attr("src", attachment.url);
		$('#MintNFT_bgimage2_admin_hover_preview').attr("src",  attachment.url); /* Also update the preview image */
		$(".mc-preview-blocks").css("display","block");
		$(".mc-error-logo-url").css("display","none");
	}).open();
});


	/* Check Image Field */
		$('.MintNFT_form').on('submit', function () {
			var MintNFT_logo_image = $('input#MintNFT_logo_image').attr('value');    // Getting Width Value

			if ((MintNFT_logo_image === '' || MintNFT_logo_image === null)) {
				alert("Please select the img.");
				return false;
			}
			
			
		});

		var serverType = $("#inputServerType").val();
	 
		if(serverType != 'pinata'){
			$(".form-table").find(".pinata_data").addClass("hide");
		}
 
		$("#inputServerType").change(function(){
		  
			if($("#inputServerType option:selected").text() == 'PINATA'){
				$(".form-table").find(".pinata_data").removeClass("hide");
			}else{
				$(".form-table").find(".pinata_data").addClass("hide");
			}
			 
		}); 
		//$(".form-table").find(".mainnetField").addClass("hide");
		//$(".form-table").find(".goerliField").addClass("hide");
		
	// $(".form-table").find(".mainnetField").addClass("hide");
	// $(".form-table").find(".goerliField").addClass("hide");
	// $("#inputNetworkType").change(function(){
	// 	if($("#inputNetworkType").val() == '0x5'){
	// 		$(".form-table").find(".goerliField").removeClass("hide");
	// 		$(".form-table").find(".mainnetField").addClass("hide");
	// 	}else if($("#inputNetworkType").val() == '0x1'){
	// 		$(".form-table").find(".mainnetField").removeClass("hide");
	// 		$(".form-table").find(".goerliField").addClass("hide");
	// 	}else if($("#inputNetworkType").val() == '0'){
	// 		$(".form-table").find(".mainnetField").addClass("hide");
	// 		$(".form-table").find(".goerliField").addClass("hide");
	// 	}
		
	// }); 

		//$("[data-toggle=tooltip]").tooltip('show');
		 

});