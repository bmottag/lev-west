<script type="text/javascript" src="<?php echo base_url("assets/js/validate/admin/shop.js"); ?>"></script>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="exampleModalLabel">Shop
		<br><small>Add Shop</small>
	</h4>
</div>

<div class="modal-body">
	<form name="form" id="form" role="form" method="post">
		<input type="hidden" id="hddId" name="hddId" value="<?php echo $idMaterial; ?>" />

		<?php if ($shopList) { ?>
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group text-left">
						<label class="control-label" for="shop">Shop: *</label>
						<select name="id_shop" id="id_shop" class="form-control">
							<option value=''>Select...</option>
							<?php for ($i = 0; $i < count($shopList); $i++) { ?>
								<option value="<?php echo $shopList[$i]["id_shop"]; ?>"> <?php echo $shopList[$i]["shop_name"]; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		<?php } ?>

		<div id="div_shop_detail" style="display:inline">
			<hr>
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group text-left">
						<label class="control-label" for="shop_name">Shop Name: *</label>
						<input type="text" id="shop_name" name="shop_name" class="form-control" placeholder="Shop Name">
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group text-left">
						<label class="control-label" for="shop_contact">Shop Contact: *</label>
						<input type="text" id="shop_contact" name="shop_contact" class="form-control" placeholder="Shop Contact">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group text-left">
						<label class="control-label" for="shop_address">Shop Address: *</label>
						<input type="text" id="shop_address" name="shop_address" class="form-control" placeholder="Shop Address">
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-group text-left">
						<label class="control-label" for="mobile_number">Shop Mobile Number: *</label>
						<input type="text" id="mobile_number" name="mobile_number" class="form-control" placeholder="Shop Mobile Number">
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-6">
					<div class="form-group text-left">
						<label class="control-label" for="shop_email">Shop Email: *</label>
						<input type="email" id="shop_email" name="shop_email" class="form-control" placeholder="Shop Email">
					</div>
				</div>
			</div>
			<hr>
		</div>

		<div class="form-group">
			<div class="row" align="center">
				<div style="width:50%;" align="center">
					<button type="button" id="btnSubmit" name="btnSubmit" class="btn btn-primary">
						Save <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true">
					</button>
				</div>
			</div>
		</div>

	</form>
</div>