<?php
/**
* Plugin Name: SOMRAD - List Residents
* Description: This plugin will be used to list residents
* Version: 0.0.1
* Author: Zachary Eagle
*/
function noHTML($input, $encoding = 'UTF-8')
{
	return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
}
	
function add_level_vars_filter( $vars ){
  $vars[] = "level";
  return $vars;
}
add_filter( 'query_vars', 'add_level_vars_filter' );

function list_residents( $atts ){
	$a = shortcode_atts( array(
		'classification' => '',
	), $atts );
	

	$level = noHTML(get_query_var( 'level'));
	$level = ucwords(str_replace('%20', ' ', $level));
	$levelClass = "Resident - " . $level;
	
$out .= '<table>';

$args = array(
	'numberposts'	=> -1,
	'post_type'	=> 'person',
	'meta_query' => array(
				'relation' => 'AND',	
				array(
					'key' => 'classification',
					'value' => 'Resident',
					'compare' => 'LIKE',
			   ),
			   	array(
					'key' => 'classification',
					'value' => $levelClass,
					'compare' => 'LIKE',
				),
				),
	'orderby'        => 'meta_value',
	'meta_key'       => 'last_name', 
	'order'          => 'ASC',
);


// query
$the_query = new WP_Query( $args );
$the_query;
?>
<?php if( $the_query->have_posts() ):
	$i = 0;
	if($level):
		$out .= '<h1>' . $level . ' - Residents</h1>';
	else:
		$out .= '<h1>All Residents</h1>';
	endif;
	$out .= '<ul class="flex-container">';
	while( $the_query->have_posts() ) : $the_query->the_post();
					$out .= '<li class="flex-item">';
					$out .= '<div style="float:left; padding-right:10px; display:block;max-width:33%"><img src="'.get_field( 'picture' ).'""></div>';
					$out .= '<div style="min-width:300px;margin-left:10px;">';
					$out .= '<h3 style="margin-top:0px;margin-bottom:0px;">'.get_field( 'first_name' ).' '.get_field( 'last_name' ).', '.get_field( 'suffix' ).'</h3>';
					$out .= '<p style="overflow:hidden;">';
					$out .= get_field('program').'<br>';
					$fields = ["medical_school","undergraduate_school"];
					foreach ($fields as $field){
						$item = get_field_object($field);
						if($item['value']):
							$out .= '<b>'.$item['label'].': </b><br>' . $item['value'] . '<br>';
						endif;
					}
					$out .= '<a href="'.get_permalink().'">View Full Profile</a>';
					$out .= '<br></p></div>';

		$out .= '</li>';
		$i++;
	endwhile;
	$out .= "</ul>";
	endif;

wp_reset_query();
return $out;
}

add_shortcode( 'list-residents', 'list_residents');

function person_flex_style() {
    wp_enqueue_style( 'style-name', plugins_url('person-flex.css',__FILE__ ));
}
add_action( 'wp_enqueue_scripts', 'person_flex_style' );