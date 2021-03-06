<?php

	$title = array(
			"name" => "title",
			"id" => "title",	
			"type" => "text",
			"class" => "form-control",
			"required" => "required"
	);

	$year = array(
		"name" => "year",
		"id" => "year",	
		"type" => "text",
		"class" => "form-control",
	);

	$typeValue = array();
	$subtypeValue = array();
	$projectValue = array();

	$periodic = array(
		"name" => "periodic",
		"id" => "periodic",	
		"type" => "text",
		"class" => "form-control",
	);

	$identifier = array(
		"name" => "identifier",
		"id" => "identifier",	
		"type" => "text",
		"class" => "form-control",
	);			
	
	$qualis = array(
		"name" => "qualis",
		"id" => "qualis",	
		"type" => "text",
		"class" => "form-control",
		"readonly" => "readonly"
	);	
		
?>

<div id="addIntellectualProductionForm" class="collapse">

	<h4> Nova Produção Intelectual</h4>
	<hr>
	<div class="row">

		<div class="col-lg-10">
			<?= form_open("save_production") ?>
				<div class="header"></div>
				<?php include '_intellectual_production_form.php'; ?>

			<?= form_close() ?>
			<br><br><br>

		</div>
	</div>
</div>

