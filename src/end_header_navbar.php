</head>
<body class="body-with-breadcrumbs">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<div class="nav-collapse collapse">
						  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
					  </a>
					  <a class="brand" href="#"> <img src="images/logo.png"><?php echo $course_title?></a>
					    <?php include('logos.php');?>

					<!--ul class="nav">
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
					</ul-->
				</div><!--/.nav-collapse -->
			</div>
		</div>
<?php 
	if (isset($show_breadbrumbs) && $show_breadbrumbs===true && $is_instructor) {
?>
	<ul class="breadcrumb">
	  <li>
	    <a href="index_instructor.php"><?php echo Language::get('home') ?></a> <span class="divider">></span>
	  </li>
	  <li class="active"><?php echo $InstanceId?></li>
	</ul>
<?php } ?>
</div>
<div class="container">