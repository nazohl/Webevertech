<?php
$mailstatus = $_GET['message'];
?>








<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Webever Technologies</title>
<link href="style/style.css" rel="stylesheet" type="text/css" />
<link href="style/dropdown.css" rel="stylesheet" type="text/css" />
<link href="style/media.css" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/png" href="favicon.png">
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-33560238-1', 'auto');
  ga('send', 'pageview');

</script>
</head>
<body>
<header id="top-container">
  <div class="top-strip">
    <div class="fix-content">
      <div class="topstrip-l">Web & Mobile Application Development, Ecommerce & Content Management System, Branding & Graphic Design, Digital Marketing Services</div>
      <div class="loginbtn"><a href="http://webever.co.in/" target="_blank">Login</a></div>
      <div class="topstrip-r"><a href="https://www.facebook.com/webevertech/" target="_blank">Facebook</a><a href="https://twitter.com/webevertech" target="_blank">Twitter</a></div>
      <div class="clear"></div>
    </div>
  </div>
  <div class="fix-content">
  <div class="logo-navigation">
            <div class="logo">
              <a href="index.html"><img alt="" src="images/logo.png" /></a>
            </div>
            <a href="https://www.iitnj.org/">
              <span style="position: relative; top: 40px">Powered by </span>
              <div
                style="
                  position: relative;
                  left: 325px;
                  top: -10px;
                  height: 75px;
                  width: 75px;
                  border-radius: 50%;
                  background-image: url('https://iitnjnew.b-cdn.net/wp-content/uploads/2019/09/01.png');
                  background-size: 60%;
                  background-repeat: no-repeat;
                  background-position: center;
                "
              ></div
            ></a>
            <div class="topright">
              <!--<!--<div class="topsearch">
            <input name="" type="text" placeholder="Search">
            <a href="#" class="sprites search-ico"></a></div>-->
              <div class="topsmallnav">
                <ul>
                  <!-- <li><a href="inquiry.html">Inquire Now</a></li> -->

                  <!-- <li><a href="sitemap.html">Sitemap</a></li>
                  <li><a href="careers.html">Careers</a></li> -->
                </ul>
                <div class="clear"></div>
              </div>
              <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <nav id="topnavi">
              <ul>
                <li><a href="index.html" class="active">Home</a></li>
                <li>
                  <a href="company-overview.html">About Company</a>
                  <!-- First Tier Drop Down -->
                  <ul>
                    <li>
                      <a href="company-overview.html">Company Overview</a>
                    </li>
                    <li><a href="why-us.html">Why Us</a></li>
                    <li>
                      <a href="business-charter.html">Our Business Charter</a>
                    </li>
                    <li><a href="careers.html">Careers</a></li>
                  </ul>
                </li>
                <li>
                  <a href="web-application-development.html">Services</a>
                  <!-- First Tier Drop Down -->
                  <ul>
                    <li>
                      <a href="web-application-development.html"
                        >Web & Mobile Application Development</a
                      >
                    </li>
                    <li>
                      <a href="shopping-cart-development.html"
                        >Ecommerce & Content Management System</a
                      >
                    </li>
                    <li>
                      <a href="branding-and-graphic-design.html"
                        >Branding & Graphic Design</a
                      >
                    </li>
                    <!-- <li>
                      <a href="digital-marketing-services.html"
                        >Digital Marketing Services</a
                      >
                    </li> -->
                    <li>
                      <a href="software-product-development.html"
                        >Software Product Development</a
                      >
                    </li>
                    <li><a href="technologies.html">Technologies</a></li>
                  </ul>
                </li>
                <li><a href="industries.html">Industries</a></li>
                <li><a href="clients-1.html">Clients</a></li>
                <li>
                  <a href="#">Our Work</a>
                  <ul>
                    <li>
                      <a href="branding-consultant.html">Branding Consultant</a>
                    </li>
                    <li><a href="work.html">Work</a></li>
                  </ul>
                </li>
                <li>
                  <a href="inquiry.html">Inquiry</a>
                  <ul>
                    <li><a href="outsourcing.html">Outsourcing</a></li>
                  </ul>
                </li>
                <li><a href="hire-resource.html">Hire Resource</a></li>
                <li><a href="contact.html">Contact</a></li>
                <!-- <li><a href="blog.html" target="_blank">Blog</a></li> -->
              </ul>
            </nav>
          </div>

  </div>
</header>
<div class="about-banner">
  <div class="banner-title">
    <h1>Inquiry</h1>
  </div>
</div>
<div class="container">
  <div class="breadcrumb"><a href="index.html">Home</a>><span>Inquiry</span></div>
  
  
  
                             
   
  <div class="inquiry-title">
     
     
<?php if ($mailstatus == '1') { ?>
                          <h1 class="sucesstitle">Thank You!, for showing your interest in our service, Your details have been received sucessfully !</h1>  
                        <?php } else { ?>
                           <h2 class="sucesstitle">Due to some error your message could not be sent, Please <a href="inquiry.html">click here</a> to try again!</h2>
                        <?php } ?>
     
    <div class="clear"></div>
  </div>
   
   
</div>
<div class="footer-area">
  <div class="footer">
    <div class="footer-contain">
      <div class="footer-menu">
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="company-overview.html">About Company</a></li>
          <li><a href="web-application-development.html">Services</a></li>
          <li><a href="technologies.html">Technologies</a></li>
          <li><a href="clients-1.html">Clients</a></li>
          <li><a href="work.html">Work</a></li>
          <li><a href="industries.html">Industries</a></li>
          <li><a href="hire-resource.html">Hire Dadicated Resource</a></li>
          <li><a href="inquiry.html">Inquiry</a></li>
          <li><a href="contact.html">Contact Us</a></li>
        </ul>
      </div>
      <div class="copyright"> &copy; 2016, Copyright All Rights Reserved By <a href="http://www.webever.co">Webever Technologies</a>
        <div class="bottpart-r"> <a href="https://www.facebook.com/webevertech/" target="_blank" class="social-ico1"></a><a href="https://twitter.com/webevertech" target="_blank" class="social-ico2"></a> </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>