<?php
/**
* @package WordPress
* @subpackage FAU
* @since FAU 1.0
*/

class FAUShortcodes {

	function __construct() {
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wpautop' , 12);
		add_action( 'init', array( $this, 'add_shortcodes' ) ); 
	}

	function add_shortcodes() {
		add_shortcode('assistant', array( $this, 'fau_assistant' ));
		add_shortcode('organigram', array( $this, 'fau_organigram'));
		add_shortcode('downloads', array( $this, 'fau_downloads'));
		add_shortcode('hr', array( $this, 'fau_hr'));
	}
	
	function fau_hr ( $atts, $content = null) {
		return '<hr>';
	}
	
	function fau_organigram( $atts, $content = null) {
		extract(shortcode_atts(array(
			"menu" => 'menu'
			), $atts));
			
		return wp_nav_menu( array('menu' => $menu, 'container' => false, 'menu_id' => 'organigram', 'menu_class' => 'organigram', 'echo' => 0));
	}
	
	function fau_downloads( $atts, $content = null) {
		extract(shortcode_atts(array(
			"category" => 'category'
			), $atts));
			
		$category = get_term_by('slug', $category, 'attachment_document');
		
		$return = '';
		
		if($category)
		{
			$return .= $this->fau_downloads_recursive($category->term_id);
			
			$files = get_posts(array('post_type' => 'attachment', 'numberposts' => 1000, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
					 array(
						'taxonomy' => 'attachment_document',
						'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
						'terms' => $category->term_id,
						'include_children' => false
						)
					), 
				'suppress_filters' => true));

			if($files)
			{
				$return .= '<ul class="files">';
					foreach($files as $file)
					{	
						$return .= '<li><a href="'.$file->guid.'">'.$file->post_title.'</a></li>';
					}
				$return .= '</ul>';
			}
		}
		
		return $return;
	}
	
	function fau_downloads_recursive($term_id)
	{
		$categories = get_terms(array('attachment_document'), array('parent' => $term_id, 'hide_empty' => false));
		
		$return = '';
			
		if($categories)
		{
			$return .= '<div class="accordion">';

			$i = 0;

			foreach($categories as $category)
			{
				$return .= '<div class="accordion-group">';
					$return .= '<div class="accordion-heading">';
						$return .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="accordion-" href="#collapse-downloads-'.$category->term_id.'-'.$i.'">'.$category->name.'</a>';
					$return .= '</div>';
					$return .= '<div id="collapse-downloads-'.$category->term_id.'-'.$i.'" class="accordion-body">';
						$return .= '<div class="accordion-inner clearfix">';

							$return .= $this->fau_downloads_recursive($category->term_id);

							$files = get_posts(array('post_type' => 'attachment', 'numberposts' => 1000, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(
									 array(
										'taxonomy' => 'attachment_document',
										'field' => 'id', // can be slug or id - a CPT-onomy term's ID is the same as its post ID
										'terms' => $category->term_id,
										'include_children' => false
										)
									), 
								'suppress_filters' => true));

							if($files)
							{
								$return .= '<ul class="files">';
									foreach($files as $file)
									{	
										$return .= '<li><a href="'.$file->guid.'">'.$file->post_title.'</a></li>';
									}
								$return .= '</ul>';
							}

						$return .= '</div>';
					$return .= '</div>';
				$return .= '</div>';

				$i++;
			}

			$return .= '</div>';
		}

		return $return;
	}
	
	function fau_subpages( $atts, $content = null ) {
		return '<div class="row">' . do_shortcode( $content ) . '</div>';
	}
		
		
	function fau_assistant( $atts, $content = null) {
		extract(shortcode_atts(array(
			"id" => 'id'
			), $atts));
			
		$return = '';
		
		$return .= '<div class="accordion">';

		$pages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'parent' => $id, 'hierarchical' => 0));
		$i = 0;
		foreach($pages as $page)
		{
			$return .= '<div class="accordion-group">';
				$return .= '<div class="accordion-heading">';
					$return .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="accordion-" href="#collapse-'.$page->ID.'-'.$i.'">'.$page->post_title.'</a>';
				$return .= '</div>';
				$return .= '<div id="collapse-'.$page->ID.'-'.$i.'" class="accordion-body">';
					$return .= '<div class="accordion-inner clearfix">';
						
						$subpages = get_pages(array('sort_order' => 'ASC', 'sort_column' => 'menu_order', 'parent' => $page->ID, 'hierarchical' => 0));
						
						if(count($subpages) > 0){
							$return .= '<div class="assistant-tabs">';

								$return .= '<ul class="assistant-tabs-nav">';

								$j = 0;
								foreach($subpages as $subpage) {
									if($j == 0) $class = 'active';
									else $class = '';

									$return .= '<li><a href="#accordion-'.$page->ID.'-'.$i.'-tab-'.$j.'" class="accordion-tabs-nav-toggle '.$class.'">'.$subpage->post_title.'</a></li>';
									$j++;
								}

								$return .= '</ul>';

								$j = 0;
								foreach($subpages as $subpage)
								{
									if($j == 0) $class = 'assistant-tab-pane-active';
									else $class = '';

									$return .= '<div class="assistant-tab-pane '.$class.'" id="accordion-'.$page->ID.'-'.$i.'-tab-'.$j.'">';
										$return .= '<p>'.do_shortcode($subpage->post_content).'</p>';
									$return .= '</div>';

									$j++;
								}

							$return .= '</div>';
						}
						else
						{
							$return .= '<p>'.do_shortcode($page->post_content).'</p>';
						}
						
						
					$return .= '</div>';
				$return .= '</div>';
			$return .= '</div>';
			
			$i++;
		}
		
		
		$return .= '</div>';
		
		return $return;
	}
}

new FAUShortcodes();




class FAUShortcodesRTE
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'fau_shortcodes_rte_button'));
    }

    public function fau_shortcodes_rte_button()
    {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
        {
            add_filter( 'mce_external_plugins', array($this, 'fau_rte_add_buttons' ));
            add_filter( 'mce_buttons', array($this, 'fau_rte_register_buttons' ));
        }
    }

    public function fau_rte_add_buttons( $plugin_array )
    {
        $plugin_array['faurteshortcodes'] = get_template_directory_uri().'/js/tinymce-shortcodes.js';

        return $plugin_array;
    }

    public function fau_rte_register_buttons( $buttons )
    {
        array_push( $buttons, 'separator', 'faurteshortcodes' );
        return $buttons;
    }

}

new FAUShortcodesRTE();



?>