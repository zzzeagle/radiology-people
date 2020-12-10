<?php
function noHTMLFaculty($input, $encoding = 'UTF-8'){
		return htmlentities($input, ENT_QUOTES | ENT_HTML5, $encoding);
}

function list_rad_people( $atts ){
  $clinical_section = noHTMLFaculty(get_query_var( 'clinical_section'));
  $clinical_section = ucwords(str_replace('%20', ' ', $clinical_section));
  $research_group = noHTMLFaculty(get_query_var( 'research_group'));
  $research_group = ucwords(str_replace('%20', ' ', $research_group));

	
	$a = shortcode_atts( array(
		'classification' => '',
		'section' => $clinical_section,
		'research_group' => $research_group,
		'fields' => '',
		'labels' => 'true',
		'graduation_year' => '',
		'fellowship_program' => '',
		'fellowship_year' => '',
		'no_button' => 'false',
		'new' => 'false',
		'fullwidth' => 'false',
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

if($a['research_group']):
   $research_group_array = array('key' => 'research_group','value' => $a['research_group'],'compare' => 'LIKE',);
endif;

if($a['fellowship_year']):
   $fellowship_year_array = array('key' => 'uw_fellowship_graduation_year','value' => $a['fellowship_year'],'compare' => 'LIKE',);
endif;
if($a['fellowship_program']):
   $fellowship_program_array = array('key' => 'uw_fellowship_alumni_program','value' => $a['fellowship_program'],'compare' => 'LIKE',);
endif;
if($a['graduation_year']):
   $graduation_year_array = array('key' => 'graduation_year','value' => $a['graduation_year'],'compare' => 'LIKE',);
endif;

if($a['new']=='true'):
	$date = date('Y-m-d');
	$startdate = date('Y-m-d',(strtotime ( '-1 year' , strtotime ( $date) ) ));
	$new_people_array = array('key' => 'start_date','value' => $startdate,'compare' => '>=','type' => 'DATE');
endif;


if($a['section'] && $a['classification']==='faculty'):
	$orderby = array('section_chief' => 'DESC','last_name' => 'ASC',);
	$section_chief_exists = array('key' => 'section_chief','compare' => 'exists',);
elseif($a['classification']==='staff'):
     $orderby = array('admin_leaders' => 'DESC','last_name' => 'ASC',);
	 $admin_leaders_exists = array('key' => 'admin_leaders','compare' => 'exists',);
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
			   'research_group' => $research_group_array,
			   'graduation_year' => $graduation_year_array,
			   'fellowship_year' => $fellowship_year_array,
			   'fellowship_program' => $fellowship_program_array,
			   'new_people' => $new_people_array,
			   'admin_leaders' => $admin_leaders_exists,
			   'section_chief' => $section_chief_exists,
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
					//if($a['fullwidth'] = 'true'):
						//$sectionchiefstyle = '-section-chief';
					//endif;
					
					$out .= '<li class="flex-item'. $sectionchiefstyle. '">';
					$out .= '<div class="person-flex'. $sectionchiefstyle. '">';
					$image = get_field('picture');
					if($image):
						$size = 'medium';
						$mugshot = $image['sizes'][$size];
						$out .= '<div class="person-image" style="clear:right;"><img class="person-image" loading="lazy" style="" src="'.$mugshot.'""></div>';
					endif;
					$out .= '<div class="person-text'. $sectionchiefstyle. '">';
					
					//if person has a suffix, get the suffix.
					$suffix = "";
					if (get_field('suffix')):
						$suffix = ', ' . get_field('suffix');
					endif;
					$out .= '<h3 style="margin-top:5px;margin-bottom:0px;">'.get_field( 'first_name' ).' '.get_field( 'last_name' ). $suffix .'</h3>';
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
						
						if($sc==0):
							$out .= '<div class="person-more">'. $buttonshortcode . '</div>';
						endif;
					endif;
					$out .= '<br></p></div>';
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

function add_query_vars_filter( $vars ){
  $vars[] = "clinical_section";
  return $vars;
}
add_filter( 'query_vars', 'add_query_vars_filter' );