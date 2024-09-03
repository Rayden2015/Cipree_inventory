@extends('layouts.admin')
@section('content')     

<!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title>Add DPR</title>
             
                    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-alpha1/css/bootstrap.min.css">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                   
                    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">

<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.0.2/js/select2.min.js"></script>
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
}


            </style>
            <div class="content-page">
                <div class="content ml-5">

                        <!-- end row -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <h2 class="mt-0 mb-3">New Direct Purchase Request</h2>
                                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
        
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                                    @if (Session::has('success'))
                                        {{-- expr --}}

                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Success!</strong> <span>{{Session::get('success')}}</span>

                                    </div>
                                    @endif



                                       <form role="form"  enctype="multipart/form-data" action=@if(isset($category) )
                                    "{{route('category.update',$category->id)}}" @else() "{{route('orders.store')}}" @endif method="post" >
                                    @if (isset($category))
                                        @method('PUT')
                                    @endif

                                        @csrf

                     <!-- Form row -->
                        <div class="row">
                            <div class="col-md-11">
                                <div class="card-box" style="box-shadow: 0 20px 35px 0 lightgrey">

                                    {{-- <div >
                                      
                                        <select class="client_id form-select form-select-lg form-select-solid pb-2" id="select_client" name="supplier_id" required></select>
                                    </div> --}}
                                        </div>
                                                 {{-- card end here --}}




                                      <div class="card-box" style="box-shadow: 0 25px 35px 0 lightgrey">
                                        <div class="form-group">

                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                
                                                <label for="inputCity" class="col-form-label">Proposed Supplier <span style="color:red;">*</span> </label>
                                                <select class="client_id form-select form-select-lg form-select-solid pb-2" id="select_client" name="supplier_id" required></select>
                                            </div>
                                            

                                            <div class="form-group col-md-3 ml-5">
                                                <label for="inputCity" class="col-form-label">End-user / Asset ID <span style="color:red">*</span> </label>
                                                <select class="livesearch3 form-control p-3" name="enduser_id"  required></select>
                                            </div>
                                            <div class="form-group col-md-3 ml-5">
                                                
                                                <label for="inputCity" class="col-form-label">Request Reference</label>
                                                <input name="request_number" value="{{ $request_number }}" type="text" readonly class="form-control"
                                                placeholder={{ $request_number }}>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-3">
                                                <label for="inputCity" class="col-form-label">Type of Purchase</label>
                                                <select class="select form-control" id="type_of_purchase" name="type_of_purchase"
                                                required data-fouc data-placeholder="Request Type..">
                                                <option value=""></option>
                                                <option {{ old('type_of_purchase') == 'Direct' ? 'selected' : '' }}
                                                    value="Direct">Direct
                                                </option>
                                                <option {{ old('type_of_purchase') == 'Stock' ? 'selected' : '' }}
                                                    value="Stock">
                                                    Stock</option>
                                            </select>
                                            </div>

                                         
                                            <div class="form-group col-md-3 ml-5">
                                                <label for="inputCity" class="col-form-label">Work Order Ref <span style="color:red">*</span></label>
                                                <input name="work_order_ref"  type="text"  required class="form-control"
                                                >
                                            </div>

                                            <div class="form-group col-md-3 ml-5">
                                                <label for="inputCity" class="col-form-label"> Date Created</label>
                                                <input name="request_date" value="{{$request_date}}" type="text" readonly class="form-control date-pick"
                                                >
                                            </div>
                                          
                                            
                                        </div>
                                      </div>
                                        <br><br>

                                
                            </div>
                           
                            <div class="col-md-11">
                                 <div class="form-group" id="rows bg-info" style="box-shadow: 0 25px 35px 0 lightgrey">

                                          <table class="table table-bordered table-striped table-responsive" id="user_table">
                                           <thead>
                                            <tr>
                                                
                                                {{-- <th width="10%">Line #</th> --}}
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="25%">Description</th>
                                               
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="25%">Part Number</th>
                                                 <th style="background-color:rgb(79, 79, 231); color:white;" width="10%">UoM</th>
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="10%">Quantity</th>
                                                                                              
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="5%">Priority</th>
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="45%">Remarks</th>
                                                <th style="display:none;" width="15%">Price</th>
                                                <th style="display:none;" width="15%">Products</th>

                                               
                                                {{-- <th width="15%">Comments</th> --}}
                                                <th style="background-color:rgb(79, 79, 231); color:white;" width="10%">Action</th>
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
                                        <button type="submit" class="btn btn-success pull-right"> Submit</button>
                           </div>
                           {{-- end of col --}}
                           
                        </div>
                        <!-- end row -->
                        
                    </div>
                                       



                                </div>

                            </div>
                            <!-- end col -->





                        </div>
                        <!-- end row -->

                         </form>

                </div>




              @endsection()
              <script type="text/javascript">
                $.fn.select2.defaults.set("theme", "bootstrap");
                        $("#select_client").select2({
                            width: null
                        })
                </script>
              @section('scripts')
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
                        // html += '<td> <select name="products[]" id="products" class="form-control pid" required><option value="" readonly>Chose Part</option>{{!! \App\Http\Controllers\OrderController::fetch_products() !!}</select></td>';
                        // html += '<td><input type="text" name="line_number[]" required class="form-control"/></td>';
                        html += '<td><input type="text" name="description[]" required class="form-control"/></td>';
                      
                        html += '<td><input type="text" name="part_number[]" required class="form-control"/></td>';


                        html += `
    <td>
        <select style="height:33px; font-size:11px;" class="select form-select" id="uom_id" name="uom_id[]" required>
            <option value="" selected hidden>Please Select</option>
            @foreach ($uom as $um)
                <option {{ old('um') == $um->id ? 'selected' : '' }} value="{{ $um->id }}">{{ $um->name }}</option>
            @endforeach
        </select>
    </td>
`;




                        html += '<td><input type="number" name="quantity[]" min="0" required class="form-control qty" /></td>';
                        html += '<td> <select style="height:33px; font-size:11px;"  class="select form-select" id="priority" name="priority[]" required"></option><option>  <option {{ old('priority') == 'High' ? 'selected' : '' }} value="High">High </option><option {{ old('priority') == 'Medium' ? 'selected' : '' }} value="Medium">Medium</option><option {{ old('priority') == 'Low' ? 'selected' : '' }} value="Low">Low</option> </select></td>';
                        // html += '<td><input type="text" name="" class="form-control price" /></td>';
                        html += '<td><input type="text" name="remarks[]" class="form-control"/></td>';
                        html += '<td style="display:none;"><input type="text" name="unit_price[]" class="form-control"/></td>';
                         html += '<td style="display:none;"> <select name="products[]" id="products" class="form-control pid"><option value="" readonly>Chose Part</option>{{!! \App\Http\Controllers\OrderController::fetch_products() !!}</select></td>';
                        

                       
                       
                     

                       
                        // html += '<td><input type="text" name="comments[]" class="form-control"/></td>';

                        // $('#products:last').select2();
                        if(number > 1)
                        {
                            html += '<td><button type="button" name="remove" id="" class="btn btn-light remove"> <span style="color:red;"> X</span> </button></td></tr>';
                            // $('#products:last').select2();

                            $('tbody').append(html);

                        }
                        else
                        {
                            html += '<td><button type="button" name="add" id="add" class="btn" style="background-color:rgb(79, 79, 231);"> <span style="color:white;">Add</span> </button></td></tr>';

                            $('tbody').html(html);



                        }
                          add_select();


                 }



                 $(document).on('click', '#add', function(){
                  count++;

                  dynamic_field(count);
                 });

                 $(document).on('click', '.remove', function(){
                  count--;
                  $(this).closest("tr").remove();
                 });


                $('tbody').delegate('.pid','change',function(){
                   var id = $(this).val();
                   var tr  = $(this).parent().parent();

                   $.ajax({
                    url : "{{URL::to('fetch_single_product')}}",
                    method:"post",
                    data: {"id": id,"_token": "{{ csrf_token() }}"},
                    dataType:"json",
                    success:function(response)
                    {


                      tr.find('.qty').val(1);
                      tr.find('.price').val(response[0]['price']);
                      tr.find('.total').text(1*response[0]['price'])
                      tr.find('.tqty').val(1*response[0]['quantity'])
                      $('#dis').val(0);

                      calculation(0,0);
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
                        url:"{{URL::to('fetch_customer')}}",
                        method:"POST",
                        data:{'number' : number,"_token":"{{csrf_token()}}"},
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

             @endsection

