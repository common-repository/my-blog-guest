<?php
	
function obtain_categories($oauth_token = "")
{

	$url = WPMBG_BASE_URL . "/categories";	
	
        $categories = mbg_api($url);

        return $categories;
}	

function display_categories($cats, $options)
{
	
	$current_cat_id = (isset($_REQUEST['id_category']) && intval($_REQUEST['id_category']) > 0) ? intval($_REQUEST['id_category']) : intval($options['wpmbg_category']);
?>
<select name="wpmbg_category" id="wpmbg_category" style="width: 450px;">
<option value="">------------------------------ Select a category ------------------------------</option>
<?php 
foreach ($cats['categories'] as $cat) {

	$this_cat_id = $cat['id'];
	$this_cat_name = $cat['category'];
?>
    <option value="<?php echo $this_cat_id ?>" <?php if ($this_cat_id == $current_cat_id) { echo "SELECTED"; } ?>><?php echo $this_cat_name ?></option>
<?php } ?>
</select>    
<?php
	return $options;
}



?>