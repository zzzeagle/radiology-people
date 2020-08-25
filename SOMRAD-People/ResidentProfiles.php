<?php
/**
* Plugin Name: SOMRAD - List People
* Description: This plugin will be used to list People
* Version: 0.0.1
* Author: Zachary Eagle
*/


function list_rad_people( $atts ){
	$a = shortcode_atts( array(
		'classification' => '',
		'section' => '',
		'fields' => '',
		'labels' => 'true',
		'graduation_year' => '',
		'fellowship_program' => '',
		'fellowship_year' => '',
		'no_button' => 'false',
	), $atts );
	
	
$out .= '<table>';

if($a['fellowship_year']):
   $fellowship_year_array = array('key' => 'uw_fellowship_graduation_year','value' => $a['fellowship_year'],'compare' => 'LIKE',);
endif;
if($a['fellowship_program']):
   $fellowship_program_array = array('key' => 'uw_fellowship_alumni_program','value' => $a['fellowship_program'],'compare' => 'LIKE',);
endif;

$args = array(
	'numberposts'	=> -1,
	'post_type'	=> 'person',
	'meta_query' =>array(
				'relation' => 'AND',
				array(
					'key' => 'classification',
					'value' => $a['classification'],
					'compare' => 'LIKE',
			   ),
			   array(
					'key' => 'section',
					'value' => $a['section'],
					'compare' => 'LIKE',
			   ),
			   array(
					'key' => 'graduation_year',
					'value' => $a['graduation_year'],
					'compare' => 'LIKE',
			   ),
			   'fellowship_year' => $fellowship_year_array,
			   'fellowship_program' => $fellowship_program_array,
			   'section_chief' => array(
					'key' => 'section_chief',
					'compare' => 'exists',
					),
				'last_name' => array(
					'key' => 'last_name',
					'compare' => 'exists',
					),
			),
	'orderby' =>  array('section_chief' => 'DESC',
						'last_name' => 'ASC',),

);


// query
$the_query = new WP_Query( $args );
$the_query;
?>
<?php if( $the_query->have_posts() ):
	$i = 0;
	$out .= '<ul class="flex-container">';
	while( $the_query->have_posts() ) : $the_query->the_post();
					if($i==0 and get_field('section_chief')==1):
						$sc = 1;
						$sectionchiefstyle = '-section-chief';
					else:
						$sc = 0;
						$sectionchiefstyle = '';
					endif;
					$out .= '<li class="flex-item'. $sectionchiefstyle. '">';
					$out .= '<div class="person-flex'. $sectionchiefstyle. '">';
					$out .= '<div style="clear:right;"><img class="person-image" style="" src="'.get_field( 'picture' ).'""></div>';
					$out .= '<div class="person-text'. $sectionchiefstyle. '">';
					$out .= '<h3 style="margin-top:5px;margin-bottom:0px;">'.get_field( 'first_name' ).' '.get_field( 'last_name' ).', '.get_field( 'suffix' ).'</h3>';
					$out .= '<p style="overflow:hidden;">';
					
					//If the field has a colon, split the field and check if no-label is specified.
					$fields = explode(",", $a['fields']);
					foreach ($fields as $field){
						$label = $a['labels'];
						if (strstr($field, ':')):
							$exploded_field = explode(":",$field);
							$field = $exploded_field[0];
							if($exploded_field[1] == "no-label"):
								$label = "false";
							endif;
						endif;
						
						$item = get_field_object($field);
						if($item['value']):
							$value = $item['value'];
							if(is_array($value)):
								$value = implode(", ",  $value);
							endif;
							if($label == 'false'):
								$out .= $value . '<br>';
							else:
								$out .= '<b>'.$item['label'].': </b>' . $value . '<br>';
							endif;
						endif;
					}
					if($a['no_button'] == 'false'):
						$buttonshortcode = do_shortcode('[button color=purple type=small=true url="'.get_permalink().'"]View Full Profile[/button]');
						if($sc==1):
							$out .= '<div class="person-more">'. $buttonshortcode . '</div>';
						endif;
						$out .= '<br></p></div>';
						if($sc==0):
							$out .= '<div class="person-more">'. $buttonshortcode . '</div>';
						endif;
					endif;
					$out .= '</div>';
					$out .= '</div>';

		$out .= '</li>';
		$i++;
	endwhile;
	$out .= "</ul>";
	endif;

wp_reset_query();
$out .= "</table>";
return $out;
}

add_shortcode( 'list-rad-people', 'list_rad_people');

function person_flex_style() {
    wp_enqueue_style( 'style-name', plugins_url('person-flex.css',__FILE__ ));
}
add_action( 'wp_enqueue_scripts', 'person_flex_style' );