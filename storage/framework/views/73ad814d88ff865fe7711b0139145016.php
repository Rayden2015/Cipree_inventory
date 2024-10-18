

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cart</title>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
    <link rel="stylesheet" type="text/css"
        href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.0.2/js/select2.min.js"></script>
</head>

<style>
    html,
    body {
        padding: 0;
        margin: 0;
        height: 100%;
    }

    footer {
        z-index: -1;
        position: relative;
        top: -60px;
        margin-bottom: -60px;
    }

    .select2-selection__rendered {
        line-height: 31px !important;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .select2-selection__arrow {
        height: 34px !important;
    }

    .ms-1 {
        margin-left: ($spacer * .25) !important;
    }

    #cart {
        margin-left: 3%;
        width: 50%;
    }
</style>
<?php $__env->startSection('content'); ?>
    <table id="cart" style="width: 90%;" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width:40%">Description</th>
                <th style="width:50%">Part Number</th>
                <th style="width:50%">Stock Code</th>
                <th style="width:8%">UoM</th>
                <th style="width:50%">Unit Cost</th>
                
                
                <th style="width:8%">Quantity</th>


                
                <th style="width:10%"></th>
            </tr>
        </thead>
        <tbody>
            <div class="content-page">
                <div class="content ml-5">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-box">
                                <h2 class="mt-0 mb-3">Add Request</h2>
                                <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>
        
                <?php if(session('error')): ?>
                    <div class="alert alert-danger">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                                <?php if(Session::has('success')): ?>
                                    

                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <strong>Success!</strong> <span><?php echo e(Session::get('success')); ?></span>

                                    </div>
                                <?php endif; ?>
                                <?php if(Session::has('errors')): ?>
                                    <div class="alert alert-danger">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <strong>Oops, out of quantity!</strong> <span><?php echo e(Session::get('danger')); ?></span>

                                    </div>
                                <?php endif; ?>


                                <form role="form" enctype="multipart/form-data"
                                    action=<?php if(isset($category)): ?> "<?php echo e(route('category.update', $category->id)); ?>" <?php else: ?> "<?php echo e(route('sorders.store')); ?>" <?php endif; ?>
                                    method="post">
                                    <?php if(isset($category)): ?>
                                        <?php echo method_field('PUT'); ?>
                                    <?php endif; ?>

                                    <?php echo csrf_field(); ?>

                                    <!-- Form row -->
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="card-box" style="box-shadow: 0 20px 35px 0 lightgrey">

                                                
                                            </div>
                                            




                                            <div class="card-box" style="box-shadow: 0 25px 35px 0 lightgrey">
                                               
                                                <div class="form-row">

                                                    <div class="form-group col-md-4">

                                                        <label for="inputCity" class="col-form-label">Request
                                                            Number</label>
                                                        <input style="width:250px;" name="request_number"
                                                            value="<?php echo e($request_number); ?>" type="text" readonly
                                                            class="form-control date-pick ml-3"
                                                            placeholder=<?php echo e($request_number); ?>>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="inputCity" class="col-form-label">Request Date</label>
                                                        <input name="request_date" value="<?php echo e($request_date); ?>"
                                                            type="text" readonly class="form-control date-pick"
                                                            placeholder=<?php echo e($request_date); ?>>
                                                    </div>
                                                </div>

                                            </div>
                                            <br><br>
                                        </div>

                                    </div>

                                    <div class="col-md-11">
                                        <div class="form-group" id="rows bg-info"
                                            style="box-shadow: 0 25px 35px 0 lightgrey">

                                            <?php $total = 0 ?>
                                            <?php if(session('cart')): ?>
                                                <?php $__currentLoopData = session('cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    

                                                    <tr data-id="<?php echo e($id); ?>">
                                                        <td data-th="Product" style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    <?php echo e($details['item_description']); ?></div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    <?php echo e($details['item_part_number']); ?></div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    <?php echo e($details['item_stock_code']); ?></div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    <?php echo e($details['item_uom']); ?></div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    <?php echo e($details['unit_cost_exc_vat_gh']); ?></div>

                                                            </div>
                                                        </td>

                                                        
                                                        
                                                        
                                                        <td data-th="Quantity">
                                                            <input type="number" value="<?php echo e($details['quantity']); ?>"
                                                                class="form-control quantity update-cart" />
                                                        </td>

                                                        
                                                        <td class="actions" data-th="">
                                                            <button
                                                                class="btn btn-danger btn-sm remove-from-cart">X</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
        </tbody>

        <tfoot>
            
            <br>

            <tr>
                <td colspan="5" class="text-left">
                    
                    <div class="form-row">


                        <div class="form-group col-md-3">
                            <label for="inputCity" class="col-form-label">Enduser </label>
                        <select class="livesearch3 form-control p-3"
                            required name="enduser_id" id="info_id"></select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="inputCity" class="col-form-label">Name/Description:</label>
                            <input type="text" name="" id="name_description" style="width:350px;" class="form-control" readonly>
                        </div>

                        <div class="form-group col-md-2" style="margin-left: 250px;">
                            <label for="inputCity" class="col-form-label"> Designation</label>
                            <input type="text" name="" id="designation"  class="form-control" readonly>
                        </div>




                    </div>
                    
                    <button type="submit" class="btn btn-success float-right">Checkout</button>
                </td>

            </tr>

        </tfoot>


        </form>
    </table>

    </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script type="text/javascript">
        $(".update-cart").change(function(e) {
            e.preventDefault();

            var ele = $(this);

            $.ajax({
                url: '<?php echo e(route('update.cart')); ?>',
                method: "patch",
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    id: ele.parents("tr").attr("data-id"),
                    quantity: ele.parents("tr").find(".quantity").val()
                },
                success: function(response) {
                    window.location.reload();
                }
            });
        });

        $(".remove-from-cart").click(function(e) {
            e.preventDefault();

            var ele = $(this);

            if (confirm("Are you sure want to remove?")) {
                $.ajax({
                    url: '<?php echo e(route('remove.from.cart')); ?>',
                    method: "DELETE",
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        id: ele.parents("tr").attr("data-id")
                    },
                    success: function(response) {
                        window.location.reload();
                    }
                });
            }
        });
    </script>

    <script type="text/javascript">
        $('.livesearch3').select2({
            placeholder: 'Select Enduser',
            ajax: {
                url: '/ajax-autocomplete-enduser',
                dataType: 'json',
                delay: 250,
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.asset_staff_id,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    </script>
    <script>
        $("#info_id").change(function() {
  $.ajax({
    url: '/fetch_single_enduser/' + $(this).val(),
    type: 'get',
    data: {},
    success: function(data) {
      if (data.success == true) {
        // console.log('worksssss');
        $("#name_description").val(data.info );
        // $("#designation").val(data.info );
        // $("#info_area").value = data.info;
      } else {
        alert('Cannot find info');
      }

    },
    error: function(jqXHR, textStatus, errorThrown) {}
  });
});
    </script>
    <script>
        $("#info_id").change(function() {
  $.ajax({
    url: '/fetch_single_enduser1/' + $(this).val(),
    type: 'get',
    data: {},
    success: function(data) {
      if (data.success == true) {
        // console.log('worksssss');
        $("#designation").val(data.info );
        // $("#designation").val(data.info );
        // $("#info_area").value = data.info;
      } else {
        alert('Cannot find info');
      }

    },
    error: function(jqXHR, textStatus, errorThrown) {}
  });
});
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/pensmqhz/test.cipree.com/resources/views/cart.blade.php ENDPATH**/ ?>