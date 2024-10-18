@extends('layouts.admin')
@section('content')    
@if(Auth::user()->hasRole('purchasing_officer'))
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
                <meta name="csrf-token" content="{{ csrf_token() }}">
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
<link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}">
            </head>

          
            <div class="content-page">
                <div class="content ml-5">

                        <!-- end row -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-box">
                                    <h2 class="mt-0 mb-3">Add Inventory</h2>
                                    <div class="card-header">
                                      
                                        <a href="{{ route('inventories.index') }}" class="btn btn-primary float-right">Back</a>
                                    </div>
                                    @if (Session::has('success'))
                                        {{-- expr --}}

                                    <div class="alert alert-success">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <strong>Success!</strong> <span>{{Session::get('success')}}</span>

                                    </div>
                                    @endif

                             
                                    @if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
@endif
                                    <form action="{{ route('inventories.store')}}" method="post" enctype="multipart/form" id="employee_form">
                                        @method('POST')
@csrf
                                   
                     <!-- Form row -->
                        <div class="row">
                            
                           
                            <div class="col-md-11">
                                 <div class="form-group" id="rows bg-info" style="box-shadow: 0 25px 35px 0 lightgrey">

                                          <table class="table table-bordered myTable" id="user_table" style="width:100%; white-space: nowrap;">
                                           <thead>
                                            <tr>
                                                
                                                {{-- <th width="10%">Line #</th> --}}
                                                <th>Description</th>
                                                <th>Rate</th>
                                                <th>Quantity</th>
                                                <th style="width:40px;">Subtotal</th>
                                               
                       
                                                <th width="10%">Action</th>
                                            </tr>
                                           </thead>
                                           <tbody>

                                           </tbody>
                                           <tfoot>
                                            <tr>
                                             <td colspan="2" align="right">&nbsp;</td>
                                             <td>
Grand Total
                                             </td>
                                             <td id="grand_total">
                                             
                                                                                           </td>
                                                                                           <td>
                                                                                          
                                                                                                                                         </td>
                                            </tr>
                                           </tfoot>
                                       </table>


                                        </div>
                                        {{-- <input type="text" style="width:250px; height:40px;" name="grand_total" id="" placeholder=""> --}}
                                       
                                        <button type="submit" class="btn btn-danger float-right">Order Submit</button>
                           </div>
                           {{-- end of col --}}
                        </div>
                        
                        <!-- end row -->
                    </div>
                                </div>
                            </div>
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
                       
        
                        html += '<td><select required name="item_id[]" id="products" class="form-control pid livesearch9 select2"><option value="" readonly>Chose Item</option>{{!! \App\Http\Controllers\TotalTaxController::getTax() !!}</select></td>';
                        html += '<td><input type="number" min="0" step="0.00001" name="rate[]" id="rate" required class="form-control rate"/></td>';
                                      
                        html += '<td><input type="number" min="0" name="quantity[]" id="num1" required class="form-control qty num1" /></td>';
                        html += '<td><input type="number" min="0" step="0.00001" name="sub_total[]" id="num2" required class="form-control sub_total"/></td>';
                     
                   
                 
                        if(number > 1)
                        {
                            // $inventory = air_id;
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
                    url : "{{route('fetch_single_tax')}}",
                    method:"post",
                    data: {"id": id,"_token": "{{ csrf_token() }}"},
                    dataType:"json",
                    success:function(response)
                    {
                   
                      tr.find('.rate').val(response[0]['rate']);
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

<script>
  $(document).ready(function() {
    // Add event listener to quantity input fields
    $('tbody').on('keyup', '.qty', function() {
        // Get the quantity value
        var qty = $(this).val();
        // Find the parent row of the quantity input field
        var tr = $(this).closest('tr');
        // Get the price (rate) from the respective input field in the same row
        var price = tr.find('.rate').val();
        // Calculate the subtotal
        var subTotal = qty * price;
        // Update the subtotal input field in the same row
        tr.find('.sub_total').val(subTotal.toFixed(2));
        console.log(subTotal);
        // Calculate the total of all subtotals
        calculateTotal();
    });

    // Function to calculate the total of all subtotals
    function calculateTotal() {
        var total = 0;
        // Iterate through all rows and sum up the subtotals
        $('.sub_total').each(function() {
            // Parse the subtotal value as float and add it to the total
            total += parseFloat($(this).val());
        });
        // Display the total in a designated area, e.g., a footer row
        $('#total').text(total.toFixed(2));
        $('#grand_total').text(total.toFixed(2));
        console.log('total',total);
    }
});
$('tbody').on('keyup', '.qty', function () {
    var qty = $(this).val();
    var tr = $(this).closest('tr');
    var rate = parseFloat(tr.find('.rate').val());
    var subTotal = qty * rate;
    tr.find('.sub_total').val(subTotal.toFixed(2));
    calculation(); // Call the calculation function to update the total
});

function calculation() {
    var subTotal = 0;
    $('.sub_total').each(function () {
        subTotal += parseFloat($(this).val());
    });
    // Update the total or perform any other calculations as needed
}


</script>



<script>
    $(document).ready(function() {
        $('.selectedjquery').select2();
    });
   </script>
  
    
   <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
       integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
       crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   {!! Toastr::message() !!}
   
@endif
             @endsection

