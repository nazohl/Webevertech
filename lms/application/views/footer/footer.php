<div class="footerarea">
	<div class="container">
    	<div class="footerleft footerletcontain">
        	<h1><span>Now </span><br />
            Learning. Anywhere. Anytime</h1>
			<p>Dignissim ligula condimentum Suspendisse augue ipsum, venenatis molestie<br /> 
            fringilla Nam sagittis Vestibulum ultrices ante justo gravida molestie massa<br /> 
            gravida.</p>
        </div>
    	<div class="footerright footerrightcontsec">
            <ul class="footerrightcont">
                <li><img src="<?=base_url();?>assets/images/cloud-img.png" />
                <p><span>100% Cloud Based</span> <br />
                Explore over 45,000 courses taught by expert instructors</p></li>
            </ul>
            <ul class="footerrightcont">
                <li><img src="<?=base_url();?>assets/images/mobile-img.png" />
                <p><span>Mobile Learning</span> <br />
                Explore over 45,000 courses taught by expert instructors</p></li>
            </ul>
            <ul class="footerrightcont">
                <li><img src="<?=base_url();?>assets/images/globalpresence-img.png" />
                <p><span>Strong Global Presence</span> <br />
                Explore over 45,000 courses taught by expert instructors</p></li>
            </ul>
        </div>
    </div>
    
    <div class="footershortlinks">
    	<div class="container">
        	<div class="footerleft footericons">
            	<a href="#"><img src="<?=base_url();?>assets/images/fb-icn.png" /></a>
				<a href="#"><img src="<?=base_url();?>assets/images/linkdin-icn.png" /></a>
                <a href="#"><img src="<?=base_url();?>assets/images/youtube-icn.png" /></a>
            </div>
            <div class="footerright footerlinks">
                    <a href="<?=base_url('index.php');?>">Home</a>
                    <a href="<?=base_url('index.php/course');?>">Courses</a>
                    <a href="<?=base_url('index.php/exam_control/view_all_mocks');?>">Exams</a>
                    <a href="<?=base_url('index.php/guest/pricing');?>">Pricing</a>
                    <a href="<?=base_url('index.php/blog');?>">Blog</a>
            </div>
        </div>
    </div>
    <div class="container copyrightarea">
    	<div class="footerleft"><a href="#">Terms and Conditions</a> | <a href="#">Privacy Policy</a></div>
        <div class="footerright">Copyright &copy; 2017 Ideal Education</div>
    </div>
</div> 

<!-- Modal Start -->
<?php if (isset($modal)) echo $modal; ?>
<div id="fade" class="black_overlay"></div> 
<!-- Common Scripts-->
<script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
<!-- Custom JS  -->
<script src="<?php echo base_url('assets/js/jsscript.js') ?>"></script>