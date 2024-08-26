
<?php $__env->startSection('content'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


    <?php if(Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant')): ?>
        <style>
            #rcorners1 {
                border-radius: 25px;

            }
            /* .col-sm-6{
    border:2px solid black;
} */
        </style>
        <?php
            $first_name = App\Http\Controllers\UserController::username();
            $logo = App\Http\Controllers\UserController::logo();
        ?>
      
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
          
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($total_no_of_parts); ?></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">Total Number of Items in Stock</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('items_list_site')); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e('$ ' . $total_cost_of_parts); ?></h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">Total Value of Items in Stock</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo e(route('items.index')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #19AF9D" class="small-box" id="rcorners1">
                        <div class="inner">
                            <h4 style="color: white; font-family: 'Segoe UI Light';">
                                  <?php echo e('$ ' . $total_cost_of_parts_within_the_month); ?>

                               </h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">
                                Total Value of Items Issued MTD</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="<?php echo e(route('stores.supply_history')); ?>" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box " id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">
                                   <?php echo e('$ ' . $total_value_items_received_mtd); ?>

                                </h4>


                            <p style="color: white; font-family: 'Segoe UI Light';">Total Value of Items Received MTD</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="<?php echo e(route('inventories.inventory_item_history')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- /.row (main row) -->
        </div>

        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"><?php echo e($sofficer_stock_request_pending); ?>

                            </h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">Stock Request Pending</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.sofficer_stock_request_pending')); ?>" class="small-box-footer">More info
                            <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color:#19AF9D" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';">5 ex</h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">Re-Stock Approvals Pending</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->

                <!-- ./col -->
                <div class="col-lg-3 col-6">

                    <div style="background-color: #0e6258;" class="small-box" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">Low Stock</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.reorder_level')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <!-- ./col -->

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger" id="rcorners1">
                        <div class="inner">

                            <h4 style="color: white; font-family: 'Segoe UI Light';"></h4>

                            <p style="color: white; font-family: 'Segoe UI Light';">Out of Stock </p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="<?php echo e(route('dashboard.out_of_stock')); ?>" class="small-box-footer">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
<br> <br>


            <div class="row">
                <div class="col-sm-6">
                    <canvas id="stockDistributionChart" width="500" height="250"></canvas>
                </div>
                <div class="col-sm-6">
                    <canvas id="stockValueByPurchaseTypeChart" width="600" height="300"></canvas>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Check if the stockDistributionData is not empty
            <?php if(!empty($stockDistribution)): ?>
                var ctx = document.getElementById('stockDistributionChart').getContext('2d');
                var stockDistributionData = <?php echo json_encode($stockDistribution, 15, 512) ?>;
        
                // Sort the stockDistributionData array based on total_amount values in ascending order
                stockDistributionData.sort(function(a, b) {
                    return a.total_amount - b.total_amount;
                });
        
                var labels = [];
                var data = [];
        
                stockDistributionData.forEach(function(item) {
                    labels.push(item.category_name);
                    data.push(item.total_amount);
                });
        
                // Reverse the order of the labels and data arrays to have the highest value on top
                labels.reverse();
                data.reverse();
        
                var chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Stock Value',
                            data: data,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Use horizontal bar chart
                        scales: {
                            x: { // Use x-axis scale for horizontal bar chart
                                beginAtZero: true
                            }
                        }
                    }
                });
            <?php else: ?>
                console.error('No data available for the chart.'); // Log an error if data is empty
            <?php endif; ?>
        </script>
        


        <script>
            var ctx = document.getElementById('stockValueByPurchaseTypeChart').getContext('2d');
            var stockValueByTypeData = <?php echo json_encode($stockValueByType, 15, 512) ?>;

            var labels = [];
            var data = [];

            var total = 0; // Variable to store the total value of all data points

            stockValueByTypeData.forEach(function(item) {
                if (item.total_value !== null && item.total_value !== '' && item.total_value !== 0 && item
                    .total_value !== "null" && item.total_value !== "NULL" && item.total_value !== "undefined") {
                    labels.push(item.trans_type);
                    data.push(item.total_value);
                    total += item.total_value; // Accumulate the total value
                }
            });

            var chart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Stock Value by Purchase Type',
                        data: data,
                        backgroundColor: ['#dc4d01', 'blue','gray'], // Customize colors as needed
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, context) {
                                var percentage = (value / total * 100).toFixed(2) + '%'; // Calculate percentage
                                return context.chart.data.labels[context.dataIndex] + ': ' + percentage;
                            }
                        }
                    }
                }
            });
        </script>
        <script>
            setTimeout(function() {
                window.location.reload();
            }, 60000);
        </script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/dashboard/store_officer.blade.php ENDPATH**/ ?>