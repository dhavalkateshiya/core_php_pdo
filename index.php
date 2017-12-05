<link href="assets/css/font-awesome.min.css" rel="stylesheet" crossorigin="anonymous">
<?php
require_once("config.php");


$category	= new category();	
$cat			= $category->getCategory();
$p_categories	= $category->getParentCategory();

// echo "<pre>";
// print_r($p_cat);
// echo "</pre>";

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Bootstrap Example</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">
			<h2>Panels with Contextual Classes</h2>
			<div class="panel-group">
				<?php foreach($p_categories as $p_category):?>
				<div class="panel panel-primary">
					<div class="panel-heading"><?php echo $p_category['cat_name'] ?></div>
					<div class="panel-body">
						<?php $c_categories	= $category->getChildCategory(','.$p_category['cat_id'].','); ?>	
						
						<?php 
							echo "<pre>";
							print_r($c_categories); 
							echo "<pre>";
						?>	
						
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>
	</body>
</html>