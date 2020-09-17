    <header id="header" role="banner">
        <div class="container">
            <div id="navbar" class="navbar navbar-default">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?=base_url();?><?=($this->session->userdata('log'))?'index.php/dashboard/'.$this->session->userdata('user_id'):''?>">
                        <?php if (file_exists('./logo.png')) { ?>
                            <img src="<?=base_url();?>assets/images/logo.png" width="" height="61">
                        <?php }else{ 
                            echo ($brand_name)?$brand_name:'MinorSchool';
                        } ?>
                        </a> <!-- Brand Title -->
                        
                        
                </div>
                <div class="collapse navbar-collapse">
                    <div class="headertopright">
                    	<div class="headernav">
                        	<div class="headerinfo">
                            	<img src="<?=base_url();?>assets/images/hdrmail-icn.png" />
                                	<a>info@webevertech.com</a> 
                                <img src="<?=base_url();?>assets/images/hdrphone-icn.png" />
                                +91 98253 65423
                            </div>
                        	<div class="headernav">
                            	<ul class="nav navbar-nav">
                        <li class="<?=($this->uri->segment(1) == '')?'active':''; ?>"><a href="<?=base_url('index.php');?>"><i class="fa fa-home"></i></a></li>
                        <li class="<?=($this->uri->segment(1) == 'course')?'active':''; ?>"><a href="<?=base_url('index.php/course');?>">Courses</a></li>
                        <li class="<?=($this->uri->segment(1) == 'exam_control')?'active':''; ?>"><a href="<?=base_url('index.php/exam_control/view_all_mocks');?>">Exams</a></li>
                        <li class="<?=($this->uri->segment(2) == 'pricing')?'active':''; ?>"><a href="<?=base_url('index.php/guest/pricing');?>">Pricing</a></li>
                        <li class="<?=($this->uri->segment(1) == 'blog')?'active':''; ?>"><a href="<?=base_url('index.php/blog');?>">Blog</a></li>
                        
                        <?php if ($this->session->userdata('log')) { ?>
                            <li class="<?=($this->uri->segment(1) == 'noticeboard')?'active':''; ?>"><a href="<?=base_url('index.php/noticeboard/notices');?>">Noticeboard</a></li>
                            <li class="<?=($this->uri->segment(2) == 'view_faqs')?'active':''; ?>"><a href="<?=base_url('index.php/guest/view_faqs');?>">FAQ</a></li>
                        <?php }else{ ?>
                        <?php } ?>
                        
                        
                        
						
                    </ul>
                    		</div>   
                        </div>
                        <div class="headeruser">
                        <?php if ($this->session->userdata('log')) { ?>
                            <li><a href="<?=base_url('index.php/login_control/logout'); ?>">
                            <img src="<?=base_url();?>assets/images/logout-icn.png"></a><br />
                            Logout
							</li>
                        <?php }else{ ?>
                            <li><a href="<?=base_url('index.php/admin');?>">
                            <img src="<?=base_url();?>assets/images/login-icn.png"></a><br />
                            Login
							</li>
                        <?php } ?>
                        </div>
                        <div class="clear"></div>
                    </div>

                    
                    
                     
                    
                </div>
            </div>
        </div>
    </header><!--/#header-->
    <?php   //   echo "<pre/>"; print_r($this->uri->segment(1)); exit();    ?>