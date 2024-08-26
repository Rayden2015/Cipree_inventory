<?php $__env->startSection('content'); ?>    

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
                <title>Document</title>
               
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
                    integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
                    crossorigin="anonymous" />
                    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                   
                    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">

<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.0.2/js/select2.min.js"></script>
<link rel="stylesheet" href="<?php echo e(asset('/css/select2.min.css')); ?>">
            </head>

            <style>
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
  margin-left:($spacer * .25) !important;
}input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
#contents div.select2-container {
    margin: 10px;
    display: block;
    max-width: 60%;
}

            </style>
            <div class="content-page">
                <div class="content ml-5">

                        <!-- end row -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <h2 class="mt-0 mb-3">Add Inventory</h2>
                                    <div class="card-header">
                                      
                                        <a href="<?php echo e(route('inventories.index')); ?>" class="btn btn-primary float-right">Back</a>
                                    </div>
                                    <?php if(Session::has('success')): ?>
                                        

                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Success!</strong> <span><?php echo e(Session::get('success')); ?></span>

                                    </div>
                                    <?php endif; ?>

                             
                                    <?php if(session()->has('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session()->get('error')); ?>

    </div>
<?php endif; ?>
                                    <form action="<?php echo e(route('inventories.store')); ?>" method="post" enctype="multipart/form" id="employee_form">
                                        <?php echo method_field('POST'); ?>
<?php echo csrf_field(); ?>
                                   
                     <!-- Form row -->
                        <div class="row">
                            <div class="col-md-11">
                                <div class="card-box" style="box-shadow: 0 20px 35px 0 lightgrey">

                                        </div>
                                                 

                                      <div class="card-box" style="box-shadow: 0 25px 35px 0 lightgrey">
                                        <div class="form-group">

                                        </div>
                                        <div class="form-row">
                                            
                                            <div class="form-group col-md-3">
                                                
                                                <label for="inputCity" class="col-form-label">Select Supplier</label>
                                                <select class="client_id form-select form-select-lg form-select-solid pb-2" id="select_client" name="supplier_id" ></select>
                                                
                                            </div>
                                            
                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label">Enduser </label>
                                                <select class="livesearch3 form-control p-3" required name="enduser_id"></select>
                                            </div>

                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label"> Type of Purchase</label>
                                                <select class="select form-control" id="trans_type" required name="trans_type"
                                                data-fouc data-placeholder="Type of Purchase">
                                               <option value=""></option>
                                             
                                               <option <?php echo e(old('trans_type') == 'Direct Purchase' ? 'selected' : ''); ?>

                                                   value="Direct Purchase">
                                                   Direct Purchase</option>
                                                   <option <?php echo e(old('trans_type') == 'Stock Purchase' ? 'selected' : ''); ?>

                                                   value="Stock Purchase">
                                                   Stock Purchase</option>
                                           </select>
                                            </div>
                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label"> Invoice Reference</label>
                                                <input name="invoice_number" type="text"  onblur="duplicateInvoiceNumber(this)" value="<?php echo e(old('invoice_number')); ?>" required  class="form-control date-pick" placeholder="Invoice Reference">
                                            </div>

                                            <div class="form-group col-md-2">
                                                
                                                <label for="inputCity" class="col-form-label">GRN Number</label>
                                                <input name="request_number" value="<?php echo e($grn_number); ?>" type="text" readonly class="form-control date-pick"
                                                placeholder=<?php echo e($grn_number); ?>>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label">Billing Currency</label>
                                                <select class="select form-control" id="billing_currency" required name="billing_currency"
                                                 data-fouc data-placeholder="Billing Currency..">
                                                <option value=""></option>
                                              
                                                <option <?php echo e(old('billing_currency') == 'Dollar' ? 'selected' : ''); ?>

                                                    value="Dollar">
                                                    Dollar</option>
                                            </select>
                                            </div>
                                           
                                            <div class="form-group col-md-3">
                                                <label for="inputCity" class="col-form-label">Exchange Rate:</label>
                                                <input type="text" name="exchange_rate" value="<?php echo e(old('exchange_rate')); ?>" min="0" step="0.00001" class="form-control" required placeholder="Exchange Rate">
                                                                       </div>
                                           
                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label">Waybill Reference:</label>
                                                <input type="text" name="waybill" id="waybill"  onblur="duplicateWaybill(this)" value="<?php echo e(old('waybill')); ?>" required class="form-control" placeholder="Waybill Reference">
                                                                       </div>

                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label"> PO Reference</label>
                                                <input name="po_number" type="text" value="<?php echo e(old('po_number')); ?>"  onblur="duplicatePoNumber(this)" required class="form-control date-pick" placeholder="PO Reference">
                                            </div>
                                            

                                            <div class="form-group col-md-2">
                                                <label for="inputCity" class="col-form-label"> Date</label>
                                                <input name="date" value="<?php echo e($date); ?>"  type="text" readonly class="form-control date-pick"
                                                placeholder=<?php echo e($date); ?>>
                                            </div>
                                          
                                            
                                        </div>
                                      </div>
                                        <br><br>

                                
                            </div>
                           
                            <div class="col-md-11">
                                 <div class="form-group" id="rows bg-info" style="box-shadow: 0 25px 35px 0 lightgrey">

                                          <table class="table table-bordered myTable" id="user_table" style="width:100%; white-space: nowrap;">
                                           <thead>
                                            <tr>
                                                
                                                
                                                <th>Description</th>
                                                <th>Stock Code</th>
                                                <th>Part Number</th>
                                                <th style="width:40px;">Quantity</th>
                                                <th>Unit Cost</th>
                                                <th>Disc %</th>
                                                <th>Ccy. </th>   
                                                <th>Amount </th>
                                               
                                                <th style="display:none;">Price</th>
                                                
                                                <th>Location</th>
                       
                                                <th width="10%">Action</th>
                                            </tr>
                                           </thead>
                                           <tbody>

                                           </tbody>
                                           <tfoot>
                                            <tr>
                                             <td colspan="2" align="right">&nbsp;</td>
                                             <td>

                                             </td>
                                            </tr>
                                           </tfoot>
                                       </table>


                                        </div>
                                        <input type="text" style="width:250px; height:40px;" name="delivered_by" id="" placeholder="Received By">
                                       
                                        <button type="submit" class="btn btn-danger float-right">Order Submit</button>
                           </div>
                           
                        </div>
                        
                        <!-- end row -->
                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end row -->
                         </form>
                </div>

              <?php $__env->stopSection(); ?>
              <script type="text/javascript">
                $.fn.select2.defaults.set("theme", "bootstrap");
                        $("#select_client").select2({
                            width: null
                        })
                </script>
              <?php $__env->startSection('scripts'); ?>
                  <script  type="text/javascript" charset="utf-8" async defer>
                    $(document).ready(function()
                    {
                        var count = 1;
                        html = '';


                 dynamic_field(count);
                 function add_select(){

                          $('body').find('#products').select2();
                 }

                 function dynamic_field(number)
                 {

                  html = '<tr>';
                       
        
                        html += '<td><select required name="item_id[]" id="products" class="form-control pid livesearch9 select2"><option value="" readonly>Chose Item</option>{<?php echo \App\Http\Controllers\InventoryController::getItem(); ?></select></td>';
                        html += '<td><input type="text" name="" class="form-control item_stock_code" readonly /></td>';
                        html += '<td><input type="text" name="" class="form-control item_part_number" readonly /></td>';
                       
                        html += '<td><input type="number" min="0" name="quantity[]" id="num1" required class="form-control qty num1" /></td>';
                      
                        html += '<td><input type="number" min="0" step="0.00001" name="unit_cost_exc_vat_gh[]" id="num2" required class="form-control num2"/></td>';
                        html += '<td><input type="number" min="0" step="0.00001" max="100" name="discount[]" id="num3" class="form-control num3"/></td>';
                        html += '<td><input type="text" id="currency" readonly class="form-control currency"/></td>';
                        html += '<td><input type="text" id="billing_currency" readonly class="form-control sum1"/></td>';
                
                        html += '<td style="display:none;"><input type="text" id="sum" class="form-control sum"/></td>';
                        html += '<td><select required name="location_id[]"  class="form-control required  selectedjquery select2"><option value="" readonly>Chose Location</option>{<?php echo \App\Http\Controllers\InventoryController::fetch_locations(); ?></select></td>';
            
                      
                         html += '<td style="display:none;"> <select name="products[]" id="products" class="form-control"><option value="" readonly>Chose Part</option>{<?php echo \App\Http\Controllers\OrderController::fetch_products(); ?></select></td>';
                 
                   
                 
                        if(number > 1)
                        {
                            $inventory = air_id;
                            html += '<td><button type="button" name="remove" id="" class="btn btn-danger remove">Remove</button></td></tr>';
                           
                            // $('#products:last').select2();
                            var nan = "<tr><td></td></tr>"

                            $('tbody').append(html);
                            // $('tbody').append($inventory);
                           
                        }
                        else
                        {
                            html += '<td><button type="button" name="add" id="add" class="btn btn-success">Add</button></td></tr>';

                            $('tbody').html(html);



                        }
                          add_select();
                 }

                 $(document).on('click', '#add', function(){
  
                  count++;

                  dynamic_field(count);
                  var row = $("#user_table tr:last");

row.find(".select2").each(function(index)
{
    $(this).select2('destroy');
}); 

var newrow = row.clone();       

// $("#user_table").append(newrow);

$("select.select2").select2();
             });

                 $(document).on('click', '.remove', function(){
                  count--;
                  $(this).closest("tr").remove();
                //   $(this).closest("tbody").remove();
                 });


                $('tbody').delegate('.pid','change',function(){
                   var id = $(this).val();
                   var tr  = $(this).parent().parent();

                   $.ajax({
                    url : "<?php echo e(route('fetch_single_product')); ?>",
                    method:"post",
                    data: {"id": id,"_token": "<?php echo e(csrf_token()); ?>"},
                    dataType:"json",
                    success:function(response)
                    {
                   
                      tr.find('.item_stock_code').val(response[0]['item_stock_code']);
                      tr.find('.item_part_number').val(response[0]['item_part_number']);
                                      
                    }

                   });
                });
                
                $('tbody').on('keyup','.qty',function(){
                    var qty = $(this).val();
                    var tr = $(this).parent().parent();
                   var price =  tr.find('.price').val();
                    var new_total = qty * price;
                    tr.find('.total').text(new_total);
                    calculation(0,0);
                });
           
                function calculation(dis,paid){
                  var sub_total = 0;
                  var net_total = 0;
                  var discount = dis;
                  var paid  = paid;

                    $('.total').each(function(){
                      sub_total = sub_total + ($(this).html()*1);

                    });
                  $('#sub_total').val(sub_total);

                  if(sub_total > 0)
                  {
                    var tax = (sub_total * 0) / 100;
                     $('#gst').val(Math.round(tax));
                     var net_total = tax + sub_total;
                     $('#net_total').val(Math.round(net_total));
                    if(discount > 0)
                      {
                        var net_total = net_total - discount;
                         $('#net_total').val(Math.round(net_total));
                      }
                    if(paid > 0)
                    {
                      net_total = Math.round(net_total - paid);
                      $('#due').val(net_total);
                    }
                  }
                }
                $('#dis').keyup(function(){
                    var discount = $(this).val();

                    calculation(discount);
                });

                $('#paid').keyup(function(){
                    var paid = $(this).val();
                    var discount  = $('#dis').val();
                    calculation(discount,paid);
                });
                // sending ajax request to fetch customer
                $('#number').keyup(function(){
                    var number = $(this).val();

                    if(number.length > 11)
                    {
                      alert('Please enter a valid number');
                      $('#number').val('');
                      $('#name').val('');
                    }
                    else
                    {
                      $.ajax({
                        url:"<?php echo e(URL::to('fetch_customer')); ?>",
                        method:"POST",
                        data:{'number' : number,"_token":"<?php echo e(csrf_token()); ?>"},
                        dataType:'json',
                        success:function(response)
                        {
                          if(response.name != '')
                          {
                            $('#name').val(response.name);
                          }
                        }
                      });
                    }
                });

                    });

                  </script>

<script type="text/javascript">

    $('.client_id').select2({
        placeholder: 'Select Suggested Supplier',
        ajax: {
            url: '/ajax-autocomplete-search',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
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


<script type="text/javascript">
    $('.livesearch9').select2({
        placeholder: 'Select Item',
        ajax: {
            url: '/ajax-autocomplete-item',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        }
    });
</script>
<script type="text/javascript">
$('.livesearch101').select2({
        placeholder: 'Select Location',
        ajax: {
            url: '/ajax-autocomplete-items',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.name,
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
jQuery($ => { // DOM ready and $ alias in scope
    // console.log('hello');
    $(".num1, .num2,.num3",).on("keydown keyup", sum);
    console.log(sum());
  function sum() {
     $inventory = air_id;
    const $row = $(this).closest("tr");
    
    const $num1 = $row.find(".num1");
    const $num2 = $row.find(".num2");
    const $num3 = $row.find(".num3");
    
    const $sum = $row.find(".sum");
    const $sum1 = $row.find(".sum1");

    $sum.val(Number($num1.val()) * Number($num2.val()));
    $sum1.val(Number($sum.val()) - (Number($sum.val()) * Number($num3.val()) / 100));

  }

  $(".myTable").on("keydown keyup", ".num1, .num2, .num3", sum);
console.log( $(".sum").on("keydown keyup", ".num1, .num2, .num3", sum));
});    
</script>

<script>
    jQuery($ => { // DOM ready and $ alias in scope
        // console.log('hello');
        $(".num1").on("keydown keyup", hack);
      
      function hack() {
         $inventory = air_id;
        const $row = $(this).closest("tr");
        const $num1 = $row.find(".num1");   
        const $newsum = $row.find(".currency");              
               if($inventory == 'Dollar'){
                $newsum.val('$');
               }else {
                $newsum.val('$');
               }
      }
    
      $(".myTable").on("keydown keyup", ".num1", hack);
    console.log( $(".sum").on("keydown keyup", ".num1", hack));
    });    
    </script>

<script>
    let air_id ='';
    $(document).ready(function(){
  $("#billing_currency").change(function(){
     air_id =  $(this).val();
    $(".num10").val(air_id );
    console.log(air_id);
    // var $mealone = air_id;
    if(air_id == 'Dollar'){
      console.log('$');
    } else{
        console.log('GHC');
    }
  });
});
</script>

<script>
    $(document).ready(function() {
        $('.selectedjquery').select2();
    });
   </script>
   
     
          <script>
            function duplicateInvoiceNumber(element){
                 var invoice_number = $(element).val();
                 $.ajax({
                     type: "POST",
                     url: '<?php echo e(url('check_invoice_number')); ?>',
                     data: {invoice_number:invoice_number,
                         _token: $('meta[name="csrf-token"]').attr('content') },
                     dataType: "json",
                     success: function(res) {
                         if(res.exists){
                             alert('Invoice Number already exists');
                         }else{
                             // alert('does not exist');
                         }
                     },
                     error: function (jqXHR, exception) {
         
                     }
                 });
             } 
             </script>
    
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
       integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
       crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   <?php echo Toastr::message(); ?>

   
             <?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\Hackman_GH\Desktop\Zipped Projects\Laravel-10-roles-and-permissions-master\resources\views/inventories/create.blade.php ENDPATH**/ ?>