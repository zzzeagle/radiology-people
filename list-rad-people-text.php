<?php
function noHTMLFaculty_text($input, $encoding = 'UTF-8'){
		return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
}

function list_rad_people_text( $atts ){
  $clinical_section = noHTMLFaculty(get_query_var( 'clinical_section'));
  $clinical_section = ucwords(str_replace('%20', ' ', $clinical_section));

// added vice chairs, admin leaders, clinical leaders & edu leaders	
	$a = shortcode_atts( array(
		'classification' => '',
		'section' => $clinical_section,
		'fields' => '',
		'labels' => 'true',
		'no_button' => 'false',
		'section_chiefs' => 'false',
		'vice_chairs' => 'false',
		'admin_leaders' => 'false',
		'clinical_leaders' => 'false',
		'edu_leaders' => 'false',
	), $atts );
	
	
$out .= '<table>';
// added vice chairs, admin leaders, clinical leaders & edu leaders
if($a['section_chiefs']==='true'):
   $section_chief_array = array('key' => 'section_chief','value' => '1','compare' => 'LIKE',);
   endif;
if($a['vice_chairs']==='true'):
   $vice_chairs_array = array('key' => 'vice_chairs','value' => '1','compare' => 'LIKE',);
   endif;
if($a['admin_leaders']==='true'):
   $admin_leaders_array = array('key' => 'admin_leaders','value' => '1','compare' => 'LIKE',);
   endif;
if($a['clinical_leaders']==='true'):
   $clinical_leaders_array = array('key' => 'clinical_leaders','value' => '1','compare' => 'LIKE',);
   endif;
if($a['edu_leaders']==='true'):
   $edu_leaders_array = array('key' => 'edu_leaders','value' => '1','compare' => 'LIKE',);
   
endif;

// added vice chairs, admin leaders, clinical leaders & edu leaders
if($a['section'] && $a['classification']=='faculty'):
	$orderby = array('section_chief' => 'DESC','last_name' => 'ASC',);
elseif($a['section'] && $a['classification']=='staff'):

     $orderby = array('admin_leaders' => 'DESC','last_name' => 'ASC',);	

else:
	$orderby = array('last_name' => 'ASC',);
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
// added vice chairs, admin leaders, clinical leaders & edu leaders
				$section_chief_array,
				$vice_chairs_array,
				$admin_leaders_array,
				$clinical_leaders_array,
				$edu_leaders_array,
				

				'last_name' => array(
					'key' => 'last_name',
					'compare' => 'exists',
					),
			),
	'orderby' => $orderby,

);


// query
$the_query = new WP_Query( $args );
$the_query;
?>
<?php if( $the_query->have_posts() ):
	$i = 0;
	$out .= '<ul class="flex-container">';
	while( $the_query->have_posts() ) : $the_query->the_post();
					if($i==0 and get_field('section_chief')==1 and $a['section']):
						$sc = 1;
						$sectionchiefstyle = '-section-chief';
					else:
						$sc = 0;
						$sectionchiefstyle = '';
					endif;
					$out .= '<li class="flex-item-text'. $sectionchiefstyle. '">';

					//if person has a suffix, get the suffix.
					$suffix = "";
					if (get_field('suffix')):
						$suffix = ', ' . get_field('suffix');
					endif;
					$out .= '<h6 style="margin-top:0px;margin-bottom:0px;font-weight:600"><a href="'.get_permalink().'">'.get_field( 'first_name' ).' '.get_field( 'last_name' ). $suffix .'</a></h6>';
					$out .= '<p style="overflow:hidden; margin:0 0 0!important;">';
					
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

		$out .= '</li>';
		$i++;
	endwhile;
	$out .= "</ul>";
	endif;

wp_reset_query();
$out .= "</table>";
return $out;
}

add_shortcode( 'list-rad-people-text', 'list_rad_people_text');


function add_query_vars_filter_text( $vars ){
  $vars[] = "clinical_section";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter_text' );