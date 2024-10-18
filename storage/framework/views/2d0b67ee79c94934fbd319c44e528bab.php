<?php $__env->startSection('content'); ?>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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

        

        <div class="invoice overflow-auto">
            
            <a href="#" id="printbtn" onclick="window.print()" class="float-left"> <img
                    src="<?php echo e(asset('assets/images/icons/printer.png')); ?>" style="width:30px; height:30px;"></a>
            
            
            
            


            
          
            <?php if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Authoriser')): ?>
                <a href="<?php echo e(route('sorders.store_lists')); ?>" id="printbtn" class="btn btn-primary float-right">Back</a>
            <?php elseif(Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant')): ?>
                <a href="<?php echo e(URL::previous()); ?>" id="printbtn" class="btn btn-primary float-right">Back</a>
            <?php endif; ?>
            
            <br>
            <br>
            <div style="min-width: 600px">
                <header>

                    <div class="row">
                        <div class="col">
                            <a target="_blank">
                                <img src="<?php echo e(asset('images/company/' . $company->image)); ?>"
                                    style="height: 80px; width: 250px;" alt="company image" data-holder-rendered="true" />
                            </a>
                            <br>

                            <p style="font-size: 1.6em;">Stock Requisition (SR)</p>
                        </div>

                        <div class="col">


                            
                        </div>

                        <div class="col company-details">
                            <h2 class="name">
                                <a target="#" href="#" style="text-decoration: none; pointer-events: none;">
                                    <?php echo e($company->name ?? ''); ?>

                                </a>
                            </h2>
                            <div> <?php echo e($company->address ?? ''); ?></div>
                            <div> <?php echo e($company->phone ?? ''); ?></div>
                            <div> <?php echo e($company->email ?? ''); ?></div>
                        </div>
                    </div>
                </header>
                <main>

                    <header>
                        <div class="row">

                            <div class="col">
                                <table border="0" cellspacing="0" cellpadding="0"
                                    style="width:410px; height:10%; font-size:12px;">
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            SR Number:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->request_number ?? ' '); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Request Date:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e(date('d-m-Y H:i:s', strtotime($sorder->request_date))); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Request By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->request_by->name ?? ' '); ?> </th>
                                    </tr>
                                    
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            End User:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->enduser->asset_staff_id ?? ' '); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Department:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->enduser->departmente->name ?? ' '); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Name/Description:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->enduser->name_description ?? ' '); ?></th>
                                    </tr>
                                </table>

                            </div>
                            <div style="margin-right: 50px;">

                                <table style="width:450px; font-size:12px;">
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Approval Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->approval_status ?? 'Pending'); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Approved By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->approve_by->name ?? ''); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Approved On:</th>
                                        <?php if($sorder->approved_on !== null): ?>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white;">
                                                <?php echo e(date('d-m-Y H:i:s', strtotime($sorder->approved_on ?? ''))); ?></th>
                                        <?php else: ?>
                                            <th
                                                style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black;background-color:white;">
                                            </th>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supplied By:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->user->name ?? ' '); ?></th>
                                    </tr>

                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            Supplied On:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->delivered_on ?? ' '); ?></th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; font-weight:bold; background-color:white; color:blue;">
                                            SR Status:</th>
                                        <th
                                            style="border-left:1px solid black;border-right:1px solid black;border-top:1px solid black;border-bottom:1px solid black; background-color:white;">
                                            <?php echo e($sorder->status ?? ' '); ?></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </header>
                    <table border="0" cellspacing="0" cellpadding="0" class="def">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Description</th>
                                <th>Part Number</th>
                                <th>UoM</th>
                                <th>Qty Req.</th>
                                <th>Qty Sup.</th>
                                <th>Location</th>
                                <th>Remarks</th>
                                <th>Unit Cost</th>
                                <th>Amount</th>


                            </tr>
                        </thead>

                        <?php $__empty_1 = true; $__currentLoopData = $sorder_parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tbody>
                                <tr>
                                    <td
                                        style="
                background: #eee;
                border-bottom: 1px solid #fff; text-align:left;">
                                        <?php echo e($ph->id); ?></td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->item_details->item_description ?? ''); ?></td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->item_details->item_part_number ?? ''); ?></td>
                                    
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->item_details->item_uom ?? ''); ?></td>

                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->quantity ?? ''); ?></td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->qty_supplied ?? ''); ?></td>
                                  
                                        <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->item_parts->location->name ?? ''); ?></td>

                                        <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->remarks ?? ''); ?></td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->item_parts->unit_cost_exc_vat_gh ?? ''); ?></td>
                                    <td
                                        style=" padding: 15px;
                background: #eee;
                border-bottom: 1px solid #fff">
                                        <?php echo e($ph->sub_total ?? ''); ?></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                                
                        <?php endif; ?>

                        </tr>
                       
                        </tbody>
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>


                            </tr>
                        </thead>
                      
                          <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="font-weight: bold">Total (USD):</td>
                                <td style="font-weight: bold">$ <?php echo e($sorder->total ?? ''); ?> </td>
                            </tr>
                          </tbody>
                                                  
                    </table>
                    
                    <div style=" padding: 10px; width:40%;">
                        <p style="font-weight: bold;">Received in Good Condition by: <?php echo e($sorder->supplied_to ?? ' '); ?></p> <br>
                        <p style="font-weight: bold;">Signed by:</p> <br>
                    </div>

                   
                    <div style=" padding: 10px; width:40%;">
                        <p style="font-weight: bold;">Notes: </p>
                         
                            <p><?php echo e($sorder->manual_remarks ?? ' '); ?></p> 
                      
                    </div>
                    
                    <br>
                    
                    <?php if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('Super Authoriser') || Auth::user()->hasRole('Department Authoriser')): ?>
                        
                        <?php if($sorder->approval_status == ''): ?>
                            
                            
                            <a href="<?php echo e(route('stores.approved_status', $sorder->id)); ?>" id="approvebtn"
                                class="btn btn-success float-right">Approve</a>
                        <?php elseif($sorder->approval_status == 'Approved'): ?>
                            
                            
                            <a href="<?php echo e(route('stores.denied_status', $sorder->id)); ?>" id="approvebtn"
                                class="btn btn-danger float-right">Deny</a>
                        <?php elseif($sorder->approval_status == 'Denied'): ?>
                            
                            
                            <a href="<?php echo e(route('stores.approved_status', $sorder->id)); ?>" id="approvebtn"
                                class="btn btn-success float-right">Approve</a>
                        <?php else: ?>
                            
                            
                            <a href="<?php echo e(route('stores.denied_status', $sorder->id)); ?>" id="approvebtn"
                                class="btn btn-danger float-right">Deny</a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    
                    <?php if(
                        (Auth::user()->hasRole('store_officer') && $sorder->approval_status == 'Approved') ||
                            (Auth::user()->hasRole('store_assistant') && $sorder->approval_status == 'Approved')): ?>
                        <a href="<?php echo e(route('stores.store_officer_edit', $sorder->id)); ?>" class="btn btn-success float-right"
                            style="padding-right:10px;" id="approvebtn">Process</a>
                    <?php else: ?>
                    <?php endif; ?>
                    
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/stores/view.blade.php ENDPATH**/ ?>