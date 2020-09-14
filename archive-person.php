<?php get_header(); ?>

<div class="container uw-body">

  <div class="row">

    <div class="col-md-12 uw-content" role='main'>

    	<h2 class="uw-site-title">Meet Our Faculty</h2>
    	
    	 <?php if (is_front_page()) { get_template_part( 'menu', 'mobile' ); }?>

      <div id='main_content' class="uw-body-copy" tabindex="-1">
      <div class="col-md-12">
      	<h2>Meet Our Faculty</h2>
      	<div class="col-md-3">
      	<ul>
      	<li><a href="/radiology-personnel">All</a></li>
		<li><a href="/about-us/new-faculty/">New Faculty</a></li>
      	</ul>
      	
      	<h4>Clinical Subspecialties</h4>
      		<ul>
      			<li><a href="./?clinical_section=abdominal%20imaging">Abdominal Imaging</a></li>
      			<li><a href="./?clinical_section=breast%20imaging">Breast Imaging</a></li>
				<li><a href="./?clinical_section=Cardiothoracic%20radiology">Cardiothoracic Radiology</a></li>
      			<li><a href="./?clinical_section=diagnostic%20physics">Diagnostic Physics</a></li>
      			<li><a href="./?clinical_section=emergency%20radiology">Emergency Radiology</a></li>
      			<li><a href="./?clinical_section=interventional%20radiology">Interventional Radiology</a></li>
      			<li><a href="./?clinical_section=musculoskeletal%20radiology">Musculoskeletal Radiology</a></li>
      			<li><a href="./?clinical_section=neuroradiology">Neuroradiology</a></li>
      			<li><a href="./?clinical_section=nuclear%20medicine">Nuclear Medicine</a></li>
      			<li><a href="./?clinical_section=pediatric%20radiology">Pediatric Radiology</a></li>
				<li><a href="./?clinical_section=VA%20Health%20Services%20Of%20Puget%20Sound">VA Health Services of Puget Sound</a></li>
      		      	
      		</ul>
     <!-- 	<h4>Research Interests</h4>-->

      		
      	</div>
      	<div class="col-md-9">
		<?php echo do_shortcode("[list-rad-people classification='faculty' fields='position,section' labels='false']");?>

	</div>
      </div>
      	
    </div>
  </div>
 
</div>
</div>

<?php get_footer(); ?>
