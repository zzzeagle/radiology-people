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
		'nm_residency_graduation_year'=>'',
		'fellowship_program' => '',
		'fellowship_year' => '',
		'no_button' => false,
		'section_button' => false,
		'education_button' => false,
		'new' => false,
		'fullwidth' => false,
		'order' => false,
		'top_label' => false,
		'leadership_group' => false,
		'leader' => '',
		'site' => false,
		'list' => false,
		'single' => false,
		'sort' => false,
		'email' => false,
	), $atts );
	
	
$out .= '<table>';

if($a['leadership_group']){
   $leadership_array = array('key' => $a['leadership_group'],'value' => '1','compare' => 'LIKE',);
}


if($a['research_group']):
   $research_group_array = array('key' => 'research_group','value' => $a['research_group'],'compare' => 'LIKE',);
endif;

if($a['fellowship_year']):
   $fellowship_year_array = array('key' => 'uw_fellowship_graduation_year','value' => $a['fellowship_year'],'compare' => 'LIKE',);
endif;
if($a['fellowship_program']):
   $fellowship_program_array = array('key' => 'uw_fellowship_alumni_program','value' => $a['fellowship_program'],'compare' => 'LIKE',);
endif;

if($a['nm_residency_graduation_year']):
   $graduation_year_array = array('key' => 'nm_residency_graduation_year','value' => $a['nm_residency_graduation_year'],'compare' => 'LIKE',);
endif;

if($a['graduation_year']):
   $graduation_year_array = array('key' => 'graduation_year','value' => $a['graduation_year'],'compare' => 'LIKE',);
endif;
if($a['new']=='true'):
	$date = date('Y-m-d');
	$startdate = date('Y-m-d',(strtotime ( '-1 year' , strtotime ( $date) ) ));
	$new_people_array = array('key' => 'start_date','value' => $startdate,'compare' => '>=','type' => 'DATE');
endif;

if($a['site']):
   $clinical_leaders_site = array('key' => 'clinical_leaders_site','value' => $a['site'],'compare' => 'LIKE',);
endif;

if($a['section'] && strtolower($a['classification'])=='faculty'){
	$orderby = array('section_chief' => 'DESC','last_name' => 'ASC','first_name'=> 'ASC');
	$section_chief_exists = array('key' => 'section_chief','compare' => 'exists',);
}
elseif($a['classification']==='staff'){
     $orderby = array('admin_leaders' => 'DESC','last_name' => 'ASC','first_name'=> 'ASC');
	 $admin_leaders_exists = array('key' => 'admin_leaders','compare' => 'exists',);
}
elseif($a['order']){
	$orderby_exists = array('key' => $a['order'],'compare' => 'exists',);
	$orderby = array($a['order'] => 'ASC',);
}
elseif($a['sort']){
	$orderby = array($a['sort'] => 'ASC',);
}
else{
	$orderby = array('last_name' => 'ASC','first_name'=> 'ASC');
}

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
			   'nm_residency_graduation_year' => $nm_residency_graduation_year,
			   'graduation_year' => $graduation_year_array,
			   'fellowship_year' => $fellowship_year_array,
			   'fellowship_program' => $fellowship_program_array,
			   'new_people' => $new_people_array,
			   'admin_leaders' => $admin_leaders_exists,
			   'section_chief' => $section_chief_exists,
			   'clinical_leaders_site'=> $clinical_leaders_site,
				$leadership_array,
				'last_name' => array(
					'key' => 'last_name',
					'compare' => 'exists',
					),
				'first_name' => array(
					'key' => 'first_name',
					'compare' => 'exists',
				)
			),
	'orderby' => $orderby,

);
if($a['order']):
	$args['meta_query'][$a['order']] = $orderby_exists;
endif;

if($a['single']): 
	$args['title'] = $a['single']; 
endif; 
 
if($a['list']): 
	$post_titles = explode(",",$a['list']); 
	$args['post_name__in'] = $post_titles; 
	unset($args['orderby']); 
	$args['order'] = 'ASC'; 
	$args['orderby'] = 'post_name__in'; 
endif; 

// query
$the_query = new WP_Query( $args );
$the_query;

//if leader exists, move them to the front of the query
if($a['leader']):
   $leader_id = get_page_by_title ( $a['leader'], OBJECT, 'person');
   $leader_index = array_search($leader_id->ID, wp_list_pluck($the_query->posts, 'ID'), true);
   $leader_post = $the_query->posts[$leader_index];
   unset($the_query->posts[$leader_index]);
   array_unshift($the_query->posts, $leader_post);
endif;
?>
<?php if( $the_query->have_posts() ):
	$i = 0;
	$out .= '<ul class="flex-container">';
	while( $the_query->have_posts() ) : $the_query->the_post();
					if($i==0 and get_field('section_chief')==1 and $a['section']):
						$sc = 1;
						$sectionchiefstyle = '-section-chief';
					elseif($i==0 and $a['leader']):
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
					//if person has a suffix, get the suffix.
					$suffix = "";
					if (get_field('suffix')):
						$suffix = ', ' . get_field('suffix');
					endif;
					
					$middleName = "";
					if (get_field('middle_name')):
						$middleName = ' ' . get_field('middle_name');
					endif;
					
					
					
					if($a['top_label']){
						$top_field = get_field_object($a['top_label']);
						$out .= "<div style='height:110px'><h4>".$top_field['value']."</h4>";
						$out .= '<div style="bottom:0px"><h4 style="margin-top:5px;margin-bottom:0px;color:black;">'.get_field( 'first_name' ).$middleName.' '.get_field( 'last_name' ). $suffix .'</h4></div></div>';
					}
					
					$image = get_field('picture');
					if($image):
						$size = 'medium';
						$mugshot = $image['sizes'][$size];
						$out .= '<div class="person-image" style="clear:right;"><img class="person-image" loading="lazy" style="" src="'.$mugshot.'""></div>';
					endif;
					$out .= '<div class="person-text'. $sectionchiefstyle. '">';
					
					if(!($a['top_label'])){
						$out .= '<h3 style="margin-top:5px;margin-bottom:0px;color:black;">'.get_field( 'first_name' ).$middleName.' '.get_field( 'last_name' ). $suffix .'</h3>';
					}
					
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
								$out .= '<span>'.$value . '</span><br>';
							else:
								$out .= '<b>'.$item['label'].': </b>' . $value . '<br>';
							endif;
						endif;
					}
					if($a['email'] == true):
						$email = get_the_title() . '@uw.edu';
						$out .='<a href="mailto:' . $email .'">'.$email.'</a>';
					endif;					
					$out .= '<br></p>';
					
					if($a['no_button'] == false):
						$buttonshortcode = do_shortcode('[button color=purple type=small=true url="'.get_permalink().'"]View Full Profile[/button]');
						if($sc==1):
							$out .= '<div class="person-more">'. $buttonshortcode . '</div></div>';
						endif;
						
						if($sc==0):
							$out .= '</div><div class="person-more">'. $buttonshortcode . '</div>';
						endif;
					endif;
					
					
					//Button to section people page
					if($a['section_button'] == 'true'){
						$sections = get_field('section');
						foreach($sections as $section){
							$sectionshortcode = do_shortcode('[button color=gold type=small=true url="/radiology-personnel/?clinical_section='.$section.'"]View Section[/button]');
							$out .= '<div class="person-more">'. $sectionshortcode . '</div>';
						}
					}
					
					//Button to fellowship/residency page
					if($a['education_button'] == 'true'){
						$edu_button_url = get_field('education_program_link');
						if($edu_button_url){
							$edu_button_code = do_shortcode('[button color=gold type=small=true url="'.$edu_button_url.'"]View Program[/button]');
							$out .= '<div class="person-more">'. $edu_button_code . '</div>';
						}
					}
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