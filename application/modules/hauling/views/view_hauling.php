<div id="page-wrapper">
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4 class="list-group-item-heading">
						<i class="fa fa-edit fa-fw"></i> RECORD TASK(S)
					</h4>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-truck"></i> HAULING - CLOSE
				</div>
				<div class="panel-body">

					<div class="row">
						<div class="col-lg-6">
							<div class="alert alert-info">

								<strong>Company: </strong><?php echo $information ? $information["company_name"] : ""; ?>
								<br><strong>Truck: </strong><?php echo $information ? $information["unit_number"] : ""; ?>
								<br><strong>Truck Type: </strong><?php echo $information ? $information["truck_type"] : ""; ?>
								<br><strong>Material Type: </strong><?php echo $information ? $information["material"] : ""; ?>
								<br><strong>Job Code/Name: </strong><?php echo $information ? $information["from"] : ""; ?>
								<br><strong>To Site: </strong><?php echo $information ? $information["to"] : ""; ?>
								<br><strong>Time In: </strong><?php echo $information ? $information["time_in"] : ""; ?>
								<br><strong>Payment: </strong><?php echo $information ? $information["payment"] : ""; ?>
								<br><strong>Comments: </strong><?php echo $information ? $information["comments"] : ""; ?>

							</div>
						</div>

						<div class="col-lg-3">
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:80%;" align="center">
										<?php
										if ($information["contractor_signature"]) {
										?>
											<img src="<?php echo base_url($information["contractor_signature"]); ?>" class="img-rounded" alt="Signature" width="204" height="136" />
											<br><strong>Subcontractor Signature</strong><br>
										<?php } ?>

									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="form-group">
								<div class="row" align="center">
									<div style="width:80%;" align="center">

										<?php
										if ($information["vci_signature"]) {
										?>
											<img src="<?php echo base_url($information["vci_signature"]); ?>" class="img-rounded" alt="Signature" width="204" height="136" />
											<br><strong>V-Contracting Signature</strong>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>