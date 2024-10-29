<?php

/*
Plugin Name: WP-All-Image-Sizes
Plugin URI: http://wordpress.org/plugins/all-image-list/
Description: List all registered image sizes in a meta box on the attachment page (media library) and in the Media Uploader.
Author: Salvatore Fresta
Version: 0.2
Author URI: http://www.salvatorefresta.net
*/ 

/**
 * Adds a box to the main column on the Attachment screen.
 */
function wp_all_images_sizes_add_meta_box() {

  add_meta_box('all_images_sizes_id', __( 'Image list' ), 'wp_all_images_sizes_meta_box', 'attachment', 'side');

}

add_action( 'add_meta_boxes', 'wp_all_images_sizes_add_meta_box' );


/**
 * Meta Box
 * 
 * @param WP_Post $post The object for the current post (attachment in this case)
 */
function wp_all_images_sizes_meta_box( $post ) {

  if(!wp_attachment_is_image($post->ID)) return false;

  $imagedata = wp_get_attachment_metadata($post->ID);

  if(!is_array($imagedata) || !is_array($imagedata['sizes'])) return false;

  $current_thumb_url =  wp_get_attachment_image_src( $attachment_id);
  $img_url = str_replace(basename($current_thumb_url[0]), "", $current_thumb_url[0]);


?>

  <div id="all_image_sizes">

    <div id="misc-publishing-actions">

	    <div class="misc-pub-section">

	      <strong><?php _e( 'Images' ); ?>:</strong>

	      <select name="all_image_sizes_list" class="all_image_sizes_list">
		  <option value="#" selected><?php _e("Select Image"); ?></option>
		  
		  <?php 

			foreach( $imagedata['sizes'] as $img_name => $val ): 

			$current_thumb_url = wp_get_attachment_image_src( $attachment_id, array( $val['width'], $val['height'] ), true ); 

		  ?>

			<option value="<?php echo $img_url.$val['file']; ?>" data-width="<?php echo $val['width']; ?>" data-height="<?php echo $val['height']; ?>" data-filename="<?php echo $val['file']; ?>"><?php echo $img_name; ?> (<?php echo $val['width']; ?> &times; <?php echo $val['height']; ?>)</option>

		  <?php endforeach; ?>

		</select>

	    </div>

	    <div class="misc-pub-section">
	      <label for="attachment_url"><?php _e( 'File URL:' ); ?></label>
	      <input type="text" class="widefat urlfield" readonly="readonly" name="attachment_url" value="">
	    </div>

	    <div class="misc-pub-section">
		    <?php _e( 'File name:' ); ?> <strong class="filename"></strong>
	    </div>

	    <div class="misc-pub-section">
		    <?php _e( 'Dimensions:' ); ?> <strong class="dimensions"></strong>
	    </div>

	    <div id="major-publishing-actions">
	      <div id="publishing-action">
		  <a href="#" target="out" class="all_image_sizes_link"><input type="button" class="button-primary button-large" id="publish" accesskey="p" value="<?php _e("Show"); ?>"></a>
	      </div>
	    </div>

	    <div class="clear"></div>

    </div>



  </div>

  <script>

    jQuery(".all_image_sizes_list").live("change", function() {

      var selected = jQuery(this).find('option:selected');
      var width = selected.data('width'); 
      var height = selected.data('height');
      var link = selected.val();
      var filename = selected.data('filename');

      if(link == "#") return false;

      jQuery("#all_image_sizes .urlfield").val(link);
      jQuery("#all_image_sizes .dimensions").html(width+" &times; "+height);
      jQuery("#all_image_sizes .filename").html(filename);
      jQuery("#all_image_sizes .all_image_sizes_link").attr("href", link);

    });

  </script>

<?php

}


/**
 * List all image sizes in Media Uploader
 *
 * @param $sizes, array of default image sizes
 */
function wp_all_images_sizes_media_uploader($sizes) {

  /* The following function exists since Wordpress 3.0 */
  if(!function_exists("get_intermediate_image_sizes")) return $sizes;

  $added_sizes = get_intermediate_image_sizes();

  $new_sizes = array();

  foreach($added_sizes as $key => $value) {
    $new_sizes[$value] = $value;
  }

  return array_merge($new_sizes, $sizes);

}

add_filter('image_size_names_choose', 'wp_all_images_sizes_media_uploader');

?>