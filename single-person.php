<?php get_header(); ?>

<div class="container uw-body">

  <div class="row">

    <div class="col-md-12 uw-content" role='main'>

    	<h2 class="uw-site-title"><?php get_uw_post_title();?></h2>
    	
    	 <?php if (is_front_page()) { get_template_part( 'menu', 'mobile' ); }?>

      <div id='main_content' class="uw-body-copy" tabindex="-1">
      <div class="col-md-3 uw-content">
      		<?php if(get_field( 'picture' )):
				$size = 'medium';
				$image = get_field('picture');
				$medImage = $image['sizes'][$size];
			?>
			<img src="<?php echo $medImage;?>">
		<?php endif; ?>
		<?php if(get_field( 'cv' )):?>
			<?php $cv =  get_field('cv')?>
			<h3><a href="<?php echo $cv['url']; ?>" target="_blank"><?php the_field( 'first_name' );?> <?php the_field( 'last_name' );?>'s CV</a></h3>
		<?php endif; ?>
		<?php if(get_field( 'biosketch' )):?>
			<?php $biosketch =  get_field('biosketch')?>
			<h3><a href="<?php echo $cv['url']; ?>" target="_blank"><?php the_field( 'first_name' );?> <?php the_field( 'last_name' );?>'s Biosketch</a></h3>
		<?php endif; ?>
		<?php
		$hyphName = get_field("first_name")."-".get_field("last_name");
		$url = 'https://www.uwmedicine.org/bios/'.$hyphName;
		//if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
		//	echo'Not a valid URL';
		//}
		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		
		if (strpos($resp,'Page Not Found') == false) {
			echo "<h3><a href='".$url."'>UW Medicine Biography</a></h3>";
    		}
			// Close request to clear up some resources
		curl_close($curl);
		?>
		<!--SCCA Bio Link-->
		<?php if(get_field('scca_bio_url')):?>
			<h3><a href="<?php echo get_field('scca_bio_url');?>" target="_blank">SCCA Biography</a></h3>
		<?php endif;?>
		<?php
		/*SCH With OUT Middle Initial
		$hyphNameWMI = get_field("first_name")."-".get_field("middle_initial")."-".get_field("last_name");
		$url = 'http://www.seattlechildrens.org/medical-staff/'.$hyphNameWMI.'/';
		
		$file_headers = @get_headers($url);
		if($file_headers[0] == 'HTTP/1.1 301 Moved Permanently') {
		    $exists = false;
		}
		else {
		    $exists = true;
		  echo "<h3><a href='".$url."'>Seattle Children's Biography</a></h3>";
		}

		*/
		?>
		<br>
		<?php 
		$classification = get_field('classification');
		$isresident = False;
		if(!(in_array('IR Resident', $classification)) && (in_array('Resident - R2', $classification) || in_array('Resident - R3', $classification)|| in_array('Resident - R4', $classification)|| in_array('Resident - R5', $classification))){
			$isresident = True;
			echo '<h4><a href="/education/radiology-residency/meet-our-residents/">See All Radiology Residents</a></h4>';
		}
		if(in_array('IR Resident', $classification)){
			$isresident = True;
			echo '<h4><a href="/education/interventional-radiology-programs/meet-residents-fellows/">See All IR Residents</a></h4>';
		}?>
		<h4><a href="/radiology-personnel">See All Radiology Faculty</a></h4>
		
	</div>
	<div class="col-md-9 uw-content bio-content">
        <h1 style="margin-top:0px;"><?php the_field( 'first_name' )?><?php if(get_field('middle_name')):echo " "; the_field( 'middle_name' );endif;?> <?php  the_field( 'last_name' );?><?php if(get_field('suffix')): echo ', '; the_field( 'suffix' );endif;?></h1>
        <?php the_field( 'position' ) ?><br>
        <?php
		if (!$isresident):
			echo the_field('section') . '<br>';
		endif;
		
		
		if(get_field( 'twitter' )):
        	echo '<br><a class="twitter-follow-button" href="https://twitter.com/';
        	echo the_field('twitter');
        	echo '" data-size="large" data-show-count="false">Follow @';
        	echo the_field('twitter');
        	echo '</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
        endif;
		##Contact form
		##Commented out 9/9/2022 due to spam
		#echo do_shortcode('[su_accordion class=""rad-accordion][su_spoiler title="Contact Me" open="no" style="default" icon="plus" anchor="" anchor_in_url="no" class="rad-accordion"][wpforms id="27567" title="false"][/su_spoiler][/su_accordion]');
		?>
        <?php if(get_field( 'biography' )):
        	echo "<h3>Biography</h3>";
        	the_field( 'biography' );
        endif; ?>
        <?php if(get_field( 'research_interests' )):
        	echo "<h3>Research Interests</h3>";
        	the_field( 'research_interests' );
        endif; ?>
        <?php if(get_field( 'education' )):
        echo "<h3>Education</h3>";
        the_field( 'education' );
        endif; ?>
		<?php while ( have_posts() ) : the_post(); ?>
		<?php 

		/*
		*  Query posts for a relationship value.
		*  This method uses the meta_query LIKE to match the string "123" to the database value a:1:{i:0;s:3:"123";} (serialized array)
		*/
		$news = get_posts(array(
			'post_type' => 'news',
			'category_name' => 'Awards',
			'orderby'	=> 'meta_value',
			'meta_key'	=> 'date_received',
			'order'		=> 'DESC',
			'meta_query' => array(
				array(
					'key' => 'recipients', // name of custom field
					'value' => '"' . get_the_ID() . '"', // matches exaclty "123", not just 123. This prevents a match for "1234"
					'compare' => 'LIKE',
				)
			)
		));

		?>
		<?php if( $news ): ?>
		<h3>Awards and Honors</h3>
			<ul>
			<?php foreach( $news as $new ): ?>
			<?php 
			$awardDate = strtotime(get_field('date_received', $new->ID));
			$formattedDate = date('F d, Y', $awardDate);
			?>
				<li>
					<a href="<?php echo get_permalink( $new->ID );?>"><?php echo get_field('award_title', $new->ID);?></a><?php echo ", " . $formattedDate;?>
				</li>
			<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php endwhile; // end of the loop. ?>
		
		<!--Scopus Publications section-->
		<?php while ( have_posts() ) : the_post(); ?>
		
		<!--Resident Profile Fields-->
		<?php
		#if (in_category( 'Residency' )):
		if ($isresident):
			$out = '';
			$fields = ["medical_school",
						"undergraduate_school",
						"intern_program",
						"looking_for_in_residency",
						"uw_strengths",
						"advice",
						"hobbies",
						"clinical_interests",
						"honors_and_awards",
						"extracurricular"];
			$out .= '<p style="overflow:hidden">';
			foreach ($fields as $field){
				$item = get_field_object($field);
				if($item['value']):
					$out .= '<b>'.$item['label'].': </b>' . $item['value'] . '<br>';
				endif;
			}
			if(get_field( 'fun_picture' )):
				$out .= '<div style="display:block; clear:both;"><img style="padding-top:15px;" src="'.get_field( 'fun_picture' ).'"></div>';
			endif;
			if(get_field( 'caption_for_fun_picture' )):
				$out .= get_field( 'caption_for_fun_picture' );
			endif;
			echo $out;
		endif;

		?>	
	
		<?php
		echo do_shortcode('[list-rad-publications-person person_id="'.get_the_ID().'"]');

		endwhile; // end of the loop. ?>

        
        <?php the_tags()?><br>
		<?php 
        /*if(get_field('scopus_author_id')){
        	$authorID = get_field('scopus_author_id');
        	$scopusSC = '[GetScopusPubs AuthorID="'.$authorID.'"]';
        	echo apply_filters( 'the_content',$scopusSC);
        }else{
        $pubmedname = get_field( 'pubmed_name' );
        $fullname = get_field( 'first_name' ).' '.get_field('last_name');
        $uwAffiliation = get_field( 'pubmed_no_uw_affiliation' );
        $sc = '[pubmed person="'.$pubmedname.'" fullname="'.$fullname.'" uwa="'.$uwAffiliation.'"]';
        echo apply_filters( 'the_content',$sc);
        }
        */?>
        </div>

      </div>
      	
    </div>
  </div>
 
</div>

<?php get_footer(); ?>