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
					    <?php include(dirname(__FILE__).'/logos.php');?>

					<!--ul class="nav">
					<li class="active"><a href="#">Home</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
					</ul-->
				</div><!--/.nav-collapse -->
			</div>
<?php 
	$is_administrator = isset($_SESSION[IS_ADMINISTRATOR]) && $_SESSION[IS_ADMINISTRATOR]==1;
	if ($is_administrator) {
		$currentFile = $_SERVER["PHP_SELF"];
		$parts = explode('/', $currentFile);
		$currentFile = $parts[count($parts) - 1];
		$str = $currentFile=='admin.php'?'site':'administrator';
?>			
			<div>
				<div class="span10"></div>			
				<div class="span2">
					<form method="post" action="<?php echo $currentFile=='admin.php'?'index_instructor.php':'admin.php';?>">
						<input type="submit" name="admin" value="<?php echo Language::get($str) ?>" id="admin_submit">
					<?php if ($currentFile=='admin.php') {
						?>
						<a href="list_instances.php" data-toggle="modal" class="btn btn-info"><?php echo Language::get('List instances')?></a>

					<?php } ?>
					</form>	
				</div>
			</div>
<?php  }			?>
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
<?php
} ?>
</div>
<br>
<?php if ($is_administrator) {?><br><?php }?>
<div class="container">