
<?php $__env->startSection('content'); ?>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>View GRN</title>
    <!------ Include the above in your HEAD tag ---------->

    <head>

        <style>
            #invoice {
                padding: 30px;
            }

            .invoice {
                position: relative;
                background-color: #FFF;
                min-height: 680px;
                padding: 15px
            }

            .invoice header {
                padding: 10px 0;
                margin-bottom: 20px;
                border-bottom: 1px solid #3989c6
            }

            .invoice .company-details {
                text-align: right;
            }

            .invoice .company-details .name {
                margin-top: 0;
                margin-bottom: 0
            }

            .invoice .contacts {
                margin-bottom: 20px
            }

            .invoice .invoice-to {
                text-align: left
            }

            .invoice .invoice-to .to {
                margin-top: 0;
                margin-bottom: 0
            }

            .invoice .invoice-details {
                text-align: right
            }

            .invoice .invoice-details .invoice-id {
                margin-top: 0;
                color: #3989c6
            }

            .invoice main {
                padding-bottom: 50px
            }

            .invoice main .thanks {
                margin-top: -100px;
                font-size: 2em;
                margin-bottom: 50px
            }

            .invoice main .notices {
                padding-left: 6px;
                border-left: 6px solid #3989c6
            }

            .invoice main .notices .notice {
                font-size: 1.2em
            }

            .invoice table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px
            }

            .def td,
            .def th {
                padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff
            }

            .invoice table th {
                white-space: nowrap;
                font-weight: 400;
                font-size: 16px
            }

            .invoice table td h3 {
                margin: 0;
                font-weight: 400;
                color: #3989c6;
                font-size: 1.2em
            }

            .invoice table .qty,
            .invoice table .total,
            .invoice table .unit {
                text-align: right;
                font-size: 1.2em
            }

            .invoice table .no {
                color: #fff;
                font-size: 1.6em;
                background: #3989c6
            }

            .invoice table .unit {
                background: #ddd
            }

            .invoice table .total {
                background: #3989c6;
                color: #fff
            }

            .invoice table tbody tr:last-child td {
                border: none
            }

            .invoice table tfoot td {
                background: 0 0;
                border-bottom: none;
                white-space: nowrap;
                text-align: right;
                padding: 10px 20px;
                font-size: 1.2em;
                border-top: 1px solid #aaa
            }

            .invoice table tfoot tr:first-child td {
                border-top: none
            }

            .invoice table tfoot tr:last-child td {
                color: #3989c6;
                font-size: 1.4em;
                border-top: 1px solid #3989c6
            }

            .invoice table tfoot tr td:first-child {
                border: none
            }

            .invoice footer {
                width: 100%;
                text-align: center;
                color: #777;
                border-top: 1px solid #aaa;
                padding: 8px 0
            }

            @media print {
                .invoice {
                    font-size: 11px !important;
                    overflow: hidden !important
                }

                .invoice footer {
                    position: absolute;
                    bottom: 10px;
                    page-break-after: always
                }

                /* .invoice div:last-child {
                                    page-break-before: always
                                } */
            }

            @media print {

                #approvebtn,
                #backbtn,
                #printbtn {
                    display: none;
                }
            }
        </style>

    </head>
    <div id="invoice">
        <div class="card-header">
            <a href="#" id="printbtn" onclick="window.print()" class="float-left"> <img
                src="<?php echo e(asset('assets/images/icons/printer.png')); ?>" style="width:30px; height:30px;"></a>
            <a href="<?php echo e(route('inventories.index')); ?>" id="printbtn" class="btn btn-primary float-right">Back</a>
        </div>

        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col">
                            <a target="_blank">
                                <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path('/images/company/company-1.png')))); ?>"
                                    style="height: 80px; width: 220px;" alt="company image" data-holder-rendered="true" />
                            </a>
                            <br>
                        
                            <p style="font-size: 1.6em;">Goods Received Notes (GRN)</p>
                        </div>
                        <div class="col company-details">
                            <h2 class="name">
                                <a href="#" style="text-decoration:none;">
                                    <?php echo e($company->name ?? ''); ?>

                                </a>
                            </h2>
                            <div> <?php echo e($company->address ?? ''); ?></div>
                            <div> <?php echo e($company->phone ?? ''); ?></div>
                            <div> <?php echo e($company->email ?? ''); ?></div>
                        </div>
                    </div>
                </header>
                <header>
                    <div class="row">

                        <div class="col">
                            <table border="0" cellspacing="0" cellpadding="0"
                            style="width:410px; height:10%; font-size:12px;">
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    GRN Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->grn_number ?? ''); ?></th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Invoice Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->invoice_number ?? ''); ?></th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Date:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e(date('d-m-Y H:i', strtotime($inventory->created_at))); ?></th>
                            </tr>
                            
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Exchange Rate:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->exchange_rate ?? ''); ?></th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Waybill:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->waybill ?? ''); ?></th>
                            </tr>
                            
                        </table>

                    </div>
                    <div style="margin-right: 50px;">

                        <table style="width:450px; font-size:12px;">
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Supplier:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->supplier->name ?? 'not set'); ?></th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Enduser:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->enduser->asset_staff_id ?? 'not set'); ?></th>
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Billing Currency:</th>
                              
                                    <th
                                        style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white;">
                                        <?php echo e($inventory->billing_currency ?? 'Dollar'); ?></th>
                               
                            </tr>
                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                  PO Number:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->po_number ?? 'not set'); ?></th>
                            </tr>

                            <tr>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                    Type of Purchase:</th>
                                <th
                                    style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                    <?php echo e($inventory->trans_type ?? 'not set'); ?></th>
                            </tr>
                            
                        </table>
                        </div>
                    </div>
                </header>
                <main>

                    <table border="0" cellspacing="0" cellpadding="0" class="def">
                        <thead>
                            <tr>
                                
                                <th>Description</th>
                                <th>UoM</th>
                                <th>Part Number</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Location</th>


                            </tr>
                        </thead>
                        <?php
                        $totalAmount = 0;
                    ?>

                        <?php $__empty_1 = true; $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                        $amount = $ph->amount ?? 0;
                        $totalAmount += $amount;
                    ?>
                            <tbody>
                                <tr>
                                    
                                    <td><?php echo e($ph->item->item_description ?? ''); ?></td>
                                    <td><?php echo e($ph->item->item_uom ?? ''); ?></td>
                                    <td><?php echo e($ph->item->item_part_number ?? ''); ?></td>
                                    <td><?php echo e($ph->quantity ?? ''); ?></td>

                                    <td>
                                        <?php if($inventory->billing_currency == 'Dollar'): ?>
                                            <?php echo e('$ ' . $ph->unit_cost_exc_vat_gh ?? ''); ?>

                                        <?php elseif($inventory->billing_currency == 'Cedi'): ?>
                                            <?php echo e('GHC ' . $ph->unit_cost_exc_vat_gh ?? ''); ?>

                                        <?php else: ?>
                                            <?php echo e($ph->unit_cost_exc_vat_gh ?? ''); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($inventory->billing_currency == 'Dollar'): ?>
                                            <?php echo e('$ ' . $ph->amount ?? ''); ?>

                                        <?php elseif($inventory->billing_currency == 'Cedi'): ?>
                                            <?php echo e('GHC ' . $ph->amount ?? ''); ?>

                                        <?php else: ?>
                                            <?php echo e($ph->amount ?? ''); ?>

                                        <?php endif; ?>
                                    </td>

                                    <td>

                                        <?php echo e($ph->discount ?? ''); ?>

                                    </td>
                                    <td><?php echo e($ph->location->name ?? ''); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        <?php endif; ?>

                        </tr>
                        </tbody>

                    </table>
                    <div class="float-right mt-3">
                        <strong>Total Amount: 
                          
                          
                                $<?php echo e($totalAmount); ?>

                           
                        </strong>
                    </div>

                    <div class="float-left mt-9">
                        <strong>Remarks:</strong> <br>
                        <?php echo e($inventory->manual_remarks ?? ''); ?>  <br> <br>
                        <strong>Received in Good Condition By:   </strong> <br>
                            <?php echo e($inventory->user->name ?? ''); ?> , <?php echo e($inventory->created_at ?? ''); ?>  
                      <br>   <br>  <strong> Site Name:  </strong> <br>
                          <?php echo e($inventory->user->site->name ?? ''); ?>

                          
   
                      
                    </div>

                </main>
            </div>
            <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
            <div></div>
        </div>
    </div>
    <script>
        $('#printInvoice').click(function() {
            Popup($('.invoice')[0].outerHTML);

            function Popup(data) {
                window.print();
                return true;
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/inventories/show.blade.php ENDPATH**/ ?>