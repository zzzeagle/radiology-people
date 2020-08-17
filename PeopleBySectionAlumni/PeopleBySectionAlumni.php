<?php
/**
* Plugin Name: Radiology Alumni
* Description: This plugin will be used to list alumni by section
* Version: 0.0.1 
* Author: Zachary Eagle
* Last Revision:2018.08.13
*/
function list_fellow_alumni( $atts ){
	$a = shortcode_atts( array(
		'program' => '',
	), $atts );

$cYear = date("Y");
$cYear5 = $cYear - 5;
$html = "";


while ($cYear > $cYear5) {

	$args = array(
		'numberposts'	=> -1,
		'post_type'	=> 'person',
		'order' => 'ASC',
		'orderby' => 'meta_value',
		'meta_key' => 'last_name',
		'meta_query' => array(
			'relation' => 'AND',
					array(
							'key' => 'uw_fellowship_alumni_program',
							'value' => $a[ 'program' ],
							'compare' => 'LIKE',
						),
					array(
						'key' => 'graduation_year',
						'value' => $cYear,
						'compare' => '=',
				), 
		),
		
	);

	// query
	$the_query = new WP_Query( $args );
	$the_query;
	$i = 0;
	if( $the_query->have_posts() ):
		$cYear1 = $cYear -1;
		$html .= '<h3>'.$cYear1."-".$cYear.'</h3>';
		$html .= '<table>';
		while( $the_query->have_posts() ) : $the_query->the_post();
		if( !$i % 2 ){
			$html .= "<tr>";
		}
		$post = $my_query->post;
		$html .= "<td width='50%'><div style='float:left; padding-right:5px;'>";
		if(get_field('picture') != ""){
			$html .= "<img src='".get_field( 'picture' )."' width='100px'></div><p class='faculty-small-listing'><a href='".get_permalink()."'>";
			$html .= get_field( 'first_name' )." ";
			$html .= get_field( 'last_name' ).", ";
			$html .= get_field( 'suffix' );
			$html .= "</a><br>";
			$html .= get_field( 'position' );
			$html .= "<br>";
			$section = get_field('section');
			$html .= implode($section);
			$html .= "</p>";
			$html .= "</td>";
		if( $i % 2 ){
			$html .= "</tr>";
		}
		$i++;}
		endwhile;
		if( $i % 2 ){
			$html .= "<td width='50%'><div style='float:left; padding-right:5px;'></td></tr>";
		}
	endif;

	wp_reset_query();
	$html.= "</table>";
	$cYear--;
	};
return $html;
}

add_shortcode( 'fellow_alumni', 'list_fellow_alumni');
