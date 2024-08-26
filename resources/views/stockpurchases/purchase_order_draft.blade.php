@extends('layouts.admin2')
@section('content')
    <!DOCTYPE html>
    <html lang="en">


    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        {{--  --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
        {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> --}}
        <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script>
        {{--  --}}
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.0.0-alpha1/css/bootstrap.min.css">
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>



        <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}">

    </head>

    <body>


        <div class="card">

            <div class="card-header">

                <a href="{{ route('home') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">

                <form id="purchaseForm" action="{{ route('spr_purchase_update', $purchase_order->id) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Edit Purchase</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->


                        <div class="card-body">
                            <div class="row">
                                {{-- <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Supplier: </label>
                                        <select data-placeholder="Choose..." name="supplier_id" id="supplier_id"
                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($suppliers as $ed)
                                                <option {{ $purchase_order->supplier_id == $ed->id ? 'selected' : '' }}
                                                    value="{{ $ed->id }}">{{ $ed->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}



                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Supplier's Reference: </label>
                                        <input type="text" name="suppliers_reference"
                                            value="{{ $purchase_order->suppliers_reference }}" class="form-control"
                                            required>
                                    </div>
                                </div>


                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>PO Reference: </label>
                                        <input type="text" name="po_number" readonly
                                            value="{{ $purchase_order->po_number }}" class="form-control">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Type of Purchase: </label>
                                        <select class="select form-control" id="type_of_purchase" name="type_of_purchase"
                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole required
                                            data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $purchase_order->type_of_purchase == 'Direct' ? 'selected' : '' }}
                                                value="Direct">Direct</option>
                                            <option {{ $purchase_order->type_of_purchase == 'Stock' ? 'selected' : '' }}
                                                value="Stock">Stock</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Enduser: </label>
                                        <select data-placeholder="Choose..." name="enduser_id" id="enduser_id"
                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($endusers as $ed)
                                                <option {{ $purchase_order->enduser_id == $ed->id ? 'selected' : '' }}
                                                    value="{{ $ed->id }}">{{ $ed->asset_staff_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Deliver To: </label>
                                        <input type="text" name="deliver_to" value="{{ $purchase_order->deliver_to }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-4" id="edl">
                                    <div class="form-group">
                                        <label>Date Created: </label>
                                        <input type="text" name="po_number" readonly
                                            value="{{ $purchase_order->date_created }}" class="form-control"
                                            placeholder="{{ $date_created }}">
                                    </div>
                                </div>

                            </div>


                            <br>



                        </div>
                        <!-- /.card-body -->


                        <div class="card-footer">
                            <!-- Save Draft Button -->
                            <button type="button" class="btn btn-success float-left" onclick="saveDraft()">Save
                                Draft</button>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary" style="margin-left:10px">Submit</button>
                        </div>

                        {{-- </form> --}}
                    </div>
                    @if (Auth::user()->hasRole('purchasing_officer') || Auth::user()->hasRole('authoriser'))
                        <div class="card-body" id="myDiv">
                            <div class="table-responsive">
                                @csrf
                                <table id="editable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Line#</th>
                                            <th>Description</th>
                                            <th>Part Number</th>
                                            <th>UoM</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            {{-- <th>Discount (%)</th> --}}
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order_parts as $ph)
                                            <tr>
                                                <td>{{ $ph->id }}</td>
                                                <td>{{ $ph->item->item_description ?? '' }}</td>
                                                <td>{{ $ph->item->item_part_number ?? '' }}</td>
                                                <td>
                                                    {{ $ph->item->item_uom ?? '' }}
                                                </td>
                                                <td>{{ $ph->quantity ?? '' }}</td>
                                                <td>{{ $ph->unit_price ?? '' }}</td>
                                                {{-- <td>{{ $ph->discount ?? '' }}</td> --}}
                                                <td>{{ $ph->sub_total ?? '' }}</td>
                                            </tr>
                                        @endforeach


                                    </tbody>
                                    <button class="btn btn-primary" style="float: right;"
                                        onclick="location.reload()";>Update</button>
                                </table>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        {{-- <tr>
                                            <th>Line#</th>
                                            <th>Description</th>
                                            <th>Part Number</th>
                                            <th>UoM</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Discount (%)</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>

                                        {{-- <tr>
                                            <td><label for="" readonly></label></td>
                                            <td><input type="text" name="description" class="form-control"
                                                    placeholder="Description"></td>
                                            <td><input type="text" name="part_number" class="form-control"
                                                    placeholder="Part Number"></td>
                                            <td>
                                                <select class="select form-select form-control" placeholder="uom"
                                                    id="uom" name="uom">
                                                    <option {{ old('uom') == 'Kilograms' ? 'selected' : '' }}
                                                        value="Kilograms">Kilograms </option>
                                                    <option {{ old('uom') == 'Meters' ? 'selected' : '' }} value="Meters">
                                                        Meters</option>
                                                    <option {{ old('uom') == 'Litres' ? 'selected' : '' }} value="Litres">
                                                        Litres</option>
                                                    <option {{ old('uom') == 'Pieces' ? 'selected' : '' }} value="Pieces">
                                                        Pieces </option>
                                                    <option {{ old('uom') == 'Kits' ? 'selected' : '' }} value="Kits">
                                                        Kits</option>
                                                    <option {{ old('uom') == 'Pair' ? 'selected' : '' }} value="Pair">
                                                        Pair</option>
                                                    <option {{ old('uom') == 'Bale' ? 'selected' : '' }} value="Bale">
                                                        Bale </option>
                                                    <option {{ old('uom') == 'Bottle' ? 'selected' : '' }} value="Bottle">
                                                        Bottle</option>
                                                    <option {{ old('uom') == 'Box' ? 'selected' : '' }} value="Box">Box
                                                    </option>
                                                    <option {{ old('uom') == 'Bucket' ? 'selected' : '' }} value="Bucket">
                                                        Bucket </option>
                                                    <option {{ old('uom') == 'Carton' ? 'selected' : '' }} value="Carton">
                                                        Carton</option>
                                                    <option {{ old('uom') == 'Drum' ? 'selected' : '' }} value="Drum">
                                                        Drum</option>
                                                    <option {{ old('uom') == 'Gallon' ? 'selected' : '' }} value="Gallon">
                                                        Gallon </option>
                                                    <option {{ old('uom') == 'Pack' ? 'selected' : '' }} value="Pack">
                                                        Pack</option>
                                                    <option {{ old('uom') == 'Rim' ? 'selected' : '' }} value="Rim">Rim
                                                    </option>
                                                    <option {{ old('uom') == 'Roll' ? 'selected' : '' }} value="Roll">
                                                        Roll </option>
                                                </select>

                                            </td>
                                            <td><input type="number" class="form-control" style="width: 100px"
                                                 name="quantity" id="aquantity"  placeholder="quantity"></td>
                                            <td><input type="text" id="aunit_price" class="form-control"  name="unit_price" placeholder="unit price"></td>
                                            <td><input type="text" class="form-control" style="width:100px"
                                                id="adiscount"    placeholder="discount"></td>
                                            <td><label for="amount" id="amount">Amount</label></td>
                                            <td><button type="submit" class="btn btn-primary" onclick="saveNewRow()">Save</button></td>
                                        </tr> --}}



                                    </tbody>

                                </table>

                                {{-- <div class="card-body" id="myDiv">
                                    <div class="table-responsive">
                                        <p style="margin-left: 800px;">Sub Total (Excluding Tax & Levies):
                                            {{ $grandtotal }}</p>
                                        <table id="tax_id_table" class="table table-bordered table-striped"
                                            style="font-size: 10px;">
                                           
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select data-placeholder="Choose..." name="tax_id"
                                                            id="tax_id"
                                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
                                                            class="select-search form-control">
                                                            <option value=""></option>
                                                            @foreach ($taxes as $sp)
                                                                <option data-rate="{{ $sp->rate }}"
                                                                    {{ $purchase_order->tax_id == $sp->id ? 'selected' : '' }}
                                                                    value="{{ $sp->id }}">{{ $sp->description }},
                                                                    {{ $sp->rate }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <p class="new_results"></p>
                                                    </td>
                                                    <td><button id="addRowBtn" class="btn btn-primary">Add Tax</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div>
                                            <p id="after_tax" style="margin-left:800px;"> </p>
                                           
                                        </div>
                                    </div>
                                </div> --}}

                                {{-- levies module --}}
                                <div class="card-body" id="myDiv">
                                    <div class="table-responsive">
                                      
                                        {{-- <table id="levy_id_table" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                    <th style="border: 1px solid #dee2e6;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select data-placeholder="Choose..." name="levy_id"
                                                            id="levy_id"
                                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
                                                            class="select-search livesearchtax form-control">
                                                            <option value=""></option>
                                                            @foreach ($levies as $lv)
                                                                <option data-rate="{{ $lv->rate }}"
                                                                    {{ $purchase_order->levy_id == $lv->id ? 'selected' : '' }}
                                                                    value="{{ $lv->id }}">{{ $lv->description }},
                                                                    {{ $lv->rate }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <p class="new_results2"></p>
                                                    </td>
                                                    <td><button id="addRowBtnLevy" class="btn btn-primary">Add
                                                            Levy</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div>
                                            <p id="after_levy" style="margin-left:800px;"> </p>
                                            <p id="after_tax_words" style="margin-left:800px;"> </p>
                                        </div> --}}
                                        <div class="form-group">
                                            <label>Created By: </label>
                                            @if (Auth::user()->hasRole('purchasing_officer'))
                                                <input type="text" name="created_by" style="width:40%"
                                                    value="{{ $purchase_order->created_by }}" class="form-control"
                                                    readonly>
                                            @else
                                                <input type="text" name="created_by" style="width:40%"
                                                    value="{{ $purchase_order->created_by }}" class="form-control"
                                                    readonly>
                                            @endif

                                        </div>
                                        <div class="form=group">
                                            <label for="">Notes</label>
                                            <input type="text" name="notes" style="width:40%"
                                                value="{{ $purchase_order->notes }}" class="form-control">
                                        </div>
                                    </div>
                                </div>




                            </div>

                        </div>
                        {{-- end of levies module --}}
                    @endif
                </form>
            </div>
        </div>


        <script>
            function saveDraft() {
                // Set action for saving draft
                document.getElementById("purchaseForm").action = "{{ route('spr_save_draft', $purchase_order->id) }}";

                // Submit the form
                document.getElementById("purchaseForm").submit();
            }

            function submitForm() {
                // Set action for submitting
                document.getElementById("purchaseForm").action =
                    "{{ route('spr_purchase_update', $purchase_order->id) }}";

                // Submit the form
                document.getElementById("purchaseForm").submit();
            }

            function saveNewRow() {
                // Set action for submitting
                document.getElementById("purchaseForm").action =
                    "{{ route('spr_purchase_update_row', $purchase_order->id) }}";

                // Submit the form
                document.getElementById("purchaseForm").submit();
            }
        </script>
        <script>
            // Write JavaScript to handle the addition of a new row
            $(document).ready(function() {
                $('#addRowBtn').click(function() {
                    // Define the structure of the new row
                    var newRow = `
                    <tr>
                        <td><select data-placeholder="Choose..." name="tax_id" id="tax_id"
    @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
    class="select-search form-control">
    <option value=""></option>
    @foreach ($taxes as $sp)
        <option data-rate="{{ $sp->rate }}" {{ $purchase_order->tax_id == $sp->id ? 'selected' : '' }}
            value="{{ $sp->id }}">{{ $sp->description }}, {{ $sp->rate }}</option>
    @endforeach
</select>
</td>
                        <td><p class="new_results"></p></td>
                        <td>
                                                <button class="btn btn-danger remove-row">Remove</button>
                                            </td>
                    </tr>
                `;
                    // Append the new row to the table
                    $('#tax_id_table tbody').append(newRow);
                    updateSumAndConvertToWords();
                });
            });
        </script>
        <script>
            // Write JavaScript to handle the addition of a new row levy
            $('#addRowBtnLevy').click(function() {
                var newRow = `
            <tr>
                <td>
                    <select data-placeholder="Choose..." name="levy_id" id="levy_id" class="select-search form-control">
                        <option value=""></option>
                        @foreach ($levies as $lv)
                            <option data-rate="{{ $lv->rate }}" value="{{ $lv->id }}">{{ $lv->description }}, {{ $lv->rate }}</option>
                        @endforeach
                    </select>
                </td>
                <td><p class="new_results2"></p></td>
                <td><button class="btn btn-danger remove-row-levy">Remove</button></td>
            </tr>
        `;
                $('#levy_id_table tbody').append(newRow);
                updateLevySum();
                updateSumAndConvertToWords();
            });
        </script>
        <script>
            $(document).ready(function() {
                // Event listener for changes in the content of #after_tax
                $('#after_tax').bind("DOMSubtreeModified", function() {
                    // Log the updated value of #after_tax
                    console.log("Updated after_tax value:", $(this).text());
                });
            });
        </script>
        <script>
            // Function to calculate amount
            function calculateAmount() {
                // Get quantity, unit price, and discount values
                var quantity = parseFloat(document.getElementById('aquantity').value);
                var unitPrice = parseFloat(document.getElementById('aunit_price').value);
                var discount = parseFloat(document.getElementById('adiscount').value);
        
                // Log the values for debugging
                console.log('Quantity:', quantity);
                console.log('Unit Price:', unitPrice);
                console.log('Discount:', discount);
        
                // Check if any of the values are NaN
                if (isNaN(quantity) || isNaN(unitPrice)) {
                    // If any value is NaN, set amount to 0 and return
                    console.log('Invalid input values.');
                    document.getElementById('amount').innerText = '0.00';
                    return;
                }
                
                // Calculate total amount
                var totalAmount = quantity * unitPrice;
        
                // Check if discount is provided and valid
                if (!isNaN(discount)) {
                    // Apply discount if provided
                    if (discount > 0 && discount <= 100) {
                        totalAmount -= (totalAmount * discount) / 100; // Apply discount percentage
                    } else {
                        console.log('Invalid discount percentage.');
                    }
                }
                
                // Update the amount label
                document.getElementById('amount').innerText = totalAmount.toFixed(2); // Adjust decimal places as needed
                console.log('Total amount:', totalAmount);
            }
        
            // Call the calculateAmount function when quantity, unit price, or discount changes
            document.getElementById('aquantity').addEventListener('input', calculateAmount);
            document.getElementById('aunit_price').addEventListener('input', calculateAmount);
            document.getElementById('adiscount').addEventListener('input', calculateAmount);
        </script>
        


 
        <script>
            $(document).ready(function() {
                // Event listener for when a new supplier is selected
                $('#tax_id_table').on('change', 'select[name="tax_id"]', function() {
                    // Get the selected supplier's rate
                    var rate = $(this).find(':selected').data('rate');

                    // Parse the rate as a float
                    rate = parseFloat(rate);
                    console.log(rate);

                    // Get the grandtotal
                    var grandtotal = parseFloat('{{ $grandtotal }}');
                    console.log(grandtotal);

                    // Calculate the percentage of grandtotal based on the rate
                    var percentage = (rate / 100) * grandtotal;

                    // Update the content of the <p class="new_results">
                    $(this).closest('tr').find('.new_results').text(percentage.toFixed(2));
                    console.log(percentage); // Adjust the number of decimal places as needed

                    // Call the function to update the sum
                    updateSum();
                    updateSumAndConvertToWords();
                });

                // Function to calculate and update the sum
                function updateSum() {
                    var grandTotal = parseFloat('{{ $grandtotal }}'); // Get the initial grand total
                    var newResultsSum = 0; // Initialize the sum of new results
                    $('.new_results').each(function() {
                        // Iterate through each <p class="new_results">
                        var value = parseFloat($(this).text()); // Get the value as float
                        if (!isNaN(value)) { // Check if the value is a valid number
                            newResultsSum += value; // Add the value to the sum
                        }
                    });
                    // Calculate the total sum
                    var totalSum = grandTotal + newResultsSum;
                    // Update the content of <p id="after_tax"> with the total sum
                    $('#after_tax').text("Sub Total (including levies): " + totalSum.toFixed(
                        2)); // Adjust the number of decimal places as needed
                }

                // Call the function to update the sum when the page loads
                updateSum();

                // Event listener for when the content of <p class="new_results"> changes
                $(document).on('change', '.new_results', function() {
                    updateSum(); // Update the sum when the content changes
                });
            });
        </script>

        <script>
            // Function to calculate the sum of all column values
            function calculateGrandTotal() {
                var grandTotal = 0;
                // Iterate through each row of the table
                $('#tax_id_table tbody tr').each(function() {
                    // Get the values of the columns you want to sum
                    var quantity = parseFloat($(this).find('td:eq(4)').text());
                    var unitPrice = parseFloat($(this).find('td:eq(5)').text());
                    var discount = parseFloat($(this).find('td:eq(6)').text());
                    // Calculate the subtotal for the current row
                    var subtotal = (quantity * unitPrice) - ((quantity * unitPrice) * (discount / 100));
                    // Add the subtotal to the grand total
                    grandTotal += subtotal;
                });
                // Log the grand total
                console.log("Grand Total:", grandTotal.toFixed(2)); // Adjust the number of decimal places as needed
                return grandTotal;
            }

            // Call the function to calculate the grand total when the document is ready
            $(document).ready(function() {
                calculateGrandTotal();
            });
        </script>
        <script>
            $(document).ready(function() {
                // Event listener for when a new levy is selected
                $('#levy_id_table').on('change', 'select[name="levy_id"]', function() {
                    // Get the selected levy's rate
                    var rate = $(this).find(':selected').data('rate');
                    console.log("Selected Levy Rate:", rate);

                    // Parse the rate as a float
                    rate = parseFloat(rate);

                    // Get the Sub Total (including levies) value
                    var subTotalIncludingLevies = parseFloat($('#after_tax').text().replace(
                        'Sub Total (including levies): ', ''));
                    console.log("Sub Total (including levies):", subTotalIncludingLevies);

                    // Calculate the rate based on the Sub Total (including levies)
                    var calculatedRate = (rate * subTotalIncludingLevies) / 100;
                    console.log("Calculated Rate based on Sub Total (including levies):", calculatedRate);

                    // Update the content of the <p class="new_results2">
                    $(this).closest('tr').find('.new_results2').text(calculatedRate.toFixed(2));

                    // Call the function to update the sum
                    updateLevySum();
                    updateSumAndConvertToWords();
                });

                // Function to calculate and update the sum
                // Function to update levy sum
                function updateLevySum() {
                    var subTotalIncludingLevies = parseFloat($('#after_tax').text().replace(
                        'Sub Total (including levies): ', ''));
                    var calculatedRateSum = 0;
                    $('.new_results2').each(function() {
                        var value = parseFloat($(this).text());
                        if (!isNaN(value)) {
                            calculatedRateSum += value;
                        }
                    });
                    var totalLevySum = subTotalIncludingLevies + calculatedRateSum;
                    $('#after_levy').text("Grand Total (Tax & Levies Incl): " + totalLevySum.toFixed(2));
                }

                // Call the function to update the sum when the page loads
                updateLevySum();

                // Event listener for when the content of <p class="new_results2"> changes
                $(document).on('change', '.new_results2', function() {
                    updateLevySum(); // Update the sum when the content changes
                });
            });
        </script>
        <script>
            function updateLevySum() {
                var subTotalIncludingLevies = parseFloat($('#after_tax').text().replace('Sub Total (including levies): ', ''));
                var calculatedRateSum = 0;
                $('.new_results2').each(function() {
                    var value = parseFloat($(this).text());
                    if (!isNaN(value)) {
                        calculatedRateSum += value;
                    }
                });
                var totalLevySum = subTotalIncludingLevies + calculatedRateSum;
                $('#after_levy').text("Grand Total (Tax & Levies Incl): " + totalLevySum.toFixed(2));
            }

            // Call the function to update the sum when the page loads
            updateLevySum();
            $(document).ready(function() {
                // Event listener for the remove button click
                $('#tax_id_table').on('click', '.remove-row', function() {
                    // Remove the parent row of the clicked remove button
                    $(this).closest('tr').remove();

                    // Call the function to update the sum after removing the row
                    updateSum();
                    updateSumAndConvertToWords();
                });
            });

            $(document).ready(function() {
                // Event listener for the remove button click
                $('#levy_id_table').on('click', '.remove-row-levy', function() {
                    // Remove the parent row of the clicked remove button
                    $(this).closest('tr').remove();

                    // Call the function to update the sum after removing the row
                    updateSum();
                    updateLevySum();
                    updateSumAndConvertToWords();
                });
            });

            // Function to calculate and update the sum
            function updateSum() {
                var grandTotal = parseFloat('{{ $grandtotal }}'); // Get the initial grand total
                var newResultsSum = 0; // Initialize the sum of new results
                $('.new_results').each(function() {
                    // Iterate through each <p class="new_results">
                    var value = parseFloat($(this).text()); // Get the value as float
                    if (!isNaN(value)) { // Check if the value is a valid number
                        newResultsSum += value; // Add the value to the sum
                    }
                });
                // Calculate the total sum
                var totalSum = grandTotal + newResultsSum;
                // Update the content of <p id="after_tax"> with the total sum
                $('#after_tax').text("Sub Total (including levies): " + totalSum.toFixed(
                    2)); // Adjust the number of decimal places as needed
            }
        </script>


        <script>
            // Function to convert numbers to words
            function numberToWords(number) {
                // Split the number into integer and decimal parts
                var parts = number.toString().split(".");
                var integerPart = parseInt(parts[0]);
                var decimalPart = parts.length > 1 ? parseInt(parts[1]) : 0;

                var ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
                var teens = ['', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
                    'nineteen'
                ];
                var tens = ['', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];

                // Function to convert less than thousand to words
                function convertLessThanThousand(n) {
                    if (n >= 100) {
                        return ones[Math.floor(n / 100)] + ' hundred ' + convertLessThanThousand(n % 100);
                    } else if (n >= 20) {
                        return tens[Math.floor(n / 10)] + ' ' + ones[n % 10];
                    } else if (n >= 10) {
                        return teens[n % 10];
                    } else {
                        return ones[n];
                    }
                }

                // Function to convert to words recursively
                function convertToWords(n) {
                    if (n == 0) return '';
                    var words = convertLessThanThousand(n);
                    return words;
                }

                // Convert integer part to words
                var integerWords = convertToWords(integerPart);

                // Convert decimal part to words
                var decimalWords = '';
                if (decimalPart > 0) {
                    decimalWords = ' and ' + convertToWords(decimalPart) + '';
                }

                // Concatenate integer and decimal words
                var result = integerWords + '' + decimalWords;

                return result.trim();
            }
        </script>

        <script>
            // Function to update the sum and convert it to words
            function updateSumAndConvertToWords() {
                var totalSum = parseFloat($('#after_levy').text().replace('Grand Total (Tax & Levies Incl): ', ''));

                // Convert the total sum to words
                var totalSumWords = numberToWords(totalSum);

                // Update the content of <p id="after_tax_words"> with the total sum in words
                $('#after_tax_words').text("Grand Total in Words: " + totalSumWords);
            }

            // Call the function to update the sum and convert it to words when the page loads
            $(document).ready(function() {
                updateSumAndConvertToWords();
            });

            // Call the function to update the sum and convert it to words whenever the content changes
            $(document).on('change', '.new_results', function() {
                updateSumAndConvertToWords();
            });
        </script>


        <script type="text/javascript">
            $('.livesearch').select2({
                placeholder: 'Select Parts',
                ajax: {
                    url: '/ajax-autocomplete-part',
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
            $('#supplier_id').select2({
                placeholder: 'Select Supplier',
                ajax: {
                    url: '/ajax-autocomplete-search',
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
            $('.livesearchtax').select2({
                placeholder: 'Select Tax',
                ajax: {
                    url: '/ajax-autocomplete-tax',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.description,
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
            $('#enduser_id').select2({
                placeholder: 'Select Enduser',
                ajax: {
                    url: '/ajax-autocomplete-enduser',
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
            $('.livesearch2').select2({
                placeholder: 'Select Site',
                ajax: {
                    url: '/ajax-autocomplete-site',
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
            $('.livesearch3').select2({
                placeholder: 'Select Location',
                ajax: {
                    url: '/ajax-autocomplete-location',
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
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': $("input[name=_token]").val()
                    }
                });

                $('#editable').Tabledit({
                    url: '{{ route('spr_porder_action') }}',
                    dataType: "json",
                    columns: {
                        identifier: [0, 'id'],
                        editable: [
                            [3, 'uom',
                                '{"Kilograms": "Kilograms", "Meters": "Meters","Litres":"Litres","Pieces": "Pieces", "Kits": "Kits","Pair":"Pair","Bale": "Bale", "Bottle": "Bottle","Box":"Box","Bucket": "Bucket", "Cartn": "Cartn","Drum":"Drum","Gallon": "Gallon", "Pack": "Pack","Rim":"Rim","Roll": "Roll"}'
                            ],
                            [4, 'quantity'],

                            [5, 'unit_price'],
                            [6, 'discount']

                        ]
                    },

                    restoreButton: false,
                    onSuccess: function(data, textStatus, jqXHR) {
                        if (data.action == 'delete') {
                            $('#' + data.id).remove();
                        }
                    }
                });

            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
            integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}
    </body>

    </html>
@endsection
