<div class="wrap">

	
<h2><?php echo WPMBG_DISPLAY_NAME ?> eBooks Given To Me</h2>

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
<option value="publish_date" selected="selected">Publish Date</option>
<option value="username">Username</option>
<option value="rating">Rating</option>
</select>

<select name="wpmbg_sort_order" id="wpmbg_sort_order">
<option value="asc">Asc</option>
<option value="desc" selected="selected">Desc</option>
</select>

<label>Status:</label>
<select name="wpmbg_status" id="wpmbg_status">
<option value="">All</option>
<option value="2" <?php if($wpmbg_status == 2) echo ' selected="selected" '; ?>>Pending Publication</option>
<option value="3" <?php if($wpmbg_status == 3) echo ' selected="selected" '; ?>>Published</option>
</select>

<input type="hidden" name="wpmbg_start" id="wpmbg_start" value="0"/>
<input type="button" class="button" id="wpmbg_search_booksmt_submit" value="Search" /><span class="loading" style="display:none"> <img src="<?php echo WPMBG_IMGS ?>/loading.gif" /></span><span id="wpmbg_search_status"></span>
</div>
<div id="search_results">





</div><br />

<span id="wpmbg_prev_area">
<input class="button" type="button" name="wpmbg_search_prev" id="wpmbg_search_booksmt_prev" value="Prev"/>
</span>
<span id="wpmbg_next_area">
<input class="button" type="button" name="wpmbg_search_next" id="wpmbg_search_booksmt_next" value="Next"/>

</span>


</div>


<?php
mbg_add_custom_box();
?>
</div></div>
