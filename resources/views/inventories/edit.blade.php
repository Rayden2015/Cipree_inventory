@extends('layouts.admin2')
@section('content')

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Edit GRN</title>
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




            <link rel="stylesheet" href="{{ asset('/css/select2.min.css') }}">
            <style>
                h2 {
                    color: white;
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
            </style>
        </head>

        <body>
            <div>

                <br>
            </div>

            <div class="card">

                <div class="card-header">

                    <a href="{{ route('inventories.index') }}" class="btn btn-primary float-right">Back</a>
                </div>
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

                <div class="card-body">

                    <form action="{{ route('inventories.update', $inventory->id) }}" method="POST"
                        enctype="multipart/form-data">
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
                                <h3 class="card-title">Edit Inventory</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3" id="edl">
                                        <div class="form-group">
                                            <label>Supplier: </label>
                                            <select data-placeholder="Choose..." name="supplier_id" id="supplier_id"
                                                class="select-search form-control">
                                                <option value=""></option>
                                                @foreach ($suppliers as $sp)
                                                    <option {{ $inventory->supplier_id == $sp->id ? 'selected' : '' }}
                                                        value="{{ $sp->id }}">{{ $sp->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Enduser: </label>
                                            <select data-placeholder="Choose..." name="enduser_id" id="enduser_id"
                                                class="select-search form-control">
                                                <option value=""></option>
                                                @foreach ($endusers as $ed)
                                                    <option {{ $inventory->enduser_id == $ed->id ? 'selected' : '' }}
                                                        value="{{ $ed->id }}">{{ $ed->asset_staff_id }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Waybill Reference: </label>
                                            {{-- <select class="select form-control" id="trans_type" name="trans_type" data-fouc
                                            data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $inventory->trans_type == 'Specific Task Order (STP)' ? 'selected' : '' }}
                                                value="Specific Task Order (STP)">Specific Task Order (STP)</option>
                                            <option {{ $inventory->trans_type == 'Stock Replenishment Order (SRO)' ? 'selected' : '' }}
                                                value="Stock Replenishment Order (SRO)">Stock Replenishment Order (SRO)</option>
                                        </select> --}}
                                            <input type="text" value="{{ $inventory->waybill }}" name="waybill"
                                                class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Billing Currency: </label>
                                            <select class="select form-control" id="billing_currency"
                                                name="billing_currency" data-fouc data-placeholder="Choose..">

                                                <option value=""></option>

                                                <option {{ $inventory->billing_currency == 'Dollar' ? 'selected' : '' }}
                                                    value="Dollar">Dollar</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Type of Purchase: </label>
                                            <select class="select form-control" id="trans_type"
                                                name="trans_type" data-fouc data-placeholder="Choose..">

                                                <option value=""></option>

                                                <option {{ $inventory->trans_type == 'Direct Purchase' ? 'selected' : '' }}
                                                    value="Direct Purchase">Direct Purchase</option>

                                                    <option {{ $inventory->trans_type == 'Stock Purchase' ? 'selected' : '' }}
                                                        value="Stock Purchase">Stock Purchase</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>

                                <div class="row">
                                    <div class="col-md-3" id="edl">
                                        <div class="form-group">
                                            <label>Invoice Reference: </label>
                                            <input type="text" value="{{ $inventory->invoice_number }}"
                                                name="invoice_number" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>PO Number: </label>
                                            <input type="text" value="{{ $inventory->po_number }}" name="po_number"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Date: </label>
                                            <input type="date" value="{{ $inventory->date }}" name="date" readonly
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>GRN Number: </label>
                                            <input type="text" value="{{ $inventory->grn_number }}" name="grn_number"
                                                readonly class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-md-2" id="edl">
                                        <div class="form-group">
                                            <label>Exchange Rate: </label>
                                            <input type="text" value="{{ $inventory->exchange_rate }}"
                                                name="exchange_rate" class="form-control">
                                        </div>
                                    </div>




                                </div>


                                <div class="row">
                                    <div class="col-md-3" id="edl">
                                        <div class="form-group">
                                            <label>Manual Remarks: </label>
                                            <input type="text" value="{{ $inventory->manual_remarks }}"
                                                name="manual_remarks" class="form-control">
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                    </form>
                </div>
                <div class="card-body" id="myDiv">
                    <div class="table-responsive">


                        <table id="editable" class="table table-bordered table-striped data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>


                                    <th style="width:20px;">Quantity</th>

                                    <th>Unit Cost</th>
                                    <th>Amount</th>
                                    <th style="width:20px;">Discount %</th>

                                    <th> Location</th>
                                    <th>Edit</th>

                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($inventory_items))
                                    @foreach ($inventory_items as $ph)
                                        <tr>
                                            <td>{{ $ph->id ?? '' }}</td>
                                            <form action="{{ route('inventories.update_inventory_item', $ph->id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf

                                                <td>

                                                    <select data-placeholder="Choose..." name="item_id" id="item_id"
                                                        class="select-search form-control">
                                                        <option value=""></option>
                                                        @foreach ($items as $itm)
                                                            <option {{ $ph->item_id == $itm->id ? 'selected' : '' }}
                                                                value="{{ $itm->id }}">
                                                                {{ $itm->item_description . ' ' . $itm->item_stock_code }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </td>


                                                <td><input value="{{ $ph->quantity ?? '' }}" type="number"
                                                        name="quantity" class="form-control"></td>
                                                <td>

                                                    @if ($inventory->billing_currency == 'Dollar')
                                                        <input value="{{ $ph->unit_cost_exc_vat_gh ?? '' }}"
                                                            type="text" name="unit_cost_exc_vat_gh"
                                                            class="form-control">
                                                    @else
                                                        <input value="{{ $ph->unit_cost_exc_vat_gh ?? '' }}"
                                                            type="text" name="unit_cost_exc_vat_gh"
                                                            class="form-control">
                                                    @endif


                                                </td>
                                                <td>

                                                    @if ($inventory->billing_currency == 'Dollar')
                                                        <input value="{{ 'USD ' . $ph->amount ?? '' }}" type="text"
                                                            name="amount" readonly class="form-control">
                                                    @else
                                                        <input value="{{ $ph->amount ?? '' }}" type="text"
                                                            name="amount" readonly class="form-control">
                                                    @endif
                                                </td>
                                                <td><input value="{{ $ph->discount ?? '' }}" type="text"
                                                        name="discount" class="form-control"></td>

                                                <td>
                                                    <select data-placeholder="Choose..." name="location_id"
                                                        id="location_id" class="select-search form-control">
                                                        <option value=""></option>
                                                        @foreach ($locations as $na)
                                                            <option {{ $ph->location_id == $na->id ? 'selected' : '' }}
                                                                value="{{ $na->id }}">{{ $na->name }}</option>
                                                        @endforeach
                                                    </select>

                                                </td>
                                                <td>
                                                    @if (Auth::user()->hasRole('store_officer'))
                                                    <button type="submit" onclick="editable()"
                                                    class="btn btn-primary">Submit</button>
                                                    @else
                                                       
                                                    @endif
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                            {{-- <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div> --}}
                        </table>
                        {{ $inventory_items->links() }}
                        {{-- <tr>
            
                                        <td class="font-size:40px;">Total:</td>
            
                                        <td style="font-weight:bold;">GHC {{ $grandtotal }}</td>
            
                                    </tr> --}}
                        {{-- </form> --}}
                    </div>
                </div>



                <!-- /.card-body -->



            </div>

            </div>

            </div>
        </body>
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
            $('.livesearch').select2({
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
            $('.livesearch').select2({
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
            $('.livesearch6').select2({
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
        <script>
            document.getElementById("dynamicInput").onload = addInput('dynamicInput');

            function addInput(divName) {
                var newDiv = document.createElement('div');
                var selectHTML = "";
                selectHTML = "<select>";
                for (var row = 0; row < 2; row++) {
                    for (var col = 0; col < 5; c++) {
                        var MyVal = document.getElementById("mytable").rows[row].cells[col].innerHTML;
                        selectHTML += "<option value='" + MyVal + "'>" + MyVal + "</option>";
                    }
                }
                selectHTML += "</select>";
                newDiv.innerHTML = selectHTML;
                document.getElementById(divName).appendChild(newDiv);
            }
        </script>
        <script>
            function editable() {
                $('#editable').load(document.URL + ' #editable tr');
                // document.getElementById("editable").innerHTML = "Hello World";
                // event.preventDefault();
            }
        </script>

        {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
            integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        {{-- <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script> --}}
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}


        </html>
    
@endsection
