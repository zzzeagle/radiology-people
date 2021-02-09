<?php
/**
* Plugin Name: Radiology People
* Description: This plugin adds the radiology people content type
* Version: 0.0.2
* Updated: 2018.08.13
* Author: Zachary Eagle
*/
if ( ! function_exists('person') ) {
// Register Custom Post Type
function person() {

	$labels = array(
		'name'                => _x( 'People', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Person', 'text_domain' ),
		'name_admin_bar'      => __( 'Person', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'Person', 'text_domain' ),
		'description'         => __( 'Radiology Person', 'text_domain' ),
		'labels'              => $labels,
		'menu_icon'	      => 'dashicons-admin-users',
		'supports'            => array( 'title', 'custom-fields', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,		
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'capabilities' => array(
		  'edit_post'          => 'edit_person', 
		  'read_post'          => 'read_person', 
		  'delete_post'        => 'delete_person', 
		  'edit_posts'         => 'edit_people', 
		  'edit_others_posts'  => 'edit_others_people', 
		  'publish_posts'      => 'publish_people',       
		  'read_private_posts' => 'read_private_people', 
		  'create_posts'       => 'edit_people', 
		),
		'rewrite' => array('slug' => 'radiology-personnel', 'with_front' => false),
	);
	register_post_type( 'person', $args );
	#flush_rewrite_rules();
}
add_action( 'init', 'person', 0 );
}
 
  function list_radiology_people_api( $data ) {
  $people = get_posts( array(
    'post_type' => 'person',
	'posts_per_page' => -1,
	'post_status'	=> array('private', 'publish'),
  ) );
	foreach ($people as $key => $person) {
			$picture = get_field('picture', $person->ID);
			$people[$key]->picure = $picture['url'];
			$people[$key]->last_name = get_field('last_name', $person->ID);
			$people[$key]->first_name = get_field('first_name', $person->ID);
			$people[$key]->suffix = get_field('suffix', $person->ID);
			$people[$key]->section = get_field('section', $person->ID);
			$people[$key]->classification = get_field('classification', $person->ID);
			$people[$key]->position = get_field('position', $person->ID);
			$people[$key]->scopus_author_id = get_field('scopus_author_id', $person->ID);			
			$people[$key]->section_chief = get_field('section_chief', $person->ID);			
			$image = get_field('picture', $person->ID);
			$medImage = $image['sizes']['medium'];
			$people[$key]->med_image = $medImage;
			unset($people[$key]->comment_count);
			unset($people[$key]->comment_status);
			unset($people[$key]->post_author);
			unset($people[$key]->post_mime_type);
			unset($people[$key]->post_type);
			unset($people[$key]->post_excerpt);
			unset($people[$key]->ping_status);
			unset($people[$key]->post_content_filtered);
			unset($people[$key]->post_parent);
			unset($people[$key]->post_date);
			unset($people[$key]->post_date_gmt);
			unset($people[$key]->post_modified_gmt);
			unset($people[$key]->menu_order);
			unset($people[$key]->filter);
			unset($people[$key]->to_ping);
			unset($people[$key]->pinged);
			unset($people[$key]->post_password);
			unset($people[$key]->post_content);
			unset($people[$key]->guid);

	}
  if ( empty( $people ) ) {
    return null;
  }
 
  return $people;
}

add_action( 'rest_api_init', function () {
  register_rest_route( 'people/v1', '/all/', array(
    'methods' => 'GET',
    'callback' => 'list_radiology_people_api',
  ) );
} );

function radiology_people_filter_wp_title( $title ) {
    // Return a custom document title for
    // the boat details custom page template
    if ( is_page_template( 'single-person.php' ) ) {
		$persontitle = 'test';
        return $persontitle;
    }
    // Otherwise, don't modify the document title
    return $title;
}
add_filter( 'wp_title', 'radiology_people_filter_wp_title' );

//Add the archive people template
function get_radiology_people_archive_template($archive_template) {
     global $post;

     if ($post->post_type == 'person') {
          $archive_template = dirname( __FILE__ ) . '/archive-person.php';
     }
     return $archive_template;
}
add_filter( 'archive_template', 'get_radiology_people_archive_template' );

//Add the single person template
function get_radiology_people_single_template($single_template) {
     global $post;

     if ($post->post_type == 'person') {
          $single_template = dirname( __FILE__ ) . '/single-person.php';
     }
     return $single_template;
}
add_filter( 'single_template', 'get_radiology_people_single_template' );


include( plugin_dir_path(__FILE__) . 'list-rad-people.php');
include( plugin_dir_path(__FILE__) . 'list-rad-people-text.php');