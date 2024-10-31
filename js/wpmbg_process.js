
  
jQuery.extend({
   postJSON: function( url, data, callback) {
	   
      	console.log( jQuery.post(url, data, callback, "html"));
   }
});

jQuery(document).ready(function() {
								
	if (jQuery("#wpmbg_orig_url").length > 0) {						
	var wpmbg_app_id = jQuery("#wpmbg_app_id").val();
	var wpmbg_url = jQuery("#wpmbg_url").attr("href");
	var wpmbg_url = jQuery("#wpmbg_orig_url").val();
	wpmbg_url = wpmbg_url.replace('%APP_ID%',wpmbg_app_id);
  	jQuery("#wpmbg_url").attr("href", wpmbg_url);





	}
	
	
jQuery('#save_app_id').click(function(e) {
	
	
	var wpmbg_app_id = jQuery("#wpmbg_app_id").val();
	var wpmbg_url = jQuery("#wpmbg_orig_url").val();
	wpmbg_url = wpmbg_url.replace('%APP_ID%',wpmbg_app_id);
  	jQuery("#wpmbg_url").attr("href", wpmbg_url);


if(wpmbg_app_id.length == 10) {		
            jQuery('.wpmbg_app_id_valid').html("<span style='color: red;'> Checking your APP ID</span>");
           
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'myAjax',
							wpmbg_app_id: wpmbg_app_id,
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery(".wpmbg_app_id_valid").html('');
							jQuery(".wpmbg_app_id_valid").append(data);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							//alert(errorThrown);
							 jQuery('.wpmbg_app_id_valid').html("<br /><span style='color: red;'>This client has not been register with MyBlogGuest, please visit step 1</span>");
						}
					});
						
							 
       

 
}
	 else if (wpmbg_app_id.length > 0) {
								  jQuery('.wpmbg_app_id_valid').html("<span style='color: red;'> Please enter a Valid APP ID</span>");
							 }
});
// end of app id checking
jQuery(".wpmbg_post_auth_buttons").hide();
  
jQuery('#wpmbg_app_secret_submit').click(function() {
    
	
    var wpmbg_app_secret= jQuery("#wpmbg_app_secret").val();
    if(wpmbg_app_secret.length == 20) {
		
            jQuery('.wpmbg_app_secret_valid').html("<span style='color: red;'> Authorizing, please wait...</span>");
           
					 jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'myBlogGuestAuth',
							wpmbg_app_secret: wpmbg_app_secret,
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery(".wpmbg_app_secret_valid").html('');
							jQuery(".wpmbg_app_secret_valid").append(data);
							
							if (/Authorized/i.test(data)) {
								
								jQuery(".wpmbg_pre_auth_buttons").hide();
								jQuery(".wpmbg_post_auth_buttons").toggle();
							}
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
	}
});

function findArticles(event) {
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();	
                var only_authorized     = jQuery("#only_authorized:checked").length;


var orig_table_row 		= jQuery("#table_row").html();
		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgFindArticlesAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by,
                                                        only_authorized: only_authorized,
						
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery("#the-list").html("");
							
						
						 	var search_results = jQuery.parseJSON(data);
							window.theJson=search_results;
						
							// this will enable or disable the next and prev buttons
							var wpmbg_total = search_results.total;
							var wpmbg_next_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);
							var wpmbg_prev_start =  parseInt(wpmbg_num_results,10) -  parseInt(wpmbg_start, 10);
					
					
					wpmbg_start =  parseInt(wpmbg_start, 10)

							// if we have some more max sure disabled for next button is off
							if (wpmbg_start < wpmbg_total)
							{
								jQuery("#wpmbg_search_next").removeAttr("disabled", "disabled");
							}
							

							// this will disable the next button
							if (( wpmbg_start < wpmbg_total ) && (wpmbg_next_start > wpmbg_total))
							{
								jQuery('#wpmbg_search_next').attr('disabled', 'disabled');
							} 
									
							// check the prev button
							if  (wpmbg_start == 0)
							{
								jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
							} else {
								jQuery("#wpmbg_search_prev").removeAttr("disabled", "disabled");	
							}
							
						
					  		jQuery.each(search_results.articles, function(i, item){
																		  
								var table_row = orig_table_row;														  

								if (item.offer_placed == "y") {
										table_row = table_row.replace(/%TITLE%/gi,"<strike>" + item.title + "</strike>");	
										table_row = table_row.replace(/<span class="edit">.+?<\/span>/gi,"");
								} 
								else {
									table_row = table_row.replace(/%TITLE%/gi,item.title);	
								}
								
								table_row = table_row.replace(/%DESCR%/gi,item.descr);	
								table_row = table_row.replace(/%ARTICLE_ID%/gi,item.id);	
								table_row = table_row.replace(/%AUTHOR%/gi,item.author);	
								table_row = table_row.replace(/%ID_USER_AUTHOR%/gi,item.id_user_author);		
								
								table_row = table_row.replace(/%AUTHOR_RATING%/gi,item.user_rating);
								table_row = table_row.replace(/%PIN%/gi,item.pin);
								table_row = table_row.replace(/%NUM_WORDS%/gi,item.num_words);	
								table_row = table_row.replace(/%CATEGORY%/gi,item.category);
								table_row = table_row.replace(/%DATE%/gi,item.publish_date);
								
									if(typeof(item.gravatar) != 'undefined' && item.gravatar != null && item.gravatar != '')
									{
									table_row = table_row.replace(/%GRAVATAR%/gi, '<img style="height: 20px;" src="'+item.gravatar+'"></img> <span style="color: green; font-size: 9px;">(Verified author)</span>');
									}
									else
									{
									table_row = table_row.replace(/%GRAVATAR%/gi, '');
									}
									
								jQuery("#the-list").append(table_row);
   
   	
      						});     
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							 jQuery('.loading').hide();
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
};


jQuery('#wpmbg_search_next, #wpmbg_search_ig_next, #wpmbg_search_igmt_next').click(function() {
	
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_search_prev, #wpmbg_search_ig_prev, #wpmbg_search_igmt_prev').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) -  parseInt(wpmbg_num_results,10);	
	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_search_submit, #wpmbg_search_ig_submit, #wpmbg_search_igmt_submit').click(function() {
//	jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
	jQuery("#wpmbg_start").val(0) ;		
});




jQuery('#wpmbg_search_next, #wpmbg_search_books_next, #wpmbg_search_booksmt_next').click(function() {
	
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_search_prev, #wpmbg_search_books_prev, #wpmbg_search_booksmt_prev').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) -  parseInt(wpmbg_num_results,10);	
	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_search_submit, #wpmbg_search_books_submit, #wpmbg_search_booksmt_submit').click(function() {
//	jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
	jQuery("#wpmbg_start").val(0) ;		
});


jQuery('#wpmbg_search_submit, #wpmbg_search_prev, #wpmbg_search_next').click(function(event) {

	findArticles(event);																					  
});

jQuery('#wpmbg_search_ig_submit, #wpmbg_search_ig_prev, #wpmbg_search_ig_next').click(function(event) {

	findIg(event);																					  
});

jQuery('#wpmbg_search_igmt_submit, #wpmbg_search_igmt_prev, #wpmbg_search_igmt_next').click(function(event) {

	igGivenToMe(event);																					  
});


jQuery('#wpmbg_search_books_submit, #wpmbg_search_books_prev, #wpmbg_search_books_next').click(function(event) {

	findBooks(event);																					  
});

jQuery('#wpmbg_search_booksmt_submit, #wpmbg_search_booksmt_prev, #wpmbg_search_booksmt_next').click(function(event) {

	booksGivenToMe(event);													  
});


jQuery('#wpmbg_agtm_next').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	
	wpmbg_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_agtm_prev').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) -  parseInt(wpmbg_num_results,10);	
	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_agtm_submit').click(function() {
	jQuery("#wpmbg_start").val(0) ;		
});




// get list of articles given to me
function articlesGivenToMe(event)
{
		// Need to kill old version of date picker before we can continue.
                
                    /* 
                    if(jQuery(".datepicker").length > 0)
                    {
                    jQuery(".datepicker").datepicker("destroy");
                    }
                    */

		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();
		var wpmbg_status 		= jQuery("#wpmbg_status").val();

	var orig_table_row 		= jQuery("#table_row").html();
		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgArticlesGivenToMeAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by,
							wpmbg_status: wpmbg_status
							
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery("#the-list").html("");
							
						 	var search_results = jQuery.parseJSON(data);
							window.theJson=search_results;
						
					  		jQuery.each(search_results.articles, function(i, item){
																		  
								var table_row = orig_table_row;														  
								table_row = table_row.replace(/%TITLE%/gi,item.title);	
								table_row = table_row.replace(/%DESCR%/gi,item.descr);	
								table_row = table_row.replace(/%ARTICLE_ID%/gi,item.id);	
								table_row = table_row.replace(/%AUTHOR%/gi,item.author);	
								table_row = table_row.replace(/%AUTHOR_RATING%/gi,item.author_rating);
								table_row = table_row.replace(/%ID_USER_AUTHOR%/gi,item.id_user_author);																	
								table_row = table_row.replace(/%PIN%/gi,item.pin);	
								table_row = table_row.replace(/%NUM_WORDS%/gi,item.num_words);	
								table_row = table_row.replace(/%CATEGORY%/gi,item.category);
								table_row = table_row.replace(/%DATE%/gi,item.publish_date);	
								
								var status = "<span style='color: red;'>Problem Somewhere</span>";
								if (item.status == 2) { status =  "<span style='color: red;'>Pending Publication</span>";
										table_row = table_row.replace(/%REJECT%/gi,"Reject Article");	
										table_row = table_row.replace(/%DPFORM%/gi,"none");	
										table_row = table_row.replace(/%STANDARDFORM%/gi,"block");
	
								 }
								 
								if (item.status == 3) { 
									status =  "<span style='color: green;'>Published</span>"; 
									table_row = table_row.replace(/%REJECT%/gi,"");	
									
									table_row = table_row.replace(/%DPFORM%/gi,"none");	
									table_row = table_row.replace(/%STANDARDFORM%/gi,"none");	
								}
								
								if (item.status == 4) { 
									status =  "<span style='color: orange;'>Sent through MBG Direct</span>"; 
									table_row = table_row.replace(/%STANDARDFORM%/gi,"none");
									table_row = table_row.replace(/%DPFORM%/gi,"block");
								}
								
								table_row = table_row.replace(/%STATUS%/gi,status);	

									if(typeof(item.gravatar) != 'undefined' && item.gravatar != null && item.gravatar != '')
									{
									table_row = table_row.replace(/%GRAVATAR%/gi, '<img style="height: 20px;" src="'+item.gravatar+'"></img> <span style="color: green; font-size: 9px;">(Verified author)</span>');
									}
									else
									{
									table_row = table_row.replace(/%GRAVATAR%/gi, '');
									}
								
								// check status as the API doesn't do this 
								if (wpmbg_status != "") {
									if (wpmbg_status == item.status) {
										jQuery("#the-list").append(table_row);
									}
								} else {
									jQuery("#the-list").append(table_row);				
								}
								
      						});
							jQuery(document).ready(function() {
								  // line below is for debugging
								 // jQuery(".datepicker").each(function(){alert(this.id);}); 

                                                                if(jQuery(".datepicker").length > 0)
                                                                {
                                                                /*
							 	jQuery(".datepicker").datepicker();
								jQuery(".datepicker").datepicker( "option", "dateFormat", "yy-mm-dd");
                                                                */
                                                                }
							
							 });
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							 jQuery('.loading').hide();
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
	
 
	  

	

}


jQuery('#wpmbg_agtm_submit, #wpmbg_agtm_prev, #wpmbg_agtm_next').click(function(event) {
 	 articlesGivenToMe();
});




jQuery('#wpmbg_article_offers_next').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	
	wpmbg_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_article_offers_prev').click(function() {
	var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
	var wpmbg_start 		= jQuery("#wpmbg_start").val();	
	wpmbg_start =  parseInt(wpmbg_start, 10) -  parseInt(wpmbg_num_results,10);	
	
	jQuery("#wpmbg_start").val(wpmbg_start) ;	
	
});

jQuery('#wpmbg_article_offers_submit').click(function() {
	jQuery("#wpmbg_start").val(0) ;		
});


// locate offer that i've made
function offersMade(event)
{
		
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var orig_table_row 		= jQuery("#table_row").html();
		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgOffersMadeAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start
						},
						success: function(data, textStatus, XMLHttpRequest){
							jQuery("#the-list").html("");
							
						 	var search_results = jQuery.parseJSON(data);
							if (search_results == 0) { 	
							
							// Results were blank
								jQuery("#the-list").append("<tr><td  style='color: red;'>No Results Found</td></tr>");
							
							
							} else {
						
							
					  		jQuery.each(search_results.offers, function(i, item){
																		  
								var table_row = orig_table_row;														  
								table_row = table_row.replace(/%TITLE%/gi,item.title);	
								table_row = table_row.replace(/%DESCR%/gi,item.descr);	
								table_row = table_row.replace(/%ARTICLE_ID%/gi,item.id_article);
							
								table_row = table_row.replace(/%AUTHOR%/gi,item.author);
								table_row = table_row.replace(/%ID_USER_AUTHOR%/gi,item.id_user_author);																
								table_row = table_row.replace(/%NUM_WORDS%/gi,item.num_words);	
								table_row = table_row.replace(/%TIME_FRAME%/gi,secondsToTime(item.time_frame));	
								table_row = table_row.replace(/%OFFER%/gi,item.offer);
								table_row = table_row.replace(/%DATE%/gi,item.create_date);	
								table_row = table_row.replace(/%REJECT_REASON%/gi,item.reject_reason);	
								var status = "<span style='color: grey;'>Ignored</span>";
								if (item.status == 1) { status =  "<span style='color: green;'>Approved</span>"; }
								if (item.status == 0) { status =  "<span style='color: orange;'>New</span>"; }
								if (item.status == 2) { status =  "<span style='color: red;'>Rejected</span>"; }
						
						
								table_row = table_row.replace(/%STATUS%/gi,status);	
								
								
								jQuery("#the-list").append(table_row);
         						});     
							
							} // end of something found 
							
							// Clear the searching status text
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							 jQuery('.loading').hide();
							 
							
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
	
}


jQuery('#wpmbg_article_offers_submit, #wpmbg_article_offers_prev, #wpmbg_article_offers_next').click(function(event) {
 	 offersMade();
});





// locate offer that i've made



// Do some stuff if the document is ready
// Check to see what page we are on then execute the appropriate function that matches that page.

jQuery(document).ready(function() {
  	if (jQuery('#wpmbg_agtm_submit').length != 0) {
	
		articlesGivenToMe();
	}
  	if (jQuery('#wpmbg_search_ig_submit').length != 0) {
	
		findIg();
	}
  	if (jQuery('#wpmbg_search_igmt_submit').length != 0) {
	
		igGivenToMe();
	}
  	if (jQuery('#wpmbg_search_books_submit').length != 0) {
	
		findBooks();
	}
  	if (jQuery('#wpmbg_search_booksmt_submit').length != 0) {
	
		booksGivenToMe();
	}
  	if (jQuery('#wpmbg_search_submit').length != 0) {
	
		findArticles();
	}
	if (jQuery('#wpmbg_article_offers_submit').length != 0) {
		
		offersMade();
	}	
});
 

        /*
         *  подменяем стандартные ссылки удаления постов на наши, которые будут сначала проверять
         *  не нужно ли для этой статьи требовать reject reason, а потом уже отправлять статью в треш
         */
        
jQuery('a.submitdelete').each(function(){
    
    var a = jQuery(this);
    var url = new String(a.attr('href'));
    a.attr('href', 'javascript: void(0);');
    
    a.live('click', function(){
    //      alert(url);
          
          var ret = mbgIsMBGArticle(url);
          
                if(ret){
                mbgInputDlg('Enter Reason', 
                                function(){
                        	jQuery.ajax({
                                type: 'POST',
                                url: mbgAjax.ajaxurl,
                                async : true,
                                data: {
                                    action: 'mbgSaveRejectReason',
                                    url: url,
                                    id_article:  ret['id_article'],
                                    id_ig_descr: ret['id_ig_descr'],
                                    id_book_descr: ret['id_book_descr'],
                                    reason: jQuery('#mbg-dlg-input-txt').val(),
                                    },
                                success: function(data, textStatus, XMLHttpRequest){
                                    //alert(data);
                                    },
                                error: function(MLHttpRequest, textStatus, errorThrown){
                                    mbgShowMessage(errorThrown);
                                    }
                                });         // ajax 

                                setTimeout(function(){                                    
                                            document.location = url;
                                            }
                                           , 300);
                                
                                },
                            '');        // mbgInputDlg
                }
                else{
                document.location = url;
                }
          });
    });
});

    /*
    * 
     * определяет по url был ли данный пост передан с MBG, если да - возвращает ID статьи в MBG и выполняет callback
     * иначе  - возвращает false
     * 
     * @param {string} url
     * @param {function} callback
     * @returns {bool}
     */
     
function mbgIsMBGArticle(url, callback)
{
var ret = false;
var id_article = 0;
var id_ig_descr = 0;
var id_book_descr = 0;

	jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
                async : false,
		data: {
			action: 'mbgIsMBGArticle',
			url: url,
		},
		success: function(data, textStatus, XMLHttpRequest){
			
                    var res = eval('('+data+')');
                    
                        if(typeof(res['id_article']) != 'undefined' && res['id_article'] != 0)
                        {
                        id_article = res['id_article'];
                        }

                        if(typeof(res['id_ig_descr']) != 'undefined' && res['id_ig_descr'] != 0)
                        {                            
                        id_ig_descr = res['id_ig_descr'];
                        }

                        if(typeof(res['id_book_descr']) != 'undefined' && res['id_book_descr'] != 0)
                        {                            
                        id_book_descr = res['id_book_descr'];
                        }

                        if(id_article > 0 || id_ig_descr > 0 || id_book_descr > 0)
                        {
                        ret = {id_article : id_article, id_ig_descr : id_ig_descr, id_book_descr : id_book_descr};
                        
                            if(typeof(callback) == 'function'){
                            setTimeout(callback, 1);
                            }
                        }
				
		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			mbgShowMessage(errorThrown);
		}
	});         // ajax 
        
 return ret;
}

function publishArticle(wpmbg_id_article)
{		

            jQuery('#wpmbg_update_status'+wpmbg_id_article).html("<span style='color: red;'>Publishing</span>");				
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgPublishArticleAjax',
							wpmbg_id_article: wpmbg_id_article
						},
						success: function(data, textStatus, XMLHttpRequest){
							
						 	//var published = jQuery.parseJSON(data);
							jQuery('#wpmbg_update_status'+wpmbg_id_article).html("");
							mbgShowMessage(data);
						
							jQuery('.loading').hide();
							
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							mbgShowMessage(errorThrown);
						}
					});
						
	
}


function notifyMBG(wpmbg_id_article)
{		

            jQuery('#wpmbg_update_status'+wpmbg_id_article).html("<span style='color: red;'>Notifying</span>");				
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgNotifyMBGAjax',
							wpmbg_id_article: wpmbg_id_article
						},
						success: function(data, textStatus, XMLHttpRequest){
							
						 	//var published = jQuery.parseJSON(data);
							jQuery('#wpmbg_update_status'+wpmbg_id_article).html("");
							mbgShowMessage(data);
						
							jQuery('.loading').hide();
							 
							
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							mbgShowMessage(errorThrown);
						}
					});
						
	
}


function mbgRejectArticle(wpmbg_id_article)
{
mbgInputDlg('Refuse Article', 
	
	function(){

	var wpmbg_refuse_reason	= jQuery('#mbg-dlg-input-txt').val();	
			
        jQuery('#wpmbg_update_status'+wpmbg_id_article).html("<span style='color: red;'>Notifying</span>");				
        jQuery('.loading').show();
		 jQuery.ajax({
			type: 'POST',
			url: mbgAjax.ajaxurl,
			data: {
				action: 'mbgRejectAjax',
				wpmbg_id_article: wpmbg_id_article,
				wpmbg_refuse_reason: wpmbg_refuse_reason
			},
			success: function(data, textStatus, XMLHttpRequest){
				
			 	//var published = jQuery.parseJSON(data);
			 	mbgShowMessage(data);
			 	
				jQuery('#wpmbg_update_status'+wpmbg_id_article).html("");
			
				jQuery('.loading').hide();
				
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
			}
		});
	});		
}



function sendRejectDP(wpmbg_id_article)
{
mbgInputDlg('Decline Direct Post Article', 

	function(){	

		var wpmbg_dp_refuse_reason	= jQuery('#mbg-dlg-input-txt').val();
		var wpmbg_dp_article_title	= jQuery("#article_title"+wpmbg_id_article).val();

		if (wpmbg_dp_refuse_reason == '')
		{
			alert("Please Give A Reason For Rejecting This Article");
			return;
		}
		
	jQuery('#wpmbg_update_status'+wpmbg_id_article).html("<span style='color: red;'>Notifying</span>");				
	jQuery('.loading').show();
				 jQuery.ajax({
					type: 'POST',
					url: mbgAjax.ajaxurl,
					data: {
						action: 'mbgDPRejectAjax',
						wpmbg_id_article: wpmbg_id_article,
						wpmbg_dp_refuse_reason: wpmbg_dp_refuse_reason,
						wpmbg_dp_article_title: wpmbg_dp_article_title
					},
					success: function(data, textStatus, XMLHttpRequest){
						
					 	//var published = jQuery.parseJSON(data);
						jQuery('#wpmbg_update_status'+wpmbg_id_article).html("");
						mbgShowMessage(data);
					
						jQuery('.loading').hide();
						jQuery("#DPForm"+wpmbg_id_article).hide();
						
					},
					error: function(MLHttpRequest, textStatus, errorThrown){
						mbgShowMessage(errorThrown);
					}
				});
	});			
}


function sendApproveDP(wpmbg_id_article)
{		
			var wpmbg_dp_pub_sch	= jQuery('input[name="pub_sch'+wpmbg_id_article+'"]:checked').val()
			var wpmbg_dp_sch_date	= jQuery("#sch_date"+wpmbg_id_article).val();
			var wpmbg_dp_article_title	= jQuery("#article_title"+wpmbg_id_article).val();

			
			if(typeof wpmbg_dp_pub_sch == 'undefined')
			{
				alert("Please Select Published or Scheduled");
				return;
			}

			if ((wpmbg_dp_pub_sch == 'scheduled') && (wpmbg_dp_sch_date == ''))
			{
				alert("Please Give A Scheduled Date For Publishing");
				return;
			}


            jQuery('#wpmbg_update_status'+wpmbg_id_article).html("<span style='color: red;'>Notifying</span>");				
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgDPApproveAjax',
							wpmbg_id_article: wpmbg_id_article,
							wpmbg_dp_pub_sch: wpmbg_dp_pub_sch,
							wpmbg_dp_sch_date: wpmbg_dp_sch_date,
							wpmbg_dp_article_title: wpmbg_dp_article_title
						},
						success: function(data, textStatus, XMLHttpRequest){
							
						 	//var published = jQuery.parseJSON(data);
							jQuery('#wpmbg_update_status'+wpmbg_id_article).html("");
							jQuery("#wpmbg_update_status"+wpmbg_id_article).append("<span='color: red;'><b>"+data+"</b></span>");
						
							jQuery('.loading').hide();
							jQuery("#DPForm"+wpmbg_id_article).hide();
							
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
}


function secondsToTime(seconds)
{
var numdays = Math.floor(seconds / 86400);
var numhours = Math.floor((seconds % 86400) / 3600);
var numminutes = Math.floor(((seconds % 86400) % 3600) / 60);
var numseconds = ((seconds % 86400) % 3600) % 60;
if (numdays > 1) { 
	return numdays + " days";
} else {
return numdays + " day";
}
}

function rateAuthor( wpmbg_id_user_rating, wpmbg_rating, wpmbg_current_rating)
{
	

var new_rating = Math.floor(wpmbg_rating + wpmbg_current_rating);
	// Rate authors with Ajax
	
mbgInputDlg('Rate Author', 

	function(){	
		jQuery.ajax({
			type: 'POST',
			url: mbgAjax.ajaxurl,
			data: {
				action: 'mbgRateAuthorAjax',
				wpmbg_id_user_rating: wpmbg_id_user_rating,
				wpmbg_rating: wpmbg_rating,
				wpmbg_rating_comment: jQuery('#mbg-dlg-input-txt').val()
			},
			success: function(data, textStatus, XMLHttpRequest){
			
				if (/Submitted/i.test(data)) {  data += "\nNew Rating is " + new_rating; }
			
				mbgShowMessage(data);						
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
			}
		});
	});
}

	// reject request idea
function mbgRejectIdea(id_idea)
{

	var reason = jQuery("#blogger-comment-"+id_idea).val();

		 jQuery.ajax({
			type: 'POST',
			url: mbgAjax.ajaxurl,
				data: {
					action: 'mbgRejectIdeaAjax',
					id_idea: id_idea,
					reason: reason,
					},

					success: function(data, textStatus, XMLHttpRequest){
					var param = eval('('+data+')');
					
						if(typeof(param['err']) != 'undefined' && param['err'] != '')
						{
						alert(param['err']);
						}
						else
						{
							if(param['status'] == 4)
							{
							jQuery('#idea-'+param['id_idea']).remove();
							}
						}
					},
					
					error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
					}
				});
}

	// approve request idea
function mbgApproveIdea(id_idea)
{

    var reason = '';
        
            if(jQuery("#blogger-comment-"+id_idea).length > 0)
            {
            reason = jQuery("#blogger-comment-"+id_idea).val();
            }

    mbgInputDlg('Ideas approval', function(){
        
                         reason = jQuery('#mbg-dlg-input-txt').val();
                         
                	 jQuery.ajax({
                        	type: 'POST',
                                url: mbgAjax.ajaxurl,
				data: {
					action: 'mbgApproveIdeaAjax',
					id_idea: id_idea,
					reason: reason,
					},

					success: function(data, textStatus, XMLHttpRequest){
					var param = eval('('+data+')');
					
						if(typeof(param['err']) != 'undefined' && param['err'] != '')
						{
						alert(param['err']);
						}
						else
						{
							if(param['status'] == 1 && typeof(param['code']) != 'undefined')
							{
                                                            if(jQuery('#idea-'+param['id_idea']).length > 0)
                                                            {
                                                            jQuery('#idea-'+param['id_idea']).replaceWith(param['code']);
                                                            }
                                                            
                                                        mbgShowMessage('The idea was successfully approved');
							}
						}
					},
					
					error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
					}
				});
                        //reason = jQuery;
                        }, reason);


}


function mbgArticlePreview(id_article)
{
var data = 'action=mbgArticlePreviewAjax&id_article='+id_article;

     jQuery.ajax({
	type: 'POST',
	url:  mbgAjax.ajaxurl,
	data: data,
	success: function(data, textStatus, XMLHttpRequest){
                    var result = jQuery.parseJSON(data);

                          if(typeof(result['err']) == 'string' && result['err'] != '')
                          {
                          mbgShowMessage(result['err']);
                          }
                          else if(typeof(result['code']) == 'string' && result['code'] != '')
                          {
                              // success
                              
                          mbgDlg('Preview Article', null, result['code'], 850, 35);
                          }
                          
                          if(typeof(result['msg']) == 'string' && result['msg'] != '')
                          {
                          mbgShowMessage(result['msg']);
                          }

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
				}
		});    
}

        // Article Offer
        
function mbgArticleOfferDlg(id_article)
{
var data = 'id_article='+id_article;
mbgDlgTpl('article_offer', function(){
        mbgSendArticleOffer(id_article);
        }, 
        data,
        'Send Article Offer');
}

function mbgSendArticleOffer(id_article)
{
var offer = jQuery('#offer-text').val();
var days  = jQuery('#offer-days').val();

var data = 'action=mbgSendArticleOfferAjax&id_article='+id_article+'&offer='+encodeURIComponent(offer)+'&days='+days;

     jQuery.ajax({
	type: 'POST',
	url:  mbgAjax.ajaxurl,
	data: data,
	success: function(data, textStatus, XMLHttpRequest){
                    var result = jQuery.parseJSON(data);

                          if(typeof(result['err']) == 'string' && result['err'] != '')
                          {
                          mbgShowMessage(result['err']);
                          }
                          else
                          {
                              // success
                          }
                          
                          if(typeof(result['msg']) == 'string' && result['msg'] != '')
                          {
                          mbgShowMessage(result['msg']);
                          }

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
				}
		});
}

	// Infographics

function mbgIgOfferDlg(id_descr)
{
var data = 'id_descr='+id_descr;
mbgDlgTpl('article_offer', function(){
        mbgSendIgOffer(id_descr);
        }, 
        data,
        'Send IG Offer');
}

function mbgSendIgOffer(id_descr)
{
var offer = jQuery('#offer-text').val();
var days  = jQuery('#offer-days').val();

var data = 'action=mbgSendIgOfferAjax&id_descr='+id_descr+'&offer='+encodeURIComponent(offer)+'&days='+days;

     jQuery.ajax({
	type: 'POST',
	url:  mbgAjax.ajaxurl,
	data: data,
	success: function(data, textStatus, XMLHttpRequest){
                    var result = jQuery.parseJSON(data);
					
                          if(typeof(result['err']) == 'string' && result['err'] != '')
                          {
                          mbgShowMessage(result['err']);
                          }
                          else
                          {
                              // success
                          }
                          
                          if(typeof(result['msg']) == 'string' && result['msg'] != '')
                          {
                          mbgShowMessage(result['msg']);
                          }

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
				}
		});
}

function findIg(event) {
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();	

		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgFindIgAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by
						
						},
						success: function(data, textStatus, XMLHttpRequest){
							//alert(data);

						var search_results = jQuery.parseJSON(data);
						window.theJson=search_results;
						
							// this will enable or disable the next and prev buttons
						var wpmbg_total = search_results.total;
						var wpmbg_next_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);
						var wpmbg_prev_start =  parseInt(wpmbg_num_results,10) -  parseInt(wpmbg_start, 10);
					
					
						wpmbg_start =  parseInt(wpmbg_start, 10)

							// if we have some more max sure disabled for next button is off
							if (wpmbg_start < wpmbg_total)
							{
								jQuery("#wpmbg_search_next").removeAttr("disabled", "disabled");
							}
							

							// this will disable the next button
							if (( wpmbg_start < wpmbg_total ) && (wpmbg_next_start > wpmbg_total))
							{
								jQuery('#wpmbg_search_next').attr('disabled', 'disabled');
							} 
									
							// check the prev button
							if  (wpmbg_start == 0)
							{
								jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
							} else {
								jQuery("#wpmbg_search_prev").removeAttr("disabled", "disabled");	
							}
							
						
     
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							jQuery('.loading').hide();
							
							jQuery('#search_results').html(search_results.code);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
};


function igGivenToMe(event) {
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();
		var wpmbg_status 		= jQuery("#wpmbg_status").val();

		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgIgGivenToMeAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by,
							wpmbg_status: wpmbg_status
						
						},
						success: function(data, textStatus, XMLHttpRequest){
							//alert(data);

						var search_results = jQuery.parseJSON(data);
						window.theJson=search_results;
						
							// this will enable or disable the next and prev buttons
						var wpmbg_total = search_results.total;
						var wpmbg_next_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);
						var wpmbg_prev_start =  parseInt(wpmbg_num_results,10) -  parseInt(wpmbg_start, 10);
					
					
						wpmbg_start =  parseInt(wpmbg_start, 10)

							// if we have some more max sure disabled for next button is off
							if (wpmbg_start < wpmbg_total)
							{
								jQuery("#wpmbg_search_next").removeAttr("disabled", "disabled");
							}
							

							// this will disable the next button
							if (( wpmbg_start < wpmbg_total ) && (wpmbg_next_start > wpmbg_total))
							{
								jQuery('#wpmbg_search_next').attr('disabled', 'disabled');
							} 
									
							// check the prev button
							if  (wpmbg_start == 0)
							{
								jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
							} else {
								jQuery("#wpmbg_search_prev").removeAttr("disabled", "disabled");	
							}
							
						
     
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							jQuery('.loading').hide();
							
							jQuery('#search_results').html(search_results.code);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							mbgShowMessage(errorThrown);
						}
					});
						
};

function mbgImportIgToDraft(id_descr)
{
	 jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
		data: {
			action: 'mbgImportIgToDraftAjax',
			wpmbg_id_descr: id_descr,		
		},
		success: function(data, textStatus, XMLHttpRequest){
			mbgShowMessage(data);

		var results = jQuery.parseJSON(data);

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			mbgShowMessage(errorThrown);
		}
	});

};

function mbgIgNotifyMBGAjax(id_descr)
{
	 jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
		data: {
			action: 'mbgIgNotifyMBGAjax',
			wpmbg_id_descr: id_descr,		
		},
		success: function(data, textStatus, XMLHttpRequest){
			mbgShowMessage(data);

		var results = jQuery.parseJSON(data);

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			mbgShowMessage(errorThrown);
		}
	});

};

function mbgIgRefuseAjax(id_descr)
{
mbgInputDlg('Refuse Infographic', 
	
	function(){
		jQuery.ajax({
			type: 'POST',
			url: mbgAjax.ajaxurl,
			data: {
				action: 'mbgIgRejectAjax',
				wpmbg_id_descr: id_descr,
				wpmbg_refuse_reason: jQuery('#mbg-dlg-input-txt').val()
			},
			success: function(data, textStatus, XMLHttpRequest){
				mbgShowMessage(data);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
			}
		});
	
	});
};
        //////////End of Infographics///////////////////////////////////////////
        
        


	///eBooks///////////////////////////////////////////////////////////////

function mbgBookOfferDlg(id_descr)
{
var data = 'id_descr='+id_descr;
mbgDlgTpl('article_offer', function(){
        mbgSendBookOffer(id_descr);
        }, 
        data,
        'Send eBook Offer');
}

function mbgSendBookOffer(id_descr)
{
var offer = jQuery('#offer-text').val();
var days  = jQuery('#offer-days').val();

var data = 'action=mbgSendBookOfferAjax&id_descr='+id_descr+'&offer='+encodeURIComponent(offer)+'&days='+days;

     jQuery.ajax({
	type: 'POST',
	url:  mbgAjax.ajaxurl,
	data: data,
	success: function(data, textStatus, XMLHttpRequest){
                    var result = jQuery.parseJSON(data);
					
                          if(typeof(result['err']) == 'string' && result['err'] != '')
                          {
                          mbgShowMessage(result['err']);
                          }
                          else
                          {
                              // success
                          }
                          
                          if(typeof(result['msg']) == 'string' && result['msg'] != '')
                          {
                          mbgShowMessage(result['msg']);
                          }

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
				}
		});
}

function findBooks(event) {
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();	

		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgFindBooksAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by
						
						},
						success: function(data, textStatus, XMLHttpRequest){
							//alert(data);

						var search_results = jQuery.parseJSON(data);
						window.theJson=search_results;
						
							// this will enable or disable the next and prev buttons
						var wpmbg_total = search_results.total;
						var wpmbg_next_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);
						var wpmbg_prev_start =  parseInt(wpmbg_num_results,10) -  parseInt(wpmbg_start, 10);
					
					
						wpmbg_start =  parseInt(wpmbg_start, 10)

							// if we have some more max sure disabled for next button is off
							if (wpmbg_start < wpmbg_total)
							{
							jQuery("#wpmbg_search_next").removeAttr("disabled", "disabled");
							}
							

							// this will disable the next button
							if (( wpmbg_start < wpmbg_total ) && (wpmbg_next_start > wpmbg_total))
							{
							jQuery('#wpmbg_search_next').attr('disabled', 'disabled');
							} 
									
							// check the prev button
							if  (wpmbg_start == 0)
							{
							jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
							} else {
							jQuery("#wpmbg_search_prev").removeAttr("disabled", "disabled");
							}
							
						
     
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							jQuery('.loading').hide();
							
							jQuery('#search_results').html(search_results.code);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							alert(errorThrown);
						}
					});
						
};


function booksGivenToMe(event) {
		var wpmbg_search_string = jQuery("#wpmbg_search_string").val();
		var wpmbg_num_results 	= jQuery("#wpmbg_num_results").val();
		var wpmbg_category 		= jQuery("#wpmbg_category").val();
		var wpmbg_start 		= jQuery("#wpmbg_start").val();	
		var wpmbg_sort_order 	= jQuery("#wpmbg_sort_order").val();	
		var wpmbg_sort_by 		= jQuery("#wpmbg_sort_by").val();
		var wpmbg_status 		= jQuery("#wpmbg_status").val();

		// end of table row
            jQuery('#wpmbg_search_status').html("<span style='color: red;'> Searching, please wait...</span>");
            jQuery('.loading').show();
					 jQuery.ajax({
						type: 'POST',
						url: mbgAjax.ajaxurl,
						data: {
							action: 'mbgBooksGivenToMeAjax',
							wpmbg_search_string: wpmbg_search_string,
							wpmbg_num_results: wpmbg_num_results,
							wpmbg_category: wpmbg_category,
							wpmbg_start: wpmbg_start,
							wpmbg_sort_order: wpmbg_sort_order,
							wpmbg_sort_by: wpmbg_sort_by,
							wpmbg_status: wpmbg_status
						
						},
						success: function(data, textStatus, XMLHttpRequest){
							//alert(data);

						var search_results = jQuery.parseJSON(data);
						window.theJson=search_results;
						
							// this will enable or disable the next and prev buttons
						var wpmbg_total = search_results.total;
						var wpmbg_next_start =  parseInt(wpmbg_start, 10) +  parseInt(wpmbg_num_results,10);
						var wpmbg_prev_start =  parseInt(wpmbg_num_results,10) -  parseInt(wpmbg_start, 10);
					
					
						wpmbg_start =  parseInt(wpmbg_start, 10)

							// if we have some more max sure disabled for next button is off
							if (wpmbg_start < wpmbg_total)
							{
							jQuery("#wpmbg_search_next").removeAttr("disabled", "disabled");
							}
							

							// this will disable the next button
							if (( wpmbg_start < wpmbg_total ) && (wpmbg_next_start > wpmbg_total))
							{
							jQuery('#wpmbg_search_next').attr('disabled', 'disabled');
							} 
									
							// check the prev button
							if  (wpmbg_start == 0)
							{
							jQuery('#wpmbg_search_prev').attr('disabled', 'disabled');
							} else {
							jQuery("#wpmbg_search_prev").removeAttr("disabled", "disabled");	
							}
							
						
     
							//jQuery("#search_results").append(data);
							jQuery('#wpmbg_search_status').html("");
							jQuery('.loading').hide();
							
							jQuery('#search_results').html(search_results.code);
						},
						error: function(MLHttpRequest, textStatus, errorThrown){
							mbgShowMessage(errorThrown);
						}
					});
						
};

function mbgImportBookToDraft(id_descr)
{
	 jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
		data: {
			action: 'mbgImportBookToDraftAjax',
			wpmbg_id_descr: id_descr,		
		},
		success: function(data, textStatus, XMLHttpRequest){
			mbgShowMessage(data);

		var results = jQuery.parseJSON(data);

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			mbgShowMessage(errorThrown);
		}
	});

};

function mbgBookNotifyMBGAjax(id_descr)
{
	 jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
		data: {
			action: 'mbgBookNotifyMBGAjax',
			wpmbg_id_descr: id_descr,		
		},
		success: function(data, textStatus, XMLHttpRequest){
			mbgShowMessage(data);

		var results = jQuery.parseJSON(data);

		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			mbgShowMessage(errorThrown);
		}
	});

};

function mbgBookRefuseAjax(id_descr)
{
mbgInputDlg('Refuse eBook', 
	
	function(){
		jQuery.ajax({
			type: 'POST',
			url: mbgAjax.ajaxurl,
			data: {
				action: 'mbgBookRejectAjax',
				wpmbg_id_descr: id_descr,
				wpmbg_refuse_reason: jQuery('#mbg-dlg-input-txt').val()
			},
			success: function(data, textStatus, XMLHttpRequest){
				mbgShowMessage(data);
			},
			error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
			}
		});
	
	});
};
        //////////End of eBooks///////////////////////////////////////////


        
        
        

function mbgShowMessage(pmsg)
{
mbgCloseMessage();

	if(pmsg != '')
	{
	var wnd = jQuery('<div class="mbg-popup rounded"><span onclick="mbgCloseMessage()" class="close-btn">x</span><p>'+pmsg+'</p></div>');
	
	width = 500;
	//var top = jQuery(window).scrollTop()+100;
	var top = 100;
	var container = jQuery('body');
			
	var left = jQuery(window).width() / 2 - width / 2;
	
	wnd.css('top', top + 'px');
	wnd.css('left', left + 'px');
//	wnd.corner("5px");

	container.prepend(wnd);
	//wnd.css('position', 'absolute');
	wnd.css('position', 'fixed');
	wnd.fadeIn(1000);
	//setTimeout('jQuery(".mbg-popup").fadeOut(1000)', 5000);
	}
};

function mbgCloseMessage()
{
jQuery('.mbg-popup').fadeOut(1000);
};

    // выводит диалог
function mbgDlg(title, onok, dlg_code, width, top)
{
mbgCloseDlg();
dlg_code = dlg_code || '';

var code = '<div class="mbg-dlg">';
code += '<div class="mbg-dlg-title"><span onclick="mbgCloseDlg()" class="close-btn">x</span><p>'+title+'</p></div>';
code += '<div class="mbg-dlg-body">';
code += dlg_code;
code += '<p class="dlg-buttons">';

    if(typeof(onok) == 'function')
    {
    code += '<input id="mbg-dlg-ok" type="button" value="OK" />';
    }
    
code += '<input type="button" onclick="mbgCloseDlg();" value="Cancel" />';

code += '</p>';
code += '</div>';
code += '</div>';

var wnd = jQuery(code);
	
width = width || 600;

var top = top || 100;

var container = jQuery('body');
			
var left = jQuery(window).width() / 2 - width / 2;
	
wnd.css('top',   top + 'px');
wnd.css('left',  left + 'px');
wnd.css('width', width + 'px');
//	wnd.corner("5px");

container.prepend(wnd);
//wnd.css('position', 'absolute');
wnd.css('position', 'fixed');
wnd.fadeIn(1000);

	if(typeof(onok) == 'function')
	{
	jQuery('.mbg-dlg #mbg-dlg-ok').click(
					function(){
					onok();
					mbgCloseDlg();				    
					});
	}
};

    /*
     *  диалог, с запросом ввода
     *  
     * @param {type} title
     * @param {type} onok
     * @param {type} cont
     * @returns {undefined}
     */
function mbgInputDlg(title, onok, cont)
{
cont = cont || '';

var code = '<textarea id="mbg-dlg-input-txt">'+cont+'</textarea>';

mbgDlg(title, onok, code);
};

    /*
     *  диалог, формируемый из загружаемого шаблона tpl
     *  
     * @param {type} tpl
     * @param {type} onok
     * @param {type} postdata
     * @returns {undefined}
     */

function mbgDlgTpl(tpl, onok, postdata, title)
{
title    = title || '';
postdata = postdata || '';
postdata += '&action=mbgLoadTplAjax&tpl='+tpl;

	 jQuery.ajax({
		type: 'POST',
		url: mbgAjax.ajaxurl,
		data: postdata,
		success: function(data, textStatus, XMLHttpRequest){
			 	var result = jQuery.parseJSON(data);
						
                                    if(typeof(result['err']) == 'string' && result['err'] != '')
                                    {
                                    mbgShowMessage(result['err']);
                                    }
                                    else if(typeof(result['code']) == 'string' && result['code'] != '')
                                    {
                                    mbgDlg(title, onok, result['code']);
                                    }                                    
				},
		error: function(MLHttpRequest, textStatus, errorThrown){
				mbgShowMessage(errorThrown);
				}
		});
}
    /*
     *  закрывает диалог
     * @returns {undefined}
     */
function mbgCloseDlg()
{
jQuery('.mbg-dlg').fadeOut(1000);
jQuery('.mbg-dlg').remove();
};

