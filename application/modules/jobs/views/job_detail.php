<div id="page-wrapper">

    <br>		
    <!-- /.row -->
    <div class="row">
			
        <div class="col-lg-3">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> <strong>JOB DETAILS</strong>
                </div>
                <!-- /.panel-heading -->

                <?php 

echo $jobInfo[0]['job_description'];
                    $noWorkOrder= 0;
                    if($workOrderInfo){
                            $noWorkOrder = count($workOrderInfo);
                    }

                ?>

                <div class="panel-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item">
                            <p class="text-success"><i class="fa fa-money fa-fw"></i><strong> Work Orders Records</strong>
                                <span class="pull-right text-muted small"><em><?php echo $noWorkOrder ; ?></em>
                                </span>
                            </p>
                        </a>
                    </div>
                    <!-- /.list-group -->

                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->


        
            <div class="list-group">
                <a href="<?php echo base_url('jobs/tool_box/' . $jobInfo[0]['id_job']); ?>" class="btn btn-warning btn-block">
                    <i class="fa fa-tag"></i> IHSR
                </a>
                <a href="<?php echo base_url('jobs/erp/' . $jobInfo[0]['id_job']); ?>" class="btn btn-success btn-block">
                    <i class="fa fa-tags"></i> ERP
                </a>
                <a href="<?php echo base_url('jobs/hazards/' . $jobInfo[0]['id_job']); ?>" class="btn btn-danger btn-block">
                    <i class="fa fa-photo"></i> JHA
                </a>
                <a href="<?php echo base_url('jobs/safety/' . $jobInfo[0]['id_job']); ?>" class="btn btn-default btn-block">
                    <i class="fa fa-thumb-tack"></i> FLHA
                </a>
                <a href="<?php echo base_url('jobs/jso/' . $jobInfo[0]['id_job']); ?>" class="btn btn-info btn-block">
                    <i class="fa fa-tint"></i> JSO
                </a>
                <a href="<?php echo base_url('more/environmental/' . $jobInfo[0]['id_job']); ?>" class="btn btn-purpura btn-block">
                    <i class="fa fa-tags"></i> ESI
                </a>
                <a href="<?php echo base_url('jobs/locates/' . $jobInfo[0]['id_job']); ?>" class="btn btn-primary btn-block">
                    <i class="fa fa-photo"></i> Locates
                </a>
                <a href="<?php echo base_url('more/confined/' . $jobInfo[0]['id_job']); ?>" class="btn btn-warning btn-block">
                    <i class="fa fa-thumb-tack"></i> CSEP
                </a>
                <a href="<?php echo base_url('more/task_control/' . $jobInfo[0]['id_job']); ?>" class="btn btn-success btn-block">
                    <i class="fa fa-tint"></i> COVID
                </a>
            </div>

        </div>
        <!-- /.col-lg-4 -->

        <div class="col-lg-9">

        </div>
    </div>

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
<?php
    if($workOrderInfo){ 
?>  
            <div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-money fa-fw"></i> <strong>WORK ORDER RECORDS</strong>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataPlanning">
                        <thead>
                            <tr>
                                <th class='text-center'>Work Order #</th>
                                <th class='text-center'>Job Code/Name</th>
                                <th class='text-center'>Task Description</th>
                                <th class='text-center'>Last Message</th>
                            </tr>
                        </thead>
                        <tbody>                         
                        <?php
                            foreach ($workOrderInfo as $lista):
                                switch ($lista['state']) {
                                        case 0:
                                                $valor = 'On Field';
                                                $clase = "text-danger";
                                                $icono = "fa-thumb-tack";
                                                break;
                                        case 1:
                                                $valor = 'In Progress';
                                                $clase = "text-warning";
                                                $icono = "fa-refresh";
                                                break;
                                        case 2:
                                                $valor = 'Revised';
                                                $clase = "text-primary";
                                                $icono = "fa-check";
                                                break;
                                        case 3:
                                                $valor = 'Send to the Client';
                                                $clase = "text-success";
                                                $icono = "fa-envelope-o";
                                                break;
                                        case 4:
                                                $valor = 'Closed';
                                                $clase = "text-danger";
                                                $icono = "fa-power-off";
                                                break;
                                }

                                echo "<tr>";
                                echo "<td class='text-center'>" . $lista['id_workorder'];
                                echo '<p class="' . $clase . '"><i class="fa ' . $icono . ' fa-fw"></i>' . $valor . '</p>';
                                echo "</td>";
                                echo "<td class='text-center'>" . $lista['job_description'] . "</td>";
                                echo "<td class='text-center'>" . $lista['observation'] . "</td>";
                                echo "<td class='text-center'>" . $lista['last_message'] . "</td>";
                                echo "</tr>";
                            endforeach;
                        ?>
                        </tbody>
                    </table>                    
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
<?php   } ?>

		</div>
    </div>		

    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">






        </div>
    </div>      

</div>
<!-- /#page-wrapper -->

<!-- Tables -->
<script>
$(document).ready(function() {
    $('#dataTables').DataTable({
        responsive: true,
		 "ordering": false,
		 paging: false,
		"searching": false,
		"pageLength": 25
    });
	
	
});
</script>