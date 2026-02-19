<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	
    <title>POSEIDON</title>

    <!-- Bootstrap Core CSS -->
	<link href="<?php echo base_url("assets/bootstrap/vendor/bootstrap/css/bootstrap.min.css"); ?>" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo base_url("assets/bootstrap/vendor/metisMenu/metisMenu.min.css"); ?>" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url("assets/bootstrap/dist/css/sb-admin-2.css"); ?>" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="http://vci-app.thibot.com/assets/bootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
	<br>
	
	<div class="well">   
		<a class="btn btn-success" href="<?php echo base_url() . 'dashboard/'; ?>"><span class="glyphicon glyphicon-hand-left" aria-hidden="true"></span> Go back </a>
	</div>
	<!-------- Barra de progreso -------->
		
	<!-------- Barra de progreso -------->
        <div class="row">


				
				
          <!-- Start of Reportico Report -->
          <?php
            set_include_path('{http://localhost/vci_app/assets/reportico/run.php?project=admin&execute_mode=ADMIN&clear_session=1}');
            require_once('./././././assets/reportico/reportico.php'); 
            $a = new reportico();
            $a->embedded_report = true;
            $a->forward_url_get_parameters = "x1=y1&x2=y2"; 
            $a->execute();
          ?> 
          <!-- End of Reportico Report -->
				

        </div>
    </div>



</body>

</html>
