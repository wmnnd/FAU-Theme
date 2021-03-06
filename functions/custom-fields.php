<?php

/*
 * Metaboxes and adjustments for generell custom fields 
 */


add_action( 'load-post.php', 'fau_metabox_cf_setup' );
add_action( 'load-post-new.php', 'fau_metabox_cf_setup' );



/* Meta box setup function. */
function fau_metabox_cf_setup() {
    global $options;
	/* Display Metabox */
	add_action( 'add_meta_boxes_page', 'fau_add_metabox_page' );
	add_action( 'add_meta_boxes_post', 'fau_add_metabox_post' );


	/* Save sidecontent */
	add_action( 'save_post', 'fau_save_metabox_page_untertitel', 10, 2 );	
	add_action( 'save_post', 'fau_save_metabox_page_subnavmenu', 10, 2 );
	
	
	add_action( 'save_post', 'fau_save_metabox_page_portalmenu', 10, 2 );
	add_action( 'save_post', 'fau_save_metabox_page_imagelinks', 10, 2 );
	
	add_action( 'save_post', 'fau_save_metabox_page_sidebar', 10, 2 );

	if ($options['advanced_post_active_subtitle']==true) {
	    add_action( 'save_post', 'fau_save_metabox_post_untertitel', 10, 2 );
	}	
	if ($options['advanced_activateads'] == true) {
	    add_action( 'save_post', 'fau_save_metabox_page_ad', 10, 2 );
	}
	if ($options['advanced_beitragsoptionen']==true) {
	    add_action( 'save_post', 'fau_save_post_teaser', 10, 2 );
	}
	if ($options['advanced_topevent']==true) {
	    add_action( 'save_post', 'fau_save_post_topevent', 10, 2 );
	}

}


/* Create one or more meta boxes to be displayed on the post editor screen. */

function fau_add_metabox_page() {
    global $options;
	add_meta_box(
		'fau_metabox_page_untertitel',			
		esc_html__( 'Untertitel', 'fau' ),		
		'fau_do_metabox_page_untertitel',		
		 'page','normal','high'
	);
	add_meta_box(
		'fau_metabox_page_portalmenu',			
		esc_html__( 'Optionen für Portalseiten', 'fau' ),		
		'fau_do_metabox_page_portalmenu',		
		 'page','side','core'
	);

	add_meta_box(
		'fau_metabox_page_subnavmenu',			
		esc_html__( 'Menüoptionen', 'fau' ),		
		'fau_do_metabox_page_subnavmenu',		
		 'page','side','core'
	);
	add_meta_box(
		'fau_metabox_page_imagelinks',			
		esc_html__( 'Logos (Bildlinks) anzeigen', 'fau' ),		
		'fau_do_metabox_page_imagelinks',		
		 'page','side','core'
	);
	if ($options['advanced_activateads'] == true) {
	    add_meta_box(
		    'fau_metabox_page_ad',			
		    esc_html__( 'Werbung aktivieren', 'fau' ),		
		    'fau_do_metabox_page_ad',		
		     'page','side','core'
	    );
	}

	add_meta_box(
		'fau_metabox_page_sidebar',			
		esc_html__( 'Sidebar', 'fau' ),		
		'fau_do_metabox_page_sidebar',		
		 'page','normal','core'
	);
  
 
}

function fau_add_metabox_post() {
	global $options;
	add_meta_box(
		'fau_metabox_post_teaser',			
		esc_html__( 'Beitragsoptionen', 'fau' ),		
		'fau_do_metabox_post_teaser',		
		 'post','normal','high'
	);
	if ($options['advanced_post_active_subtitle']==true) {
	    add_meta_box(
		   'fau_metabox_post_untertitel',			
		    esc_html__( 'Untertitel', 'fau' ),		
		    'fau_do_metabox_post_untertitel',	
		     'post','normal','high'
	    );
	}
	if ($options['advanced_topevent']==true) {
	    add_meta_box(
		    'fau_metabox_post_topevent',			
		    esc_html__( 'Top-Event', 'fau' ),		
		    'fau_do_metabox_post_topevent',		
		     'post','normal','high'
	    );
	}
	
}





/* Display Options for posts */
function fau_do_metabox_post_teaser( $object, $box ) { 
	global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_post_teaser_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'post' == $post_type ) {
	    if ( !current_user_can( 'edit_post', $object->ID) )
		return;
	} else {
	    return;
	}
	
	
	
	
	echo "<p>\n";
	 echo __('Bitte beachten: Damit ein Artikel auf der Startseite angezeigt werden soll, muss er das folgende Schlagwort erhalten: ','fau');
	   echo '<b>'.$options['start_prefix_tag_newscontent'].'</b> - '.__('Dies gefolgt von einer Nummer (1-3) für die Reihenfolge.','fau'); 
	   if (isset($options['slider-catid']) && $options['slider-catid']>0) {
 
		$category = get_category($options['slider-catid']);	   
		if (isset($category->name)) {
		    echo ' '.__('Damit ein Artikel in der Bühne erscheint, muss er folgender Kategorie angehören: ','fau');
		    echo '<b>'.$category->name.'</b>';
		}
	    }
	   
	echo "</p>\n";
	
	if ($options['advanced_beitragsoptionen']==true) {	
	    
	    $howto = __('Kurztext für die Bühne und den Newsindex (Startseite und Indexseiten). Wenn leer, wird der Kurztext automatisch aus dem Inhalt abzüglich der erlaubten Zeichen gebildet. ','fau');
	    $howto .= '<br>'.__('Erlaubte Anzahl an Zeichen:','fau');
	    $howto .= ' <span class="fauval_anleser_signs">'.$options['default_anleser_excerpt_length'].'</span>';	
	    
	    
	    $abstract  = get_post_meta( $object->ID, 'abstract', true );	
	    fau_form_textarea('fauval_anleser', $abstract, __('Anleser','fau'), 80, 5, $howto);

	    $external_link =  get_post_meta( $object->ID, 'external_link', true );
	    fau_form_url('fauval_external_link', $external_link, __( "Externer Link", 'fau' ), __('Wenn der Artikel nicht auf der Website liegt, sondern auf eine externe Seite verlinkt werden soll, ist hier eine URL anzugeben.','fau'), $placeholder='http://', $size = 0);

	    $override_thumbdesc =  get_post_meta( $object->ID, 'fauval_overwrite_thumbdesc', true );
	    fau_form_text('fauval_overwrite_thumbdesc', $override_thumbdesc, __('Ersetze Bildbeschreibung','fau'), __('Mit diesem optionalen Text kann die Bildunterschrift des verwendeten Beitragsbildes durch einen eigenen Text ersetzt werden, der nur für diesen Beitrag gilt.','fau'));

	    $sliderimage =  get_post_meta( $object->ID, 'fauval_slider_image', true );
	    fau_form_image('fauval_slider_image', $sliderimage, __('Bühnenbild','fau'), __('An dieser Stelle kann optional ein alternatives Bild für die Bühne der Startseite ausgewählt werden, falls das normale Beitragsbild hierzu nicht verwendet werden soll.','fau'),540,150);
	}
 }

 /* Save the meta box's post/page metadata. */
function fau_save_post_teaser( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_post_teaser_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_post_teaser_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'post' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}

	$newval = ( isset( $_POST['fauval_anleser'] ) ? wp_filter_nohtml_kses( $_POST['fauval_anleser'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'abstract', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'abstract', $newval );
	    } else {
		add_post_meta( $post_id, 'abstract', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'abstract', $oldval );	
	} 

	if (filter_var($_POST['fauval_external_link'], FILTER_VALIDATE_URL)) {
	    $newval =  $_POST['fauval_external_link']  ; 
	} else {
	    $newval = '';
	}
	$oldval = get_post_meta( $post_id, 'external_link', true );
	
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'external_link', $newval );
	    } else {
		add_post_meta( $post_id, 'external_link', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'external_link', $oldval );	
	} 
	
	$newval = ( isset( $_POST['fauval_overwrite_thumbdesc'] ) ? wp_filter_nohtml_kses( $_POST['fauval_overwrite_thumbdesc'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'fauval_overwrite_thumbdesc', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_overwrite_thumbdesc', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_overwrite_thumbdesc', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'fauval_overwrite_thumbdesc', $oldval );	
	} 
	
	
	$newval = ( isset( $_POST['fauval_slider_image'] ) ? intval( $_POST['fauval_slider_image'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'fauval_slider_image', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_slider_image', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_slider_image', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'fauval_slider_image', $oldval );	
	} 
	
	
}

 
 
/* Display Options for posts */
function fau_do_metabox_post_topevent( $object, $box ) { 
	global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_post_topevent_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'post' == $post_type ) {
	    if ( !current_user_can( 'edit_post', $object->ID) )
		return;
	} else {
	    return;
	}
	
	
	$topevent_desc  = get_post_meta( $object->ID, 'topevent_description', true );
	$topevent_date  = get_post_meta( $object->ID, 'topevent_date', true );
	$topevent_image  = get_post_meta( $object->ID, 'topevent_image', true );
		
	?>

	
	
	<p>
	   <?php echo __('Bitte beachten: Damit ein Artikel als Top-Event angezeigt wird, muss er das folgende Schlagwort erhalten: ','fau');
	   echo '<b>'.$options['start_topevents_tag'].'</b>'; ?>
	</p>
	
	
	<?php 
	    $topevent_title  = get_post_meta( $object->ID, 'topevent_title', true );
	    fau_form_text('fauval_topevent_title', $topevent_title, __('Titel','fau'), __('Titel wie er in der Sidebar erscheinen soll. Wenn leer, wird der normale Titel des Beitrags verwendet.','fau'));
	?>

	
	
	<div class="optionseingabe">
	    <p>
		    <label for="fauval_topevent_desc">
			<?php _e( "Kurzbeschreibung", 'fau' ); ?>:
		    </label>
	    </p>
	    <textarea name="fauval_topevent_desc" id="fauval_topevent_desc" class="large-text" rows="3" ><?php echo $topevent_desc; ?></textarea>	
	    <br>
	    <div class="howto"><?php echo __('Kurztext für die Sidebar. Wenn leer, wird der Anleser verwendet.','fau');
	    echo ' '. __('Erlaubte Anzahl an Zeichen:','fau');
	    echo ' <span class="fauval_topevent_desc_signs">'.$options['default_topevent_excerpt_length'].'</span>';
	    ?></div>
	</div>
	<div class="optionseingabe">
	    <p>
		    <label for="fauval_topevent_date">
			<?php _e( "Datum", 'fau' ); ?>:
		    </label>
	    </p>
	    <input type="date" name="fauval_topevent_date" id="fauval_topevent_date" class="text" value="<?php echo $topevent_date; ?>">		
	    <br>
	    
	    
	    <div class="howto"><?php echo __('Geben Sie hier das Datum des Events ein.','fau'); ?></div>
	    
	    <script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#fauval_topevent_date').datepicker();
});
</script>
	    
	    
	</div>
	
	<div class="optionseingabe">
	    <p>
		    <label for="fauval_topevent_image">
			<?php _e( "Symbolbild", 'fau' ); ?>:
		    </label>
	    </p>

	    <?php 
	    echo '<div class="uploader">';
	    
	    $image = '';
	    $imagehtml = '';
	    if (isset($topevent_image) && ($topevent_image>0)) {
		$image = wp_get_attachment_image_src($topevent_image, 'topevent-thumb'); 
		if (isset($image)) {
		    $imagehtml = '<img class="image_show_topevent_image" src="'.$image[0].'" width="'.$options['default_topevent_thumb_width'].'" height="'.$options['default_topevent_thumb_height'].'" alt="">';
		}
	    }

	    echo '<div class="previewimage showimg_topevent_image">';
	    if (!empty($imagehtml)) {  
		echo $imagehtml;
	    } else {
		 $imagehtml = '<img src="'.fau_esc_url($options['default_topevent_thumb_src']).'" width="'.$options['default_topevent_thumb_width'].'" height="'.$options['default_topevent_thumb_height'].'" alt="">';			    
		 echo $imagehtml;
		 echo "<br>";
		 _e('Kein Bild ausgewählt. Ersatzbild wird gezeigt.', 'fau');
	    } 
	    echo "</div>\n"; ?>		

	    <input type="hidden" name="fauval_topevent_image" id="fauval_topevent_image" 
		     value="<?php echo sanitize_key( $topevent_image ) ; ?>" />

	    <input class="button" name="image_button_topevent_image" id="image_button_topevent_image" value="<?php _e('Bild auswählen', 'fau'); ?>" />
	    <small><a href="#" class="image_remove_topevent_image"><?php _e( "Entfernen", 'fau' );?></a></small>
	    <br><div class="howto"><?php echo __('Hier können Sie ein Thumbnail auswählen für den Event. Wenn kein Bild gewählt wird, wird ein Ersatzbild angezeigt.','fau'); ?>	      
	    </div><script>
	    jQuery(document).ready(function() {
		jQuery('#image_button_topevent_image').click(function()  {
		    wp.media.editor.send.attachment = function(props, attachment) {
			jQuery('#fauval_topevent_image').val(attachment.id);
			htmlshow = "<img src=\""+attachment.url + "\" width=\"<?php echo $options['default_topevent_thumb_width'];?>\" height=\"<?php echo $options['default_topevent_thumb_height'];?>\">";  					   
			jQuery('.showimg_topevent_image').html(htmlshow);

		    }
		    wp.media.editor.open(this);
		    return false;
		});
	    });
	    jQuery(document).ready(function() {
		jQuery('.image_remove_topevent_image').click(function()   {
			jQuery('#fauval_topevent_image').val('');
			jQuery('.showimg_topevent_image').html('<?php _e('Kein Bild ausgewählt.', 'fau'); ?>');
			return false;
		});
	    });
	   </script> 		    	    
	    
	   </div>
	</div>
	
	
	<?php 

 }

 
 /* Save the meta box's post/page metadata. */
function fau_save_post_topevent( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_post_topevent_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_post_topevent_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'post' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}

	$newval = ( isset( $_POST['fauval_topevent_title'] ) ? sanitize_text_field( $_POST['fauval_topevent_title'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'topevent_title', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'topevent_title', $newval );
	    } else {
		add_post_meta( $post_id, 'topevent_title', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'topevent_title', $oldval );	
	} 
	
	$newval = ( isset( $_POST['fauval_topevent_desc'] ) ?  wp_filter_nohtml_kses( $_POST['fauval_topevent_desc'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'topevent_description', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'topevent_description', $newval );
	    } else {
		add_post_meta( $post_id, 'topevent_description', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'topevent_description', $oldval );	
	} 

	
	$newval = ( isset( $_POST['fauval_topevent_date'] ) ?  sanitize_option( 'date_format', $_POST['fauval_topevent_date'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'topevent_date', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'topevent_date', $newval );
	    } else {
		add_post_meta( $post_id, 'topevent_date', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'topevent_date', $oldval );	
	} 
	
	$newval = ( isset( $_POST['fauval_topevent_image'] ) ?  intval( $_POST['fauval_topevent_image'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'topevent_image', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'topevent_image', $newval );
	    } else {
		add_post_meta( $post_id, 'topevent_image', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'topevent_image', $oldval );	
	} 
		


	
}

 
 /* Display Options for menuquotes on pages */
function fau_do_metabox_page_subnavmenu( $object, $box ) { 
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_subnavmenu_nonce' ); 
	
	if ( !current_user_can( 'edit_page', $object->ID) )
	    return;


	$menuebene = get_post_meta( $object->ID, 'menu-level', true );
	?>
	
	<p>
		<label for="fau_metabox_page_menuebene">
                    <?php _e( "Menüebene", 'fau' ); ?>:
                </label>
	</p>
	<select name="fau_metabox_page_menuebene" id="fau_metabox_page_menuebene">
	    <option value="1" <?php selected($menuebene,1); ?>>1. Ebene</option>
	    <option value="2" <?php selected($menuebene,2); ?>>2. Ebene</option>
	    <option value="3" <?php selected($menuebene,3); ?>>3. Ebene</option>  
	</select>
	<p class="howto">
	    <?php _e('Die Menüebene definiert bei Seiten bis zur welchen Ebene das Menu auf der linken Seite gezeigt wird. Dies gilt nur für Seiten, die das folgende Template ausgewählt haben:','fau'); ?>
	    <code><?php _e('Inhaltsseite mit Navi','fau');?></code> 
	</p>
	
	<?php 

 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_subnavmenu( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_page_subnavmenu_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_subnavmenu_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}

	
	$newval = intval($_POST['fau_metabox_page_menuebene']);
	$oldval = get_post_meta( $post_id, 'menu-level', true );
	
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'menu-level', $newval );
	    } else {
		add_post_meta( $post_id, 'menu-level', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'menu-level', $oldval );	
	} 
	
}



/* Display Options for menuquotes on pages */
function fau_do_metabox_post_untertitel( $object, $box ) { 
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_post_untertitel_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( !current_user_can( 'edit_post', $object->ID) )
	    return;
	
	
	$untertitel  = get_post_meta( $object->ID, 'fauval_untertitel', true );	
	fau_form_text('fau_metabox_post_untertitel', $untertitel, __('Untertitel (Inhaltsüberschrift)','fau'), __('Dieser Untertitel erscheint im Inhaltsbereich, unterhalb des Balkens mit dem eigentlichen Titel.','fau'));

 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_post_untertitel( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_post_untertitel_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_post_untertitel_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	

	$newval = ( isset( $_POST['fau_metabox_post_untertitel'] ) ? sanitize_text_field( $_POST['fau_metabox_post_untertitel'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'fauval_untertitel', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_untertitel', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_untertitel', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'fauval_untertitel', $oldval );	
	} 
}




/* Display Options for menuquotes on pages */
function fau_do_metabox_page_untertitel( $object, $box ) { 
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_untertitel_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'page' == $post_type ) {
	    if ( !current_user_can( 'edit_page', $object->ID) )
		    // Oder sollten wir nach publish_pages  fragen? 
		    // oder nach der Rolle? vgl. http://docs.appthemes.com/tutorials/wordpress-check-user-role-function/ 
		return;
	} else {
	    return;
	}
	
	$untertitel  = get_post_meta( $object->ID, 'headline', true );	
	fau_form_text('fau_metabox_page_untertitel', $untertitel, __('Untertitel (Inhaltsüberschrift)','fau'), __('Dieser Untertitel erscheint im Inhaltsbereich, unterhalb des Balkens mit dem eigentlichen Titel.','fau'));

 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_untertitel( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_page_untertitel_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_untertitel_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}

	$newval = ( isset( $_POST['fau_metabox_page_untertitel'] ) ? sanitize_text_field( $_POST['fau_metabox_page_untertitel'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'headline', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'headline', $newval );
	    } else {
		add_post_meta( $post_id, 'headline', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'headline', $oldval );	
	} 
}



/* Display Options for menuquotes on pages */
function fau_do_metabox_page_portalmenu( $object, $box ) { 
    global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_portalmenu_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'page' == $post_type ) {
	    if ( !current_user_can( 'edit_page', $object->ID) )
		 
		return;
	} else {
	    return;
	}
	
	$quote  = get_post_meta( $object->ID, 'zitat_text', true );	
	$author =  get_post_meta( $object->ID, 'zitat_autor', true );
		
	echo '<div id="portalseitenquote">';
	    
	fau_form_textarea('fau_metabox_menuquote_quote', $quote, __( "Zitat", 'fau' ),40,3, __('Das Zitat und der Autor erscheint bei Portalseiten oder Menüpunkten der ersten Ebene des Hauptmenüs neben der Auflistung der Untermenüpunkte.','fau')); 	    
	fau_form_text('fau_metabox_menuquote_autor', $author, __( "Autor", 'fau' ), __('Dieser freie Text kann einen Namen enthalten auf den das Zitat zurückzuführen ist oder andere Informationen hierzu.','fau'), '', 20);
	
	echo '</div>';
	
	$currentmenu  = get_post_meta( $object->ID, 'portalmenu-slug', true );	
	$currentmenuid = 0;
	if ($currentmenu == sanitize_key($currentmenu)) {
	    $currentmenuid = $currentmenu;
	} else {
	    $thisterm = get_term_by('name', $currentmenu, 'nav_menu');
	    if (!isset($thisterm)) {
		$thisterm = get_term_by('slug', $currentmenu, 'nav_menu');
	    }
	    if ($thisterm !==false) {
		$currentmenuid = $thisterm->term_id;    
	    }
	}
	
 
	$thislist = array();
	$menuliste = get_terms('nav_menu', array('orderby'=> 'name','hide_empty'=>true));
	foreach($menuliste as $term){
		$term_id = $term->term_id;
		$term_name = $term->name;	
		$thislist[$term->term_id] =  $term->name;
	}
	fau_form_select('fau_metabox_page_portalmenu_id',$thislist,$currentmenuid,__('Portalmenü','fau'),
		__('Bei einer Portalseite wird unter dem Inhalt ein Menu ausgegeben. Bitte wählen Sie hier das Menü aus der Liste. Sollte das Menü noch nicht existieren, kann ein Administrator es anlegen.','fau'),
		1, __('Kein Portalmenu zeigen','fau'));

	$nothumbnails  = get_post_meta( $object->ID, 'fauval_portalmenu_thumbnailson', true ); 
	fau_form_onoff('fau_metabox_page_portalmenu_nothumbnails',$nothumbnails,__('Artikelbilder verstecken; Nur Überschriften zeigen.','fau'));
	
	$nofallbackthumbs  = get_post_meta( $object->ID, 'fauval_portalmenu_nofallbackthumb', true );	
	fau_form_onoff('fau_metabox_page_portalmenu_nofallbackthumb',$nofallbackthumbs,__('Keine Ersatzbilder zeigen, wenn Artikelbilder nicht gesetzt sind.','fau'));

	$nosub  = get_post_meta( $object->ID, 'fauval_portalmenu_nosub', true );	
	fau_form_onoff('fau_metabox_page_portalmenu_nosub',$nosub,__('Unterpunkte verbergen.','fau'));

	


 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_portalmenu( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_page_portalmenu_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_portalmenu_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}

	$newval =$_POST['fau_metabox_page_portalmenu_id'];
	$oldval = get_post_meta( $post_id, 'portalmenu-slug', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'portalmenu-slug', $newval );
	    } else {
		add_post_meta( $post_id, 'portalmenu-slug', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'portalmenu-slug', $oldval );	
	} 
	
	$newval = intval($_POST['fau_metabox_page_portalmenu_nothumbnails']);
	$oldval = get_post_meta( $post_id, 'fauval_portalmenu_thumbnailson', true );
	
	if ($newval==1) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_portalmenu_thumbnailson', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_portalmenu_thumbnailson', $newval, true );
	    }
	} else {
	    delete_post_meta( $post_id, 'fauval_portalmenu_thumbnailson' );	
	} 
	
	$newval = intval($_POST['fau_metabox_page_portalmenu_nofallbackthumb']);
	$oldval = get_post_meta( $post_id, 'fauval_portalmenu_nofallbackthumb', true );
	
	if ($newval==1) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_portalmenu_nofallbackthumb', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_portalmenu_nofallbackthumb', $newval, true );
	    }
	} else {
	    delete_post_meta( $post_id, 'fauval_portalmenu_nofallbackthumb' );	
	} 

	$newval = intval($_POST['fau_metabox_page_portalmenu_nosub']);
	$oldval = get_post_meta( $post_id, 'fauval_portalmenu_nosub', true );
	
	if ($newval==1) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_portalmenu_nosub', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_portalmenu_nosub', $newval, true );
	    }
	} else {
	    delete_post_meta( $post_id, 'fauval_portalmenu_nosub' );	
	} 
	
	
	$newval = ( isset( $_POST['fau_metabox_menuquote_quote'] ) ? sanitize_text_field( $_POST['fau_metabox_menuquote_quote'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'zitat_text', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'zitat_text', $newval );
	    } else {
		add_post_meta( $post_id, 'zitat_text', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'zitat_text', $oldval );	
	} 

	
	$newval = ( isset( $_POST['fau_metabox_menuquote_autor'] ) ? sanitize_text_field( $_POST['fau_metabox_menuquote_autor'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'zitat_autor', true );
	
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'zitat_autor', $newval );
	    } else {
		add_post_meta( $post_id, 'zitat_autor', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'zitat_autor', $oldval );	
	} 
}

/* 
 * Imagelinks einbinden 
 */

/* Display Options for menuquotes on pages */
function fau_do_metabox_page_imagelinks( $object, $box ) { 
    global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_imagelinks_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'page' == $post_type ) {
	    if ( !current_user_can( 'edit_page', $object->ID) )
		 
		return;
	} else {
	    return;
	}

	$thislist = array();
	$categories = get_categories( array('type' => 'imagelink', 'taxonomy' => 'imagelinks_category', 'orderby' => 'name', 'order' => 'ASC', 'hide_empty' => 1 ) ); 
	foreach($categories as $category) {
	    if (!is_wp_error( $category )) {
		if ($category->count > 1) {
		    $thislist[$category->cat_ID] = $category->name.' ('.$category->count.' '.__('Bilder','fau').')';
		} else {
		    $thislist[$category->cat_ID] = $category->name.' ('.$category->count.' '.__('Bild','fau').')';
		}
	    }	
	}	
	
	if (empty($thislist)) {
	    echo __('Es wurden noch keine Bilder als Logos definiert. Daher kann hie rnoch nichts ausgewählt werden.','fau');
	} else {
	    $currentcat  = get_post_meta( $object->ID, 'fauval_imagelink_catid', true );
	    fau_form_select('fau_metabox_page_imagelinks_catid',$thislist,$currentcat,__('Kategorie','fau'),
		__('Wählen Sie hier die Kategorie aus aus der Logos (Bildlinks) verwendet werden sollen. Die Bilder aus der gewählten Kategorie werden dann angezeigt.','fau'),
		1, __('Keine Logos zeigen','fau'));
	}
	return;


 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_imagelinks( $post_id, $post ) {
	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['fau_metabox_page_imagelinks_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_imagelinks_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}

	$newval = intval($_POST['fau_metabox_page_imagelinks_catid']);
	$oldval = get_post_meta( $post_id, 'fauval_imagelink_catid', true );
	
	if ($newval>0) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_imagelink_catid', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_imagelink_catid', $newval, true );
	    }
	} else {
	    delete_post_meta( $post_id, 'fauval_imagelink_catid', $oldval );	
	} 

}




/* 
 * Werbung aktivieren 
 */

/* Display Options for menuquotes on pages */
function fau_do_metabox_page_ad( $object, $box ) { 
    global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_ad_nonce' ); 
	$post_type = get_post_type( $object->ID); 
	
	if ( 'page' == $post_type ) {
	    if ( !current_user_can( 'edit_page', $object->ID) )
		 
		return;
	} else {
	    return;
	}
	
	
	$allads = get_posts( array('post_type' => 'ad', 'posts_per_page' => -1));
	if ($allads) {
	    $sidebarads = array('-1' => __('Keine (Deaktivieren)','fau'));
	    $bottomads = array('-1' => __('Keine (Deaktivieren)','fau'));
	    
	    foreach ($allads as $ad) {
		$title = get_the_title($ad->ID);
		$position = get_post_meta( $ad->ID, 'fauval_ad_position', true );
		if ($position==1) {
		    // Nur in der Sidebar
		    $sidebarads[$ad->ID] = $title;
	        } elseif ($position==2) {		    
		    // Nur Unten
		    $bottomads[$ad->ID] = $title;
		} else {
		    // Beide Bereiceh oder unedefiniert
		    $sidebarads[$ad->ID] = $title;
		    $bottomads[$ad->ID] = $title;
		}
	    }
	    wp_reset_postdata();
	    $listseite = get_post_meta( $object->ID, 'werbebanner_seitlich', true );
	    $listunten = get_post_meta( $object->ID, 'werbebanner_unten', true );
	    
	    
	    fau_form_multiselect('werbebanner_seitlich', $sidebarads, $listseite, __('Sidebar','fau'),  __('Wählen Sie die Werbung, die in der Sidebar erscheinen soll.','fau'), 0 );	    
	    fau_form_multiselect('werbebanner_unten', $bottomads, $listunten, __('Inhaltsbereich','fau'),  __('Wählen Sie die Werbung, die unterhalb des Inhalts erscheinen soll.','fau'), 0);
	    
	    
	} else {
	    _e('Es wurde noch keine Werbung definiert, die angezeigt werden kann.', 'fau');
	}
	return;
 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_ad( $post_id, $post ) {
	if ( !isset( $_POST['fau_metabox_page_ad_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_ad_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	}

	
	$newval = $_POST['werbebanner_seitlich'];
	$oldval = get_post_meta( $post_id, 'werbebanner_seitlich', true );
	$remove = 0;
	$found =0;
	if (isset($newval)) {
	    foreach ($newval as $i) {
		if ($i == -1) {
		    $remove = 1;
		} elseif ($i >0) {
		    $found = 1;
		}
	    }
	}
	
	if (($remove==1) || ($found==0)) {
	     delete_post_meta( $post_id, 'werbebanner_seitlich' );	 
	} else {
		if (isset($oldval))  {
		    update_post_meta( $post_id, 'werbebanner_seitlich', $newval );
		} else {
		    add_post_meta( $post_id, 'werbebanner_seitlich', $newval, true );
		}
	}
	
	
	$newval = $_POST['werbebanner_unten'];
	$oldval = get_post_meta( $post_id, 'werbebanner_unten', true );
	$remove = 0;
	$found =0;
	if (isset($newval)) {
	    foreach ($newval as $i) {
		if ($i == -1) {
		    $remove = 1;
		} elseif ($i >0) {
		    $found = 1;
		}
	    }
	}
	
	if (($remove==1) || ($found==0)) {
	     delete_post_meta( $post_id, 'werbebanner_unten' );	 
	} else {
		if (isset($oldval))  {
		    update_post_meta( $post_id, 'werbebanner_unten', $newval );
		} else {
		    add_post_meta( $post_id, 'werbebanner_unten', $newval, true );
		}
	}

	
}




/* 
 * Sidebar der Seiten  
 */

/* Display Options for menuquotes on pages */
function fau_do_metabox_page_sidebar( $object, $box ) { 
    global $options;
	wp_nonce_field( basename( __FILE__ ), 'fau_metabox_page_sidebar_nonce' ); 


	 if ( !current_user_can( 'edit_page', $object->ID) )
	    return;
	

        
	if ($options['advanced_page_sidebar_titleabove']) {
	    $sidebar_title_above = get_post_meta( $object->ID, 'sidebar_title_above', true );
	    fau_form_text('sidebar_title_above', $sidebar_title_above, __('Titel oben','fau'), __('Titel am Anfang der Sidebar','fau'));
	}
	$sidebar_text_above = get_post_meta( $object->ID, 'sidebar_text_above', true );
	if ($options['advanced_page_sidebar_useeditor_textabove']) {
	    fau_form_wpeditor('sidebar_text_above', $sidebar_text_above, __('Textbereich oben','fau'), __('Text am Anfang der Sidebar','fau'),true);
	} else {
	    fau_form_textarea('sidebar_text_above', $sidebar_text_above, __('Textbereich oben','fau'), $cols=50, $rows=5, __('Text am Anfang der Sidebar','fau')); 
	}

   
	if (($options['advanced_page_sidebar_linkblock1_number'] > 0) || ($options['advanced_page_sidebar_linkblock2_number'] > 0)) {
	    // Frage nach Reihenfolge Linklisten vs Personen
	    
	    $fauval_sidebar_order_personlinks = get_post_meta( $object->ID, 'fauval_sidebar_order_personlinks', true );
	    if (!isset($fauval_sidebar_order_personlinks)) {
		$fauval_sidebar_order_personlinks = $options['advanced_page_sidebar_order_personlinks'];
	    }
	    fau_form_select('fauval_sidebar_order_personlinks', array(0 => __('Zuerst Kontake, dann Linklisten','fau'), 1 => __('Zuerst Linklisten, dann Kontakte', 'fau')), $fauval_sidebar_order_personlinks, __('Reihenfolge Kontakte und Linklisten'),  __('Hier kann die Reihenfolge von Kontakte und Linklisten geändert werden, wie sie auf der Seite präsentiert werden.','fau'), 0 );
	}	
	
	$personen = get_posts(array('post_type' => 'person', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'suppress_filters' => false));
	if ($personen) {
	    $auswahl = array('-1' => __('Keine (Deaktivieren)','fau'));
	    $found = 0;
	    foreach ($personen as $current) {
		$title = get_the_title($current->ID);
		$auswahl[$current->ID] = $title;
		$found = 1;
	    }
	    wp_reset_postdata();
	    if ($found==1) {
		$sidebar_personen = get_post_meta( $object->ID, 'sidebar_personen', true );
		$sidebar_title_personen = get_post_meta( $object->ID, 'sidebar_title_personen', true );	 
		fau_form_text('sidebar_title_personen', $sidebar_title_personen, $options['advanced_page_sidebar_personen_title'], __('Titel über Ansprechpartner','fau'));
		fau_form_multiselect('sidebar_personen', $auswahl, $sidebar_personen, __('Auswahl Ansprechpartner','fau'),  __('Wählen Sie die Personen oder Ansprechpartner, die in der Sidebar erscheinen sollen. Es kann mehr als ein Eintrag gewählt werden.','fau'), 0 );	    
	    } else {
		echo __('Derzeit sind noch Persoen oder Kontakte eingetragen, die man verlinken könnte.','fau');
	    }
	} 
	

	
	
	if ($options['advanced_page_sidebar_linkblock1_number'] > 0) {	
	    $sidebar_quicklinks = get_post_meta( $object->ID, 'sidebar_quicklinks', true );
	    // Alter ACF Rotz
	    
	    $block_title = get_post_meta( $object->ID, 'fauval_sidebar_title_linkblock1', true );
	    if (strlen(trim($block_title))<1) {
		$oldtitle = get_post_meta( $object->ID, 'sidebar_title_quicklinks', true );
		if (strlen(trim($oldtitle))>0) {
		    $block_title = $oldtitle;
		// } else {
		   // $block_title = $options['advanced_page_sidebar_linkblock1_title'];
		}
	    }
	    fau_form_text('fauval_sidebar_title_linkblock1', $block_title, __('Titel erster Linkblock','fau'), __('Titel über die erste Liste von Links, sogenannte Quicklinks','fau'));
	    for ($i = 1; $i <= $options['advanced_page_sidebar_linkblock1_number']; $i++) {
		$name = 'fauval_linkblock1_link'.$i;
		$title = __('Link Nr. ','fau').$i;
		$urlname= $name.'_url';
		$titlename= $name.'_title';
		
		$oldpageid =  get_post_meta( $object->ID, $name, true );
		$oldurl =  get_post_meta( $object->ID, $urlname, true );
		$oldtitle =  get_post_meta( $object->ID, $titlename, true );
		$c = $i-1;
		if (empty($oldpageid) && empty($oldurl) && empty($oldtitle)) {
		    if (isset($sidebar_quicklinks) && (isset($sidebar_quicklinks[$c]))) {
			$oldpageid = $sidebar_quicklinks[$c];
			if (isset($oldpageid) && ($oldpageid>0)) {
			    $oldtitle = get_the_title($oldpageid );
			    $oldurl = get_permalink($oldpageid );
			}
		    }
		}
		
		fau_form_link($name, $oldtitle, $oldurl, $title);
	    }
	}
	
	
	if ($options['advanced_page_sidebar_linkblock2_number'] > 0) {	    
	    	    
	    $sidebar_quicklinks = get_post_meta( $object->ID, 'sidebar_quicklinks_external', true );
	   	    // Alter ACF Rotz mit SubFields	    
	    
	    $block_title = get_post_meta( $object->ID, 'fauval_sidebar_title_linkblock2', true );
	    // Default erstmal auskommentiert wenn man es leer haben will; irritiert sonst
	    // if (isset($block_title) && strlen(trim($block_title))<1) {
		//$block_title = $options['advanced_page_sidebar_linkblock2_title'];	
	    //}
	    fau_form_text('fauval_sidebar_title_linkblock2', $block_title, __('Titel zweiter Linkblock','fau'), __('Titel über die zweite Liste von Links. Weitere Links oder bspw. externe Links.','fau'));


	   for ($i = 1; $i <= $options['advanced_page_sidebar_linkblock2_number']; $i++) {
		$name = 'fauval_linkblock2_link'.$i;
		$title = __('Link Nr. ','fau').$i;
		$urlname= $name.'_url';
		$titlename= $name.'_title';
		
		$oldpageid =  get_post_meta( $object->ID, $name, true );
		$oldurl =  get_post_meta( $object->ID, $urlname, true );
		$oldtitle =  get_post_meta( $object->ID, $titlename, true );
		$c = $i-1;
		if (empty($oldpageid) && empty($oldurl) && empty($oldtitle)) {
		    if (!empty($sidebar_quicklinks)) {
			// Schau nach alten ACF Subfields
			$oldlinkname = 'sidebar_quicklinks_external_'.$c.'_sidebar_quicklinks_external_text';
			$oldlinkurl = 'sidebar_quicklinks_external_'.$c.'_sidebar_quicklinks_external_link';
			$oldurl =  get_post_meta( $object->ID, $oldlinkurl, true );
			$oldtitle =  get_post_meta( $object->ID, $oldlinkname, true );
		    }
		}
		
		fau_form_link($name, $oldtitle, $oldurl, $title);
	    }
	}


	if ($options['advanced_page_sidebar_titlebelow']) {
	    $sidebar_title_below = get_post_meta( $object->ID, 'sidebar_title_below', true );
	    fau_form_text('sidebar_title_below', $sidebar_title_below, __('Titel unten','fau'), __('Titel am Ende der Sidebar','fau'));
	}
 
	$sidebar_text_below = get_post_meta( $object->ID, 'sidebar_text_below', true );	
	if ($options['advanced_page_sidebar_useeditor_textbelow']) {
	    fau_form_wpeditor('sidebar_text_below', $sidebar_text_below, __('Textbereich unten','fau'), __('Text am Ende der Sidebar','fau'),true);
	} else {
	    fau_form_textarea('sidebar_text_below', $sidebar_text_below, __('Textbereich unten','fau'), $cols=50, $rows=5, __('Text am Ende der Sidebar','fau')); 
	}		    
	    

	return;
 }

/* Save the meta box's post/page metadata. */
function fau_save_metabox_page_sidebar( $post_id, $post ) {
	if ( !isset( $_POST['fau_metabox_page_sidebar_nonce'] ) || !wp_verify_nonce( $_POST['fau_metabox_page_sidebar_nonce'], basename( __FILE__ ) ) )
		return $post_id;


	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	
	$newval = ( isset( $_POST['sidebar_title_above'] ) ? sanitize_text_field( $_POST['sidebar_title_above'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'sidebar_title_above', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'sidebar_title_above', $newval );
	    } else {
		add_post_meta( $post_id, 'sidebar_title_above', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'sidebar_title_above', $oldval );	
	} 
	
	
	$newval = ( isset( $_POST['sidebar_text_above'] ) ? $_POST['sidebar_text_above']  : 0 );
	$oldval = get_post_meta( $post_id, 'sidebar_text_above', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'sidebar_text_above', $newval );
	    } else {
		add_post_meta( $post_id, 'sidebar_text_above', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'sidebar_text_above', $oldval );	
	} 
	
	
	$newval = ( isset( $_POST['sidebar_title_below'] ) ? sanitize_text_field( $_POST['sidebar_title_below'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'sidebar_title_below', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'sidebar_title_below', $newval );
	    } else {
		add_post_meta( $post_id, 'sidebar_title_below', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'sidebar_title_below', $oldval );	
	} 
	
	
	$newval = ( isset( $_POST['sidebar_text_below'] ) ? $_POST['sidebar_text_below']  : 0 );
	$oldval = get_post_meta( $post_id, 'sidebar_text_below', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'sidebar_text_below', $newval );
	    } else {
		add_post_meta( $post_id, 'sidebar_text_below', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'sidebar_text_below', $oldval );	
	} 
	
	
	$newval = intval($_POST['fauval_sidebar_order_personlinks']);
	$oldval = get_post_meta( $post_id, 'fauval_sidebar_order_personlinks', true );
	
	if ($newval>0) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_sidebar_order_personlinks', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_sidebar_order_personlinks', $newval, true );
	    }
	} else {
	    delete_post_meta( $post_id, 'fauval_sidebar_order_personlinks', $oldval );	
	} 

	
	$newval = ( isset( $_POST['sidebar_title_personen'] ) ? sanitize_text_field( $_POST['sidebar_title_personen'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'sidebar_title_personen', true );
	
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'sidebar_title_personen', $newval );
	    } else {
		add_post_meta( $post_id, 'sidebar_title_personen', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'sidebar_title_personen', $oldval );	
	} 
	

	
	$newval = $_POST['sidebar_personen'];
	$oldval = get_post_meta( $post_id, 'sidebar_personen', true );
	$remove = 0;
	$found =0;
	if (isset($newval)) {
	    foreach ($newval as $i) {
		if ($i == -1) {
		    $remove = 1;
		} elseif ($i >0) {
		    $found = 1;
		}
	    }
	}
	
	if (($remove==1) || ($found==0)) {
	     delete_post_meta( $post_id, 'sidebar_personen' );	 
	} else {
		if (isset($oldval))  {
		    update_post_meta( $post_id, 'sidebar_personen', $newval );
		} else {
		    add_post_meta( $post_id, 'sidebar_personen', $newval, true );
		}
	}

	$newval = ( isset( $_POST['fauval_sidebar_title_linkblock1'] ) ? sanitize_text_field( $_POST['fauval_sidebar_title_linkblock1'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'fauval_sidebar_title_linkblock1', true );
	if ((!isset($oldvar)) || empty($oldvar) ) {
	    	$oldval = get_post_meta( $post_id, 'sidebar_title_quicklinks', true );  
	}
	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_sidebar_title_linkblock1', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_sidebar_title_linkblock1', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'fauval_sidebar_title_linkblock1', $oldval );	
	} 
	
	
	
	$newval = ( isset( $_POST['fauval_sidebar_title_linkblock2'] ) ? sanitize_text_field( $_POST['fauval_sidebar_title_linkblock2'] ) : 0 );
	$oldval = get_post_meta( $post_id, 'fauval_sidebar_title_linkblock2', true );

	if (!empty(trim($newval))) {
	    if (isset($oldval)  && ($oldval != $newval)) {
		update_post_meta( $post_id, 'fauval_sidebar_title_linkblock2', $newval );
	    } else {
		add_post_meta( $post_id, 'fauval_sidebar_title_linkblock2', $newval, true );
	    }
	} elseif ($oldval) {
	    delete_post_meta( $post_id, 'fauval_sidebar_title_linkblock2', $oldval );	
	} 
	
	
	global $options;
	 $sidebar_quicklinks = get_post_meta( $post_id, 'sidebar_quicklinks', true );
	  if (isset($sidebar_quicklinks))  {
			delete_post_meta( $post_id, 'sidebar_quicklinks' );
	 }
		    
	    for ($i = 1; $i <= $options['advanced_page_sidebar_linkblock1_number']; $i++) {
		$name = 'fauval_linkblock1_link'.$i;
		$urlname= $name.'_url';
		$titlename= $name.'_title';
		$oldpageid =  get_post_meta( $post_id, $name, true );
		$oldurl =  get_post_meta( $post_id, $urlname, true );
		$oldtitle =  get_post_meta( $post_id, $titlename, true );
		
		
		$newurl = ( isset( $_POST[$urlname] ) ? esc_url( $_POST[$urlname] ) : 0 );
		$newid = ( isset( $_POST[$name] ) ? sanitize_key( $_POST[$name] ) : 0 );
		$newtitle = ( isset( $_POST[$titlename] ) ? sanitize_text_field( $_POST[$titlename] ) : 0 );
		
		if (!isset($newid) || ($newid <=0)) {
		    // Versuche aus der URL die ID zu ermitteln
		    $relativeurl = fau_make_link_relative($newurl);
		    if ($relativeurl != $newurl) {
			// Ist eine interne URL, also mnuss es eine ID geben			
			$newid = url_to_postid( $newurl );
		    }
		} 

		if (empty($newid) && empty($newurl) && empty($newtitle) && (!empty($oldtitle))) {
		    delete_post_meta( $post_id, $name );	
     		    delete_post_meta( $post_id, $titlename );	
		    delete_post_meta( $post_id, $urlname );
		} else {	 
		    update_post_meta( $post_id, $urlname, $newurl );
		    update_post_meta( $post_id, $titlename, $newtitle );	    
		    update_post_meta( $post_id, $name, $newid );			    
		}
	    }
	    
	    $sidebar_quicklinks = get_post_meta( $post_id, 'sidebar_quicklinks_external', true );
	   	    // Alter ACF Rotz mit SubFields	    
	    if (isset($sidebar_quicklinks))  {
			delete_post_meta( $post_id, 'sidebar_quicklinks_external' );
	    }
	    for ($i = 1; $i <= $options['advanced_page_sidebar_linkblock2_number']; $i++) {
		$name = 'fauval_linkblock2_link'.$i;
		$urlname= $name.'_url';
		$titlename= $name.'_title';
		$oldpageid =  get_post_meta( $post_id, $name, true );
		$oldurl =  get_post_meta( $post_id, $urlname, true );
		$oldtitle =  get_post_meta( $post_id, $titlename, true );
		
		$newurl = ( isset( $_POST[$urlname] ) ? esc_url( $_POST[$urlname] ) : 0 );
		$newid = ( isset( $_POST[$name] ) ? sanitize_key( $_POST[$name] ) : 0 );
		$newtitle = ( isset( $_POST[$titlename] ) ? sanitize_text_field( $_POST[$titlename] ) : 0 );
		
		if (!isset($newid) || ($newid <=0)) {
		    // Versuche aus der URL die ID zu ermitteln
		    $relativeurl = fau_make_link_relative($newurl);
		    if ($relativeurl != $newurl) {
			// Ist eine interne URL, also mnuss es eine ID geben			
			$newid = url_to_postid( $newurl );
		    }
		} 
 
		if (empty($newid) && empty($newurl) && empty($newtitle) && (!empty($oldtitle))) {
		    delete_post_meta( $post_id, $name );	
     		    delete_post_meta( $post_id, $titlename );	
		    delete_post_meta( $post_id, $urlname );
		} else {
		    update_post_meta( $post_id, $urlname, $newurl );
		    update_post_meta( $post_id, $titlename, $newtitle );
		    update_post_meta( $post_id, $name, $newid );			    
		} 
	    }
	

}

/*
 *  Ersetzt das wpLink-Skript durch ein benutzerdefiniertes Skript.
 */
add_action('admin_enqueue_scripts', function () {
    $suffix = defined('WP_DEBUG') && WP_DEBUG ? '' : '.min';

    // Erst deaktivieren wir das Standard-Wordpress-Skript
    wp_deregister_script('wplink');

    // Dann ersetzen wir es durch unser benutzerdefiniertes wpLink-Skript
    wp_enqueue_script('rrze-wplink', get_template_directory_uri() . "/js/rrze-wplink$suffix.js", array('jquery'), FALSE, TRUE);

    // Lokalisierung des Skripts
    $localized = array(
            'title' => __('Link einfügen/ändern', 'fau'),
            'update' => __('Aktualisieren', 'fau'),
            'save' => __('Link hinzufügen', 'fau'),
            'noTitle' => __('(kein Titel)', 'fau'),
            'noMatchesFound' => __('Keine Ergebnisse gefunden.', 'fau')
    );

    wp_localize_script('rrze-wplink', 'wpLinkL10n', $localized);
    
}, 999);


