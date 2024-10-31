<div class="wrap">

	
<h2><?php echo WPMBG_DISPLAY_NAME ?> Find Infographics</h2>


<div id="poststuff" >
<div style="width:75%;" class="postbox-container" >
<div class="search_form">
<label>Category: </label>
<?php     
		$cats = obtain_categories();
	  	$options = display_categories($cats, $options);
?>	 

<label>Search String:</label>
<input name="wpmbg_search_string" id="wpmbg_search_string" size="30" />
<label>Number of Results:</label>
<select name="wpmbg_num_results" id="wpmbg_num_results">
<option value="5">5</option>
<option value="10">10</option>
<option value="50">50</option>
<option value="100">100</option>
</select>


<select name="wpmbg_sort_by" id="wpmbg_sort_by">
<option value="title">Title</option>
<option value="publish_date">Publish Date</option>
<option value="username">Username</option>
<option value="rating">Rating</option>
</select>

<select name="wpmbg_sort_order" id="wpmbg_sort_order">
<option value="asc">Asc</option>
<option value="desc">Desc</option>
</select>

<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_search_ig_submit" value="Search" /><span class="loading" style="display:none"> <img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">





</div><br />

<span id="wpmbg_prev_area">
<input class="button" type="button" name="wpmbg_search_prev" id="wpmbg_search_ig_prev" value="Prev"/>
</span>
<span id="wpmbg_next_area">
<input class="button" type="button" name="wpmbg_search_next" id="wpmbg_search_ig_next" value="Next"/>

</span>


</div>


<?php
mbg_add_custom_box();
?>
</div></div>
