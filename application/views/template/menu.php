<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<?php $dashboardURL = $this->session->userdata("dashboardURL"); ?>
		<a class="navbar-brand" href="<?php echo base_url($dashboardURL); ?>"><img src="<?php echo base_url("images/logo.png"); ?>" class="img-rounded" width="87" height="32" /></a>
	</div>
	<!-- /.navbar-header -->


<!-- /.TOP MENU -->
	<ul class="nav navbar-top-links navbar-right">
<?php
		if($topMenu){
			echo $topMenu;
		}
?>
	</ul>
<!-- /.TOP MENU -->

<!-- /.LEFT MENU -->
	<div class="navbar-default sidebar" role="navigation">
		<div class="sidebar-nav navbar-collapse">
			<ul class="nav" id="side-menu">
<!--
				<li>
					<a href="#">
						<?php if($this->session->photo){ ?>
						<img src="<?php echo base_url($this->session->photo); ?>" class="img-rounded" width="26" height="26" />
						<?php }else{?>
						<i class="fa fa-child fa-fw"></i>
						<?php } ?>
						Hi <?php echo $this->session->firstname; ?>!!!<span class="fa arrow"></span>
					</a>
					<ul class="nav nav-second-level">
						<li>
							<a href="<?php echo base_url("employee/photo"); ?>">User Photo</a>
						</li>
					</ul>
				</li>
-->
				<?php
					if($leftMenu){
						echo $leftMenu;
					}
				?>
			</ul>
		</div>
		<!-- /.sidebar-collapse -->
	</div>
<!-- /.LEFT MENU -->
</nav>