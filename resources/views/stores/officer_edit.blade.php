@extends('layouts.admin2')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Stock Requisition — Process</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        {{--  --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
        {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> --}}
        {{-- <script src="https://markcell.github.io/jquery-tabledit/assets/js/tabledit.min.js"></script> --}}
        {{--  --}}
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" type="text/css"
            href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.0.0-alpha1/css/bootstrap.min.css">
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
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

            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Stock Requisition Processing</h2>

                <a href="{{ route('stores.store_officer_lists') }}" class="btn btn-primary float-right">Back</a>
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

                <form action="{{ route('stores.store_officer_update', $sorder->id) }}" method="POST"
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
                            <h3 class="card-title">Process Stock Requisition</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6" id="edl">

                                    <div class="form-group">
                                        <label>Supplier Reference Number: </label>
                                        <input type="text" required value="{{ $sorder->delivery_reference_number }}"
                                            readonly name="delivery_reference_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6" id="edl">
                                    <div class="form-group">
                                        <label>Enduser: </label>
                                        <select data-placeholder="Choose..." name="enduser_id" id="enduser_id"
                                            @unlessrole('purchasing_officer|authoriser') readonly @endunlessrole
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($endusers as $ed)
                                                <option {{ $sorder->enduser_id == $ed->id ? 'selected' : '' }}
                                                    value="{{ $ed->id }}">{{ $ed->asset_staff_id }} — {{ $ed->name_description ?? $ed->name ?? '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>


                            <br>

                            {{-- @endrole --}}
                            <div class="row">


                              
                                 @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Supplied By: </label>
                                            <input type="text" value="{{ $sorder->user->name ?? '' }}"
                                                name="delivered_by" readonly class="form-control">
                                        </div>
                                    </div>
                                @endif
                              
                                 @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Supplied Date: </label>
                                            <input type="text" value="{{ $sorder->delivered_on ? \Carbon\Carbon::parse($sorder->delivered_on)->format('d-m-Y (H:i)') : '--' }}"
                                                readonly name="delivered_on" class="form-control">
                                        </div>
                                    </div>
                                @endif
                                @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Supplied To: </label>
                                            <input type="text" value="{{ old('supplied_to', $sorder->supplied_to ?? '') }}"
                                                name="supplied_to" required class="form-control" placeholder="Enter recipient/site">
                                        </div>
                                    </div>
                                @endif
                            </div>




                        </div>
                        <!-- /.card-body -->

                        @foreach ($sorder_parts as $ph)
                            @if ($sorder->edited_at == '1')
                                <div class="card-footer">
                                    {{-- <label for="" class="label label-primary float-left" style="width:30%; height:100%;">Transaction Completed</label> --}}
                                    <span class="badge badge-success p-2">Transaction Completed</span>
                                </div>
                            @break

                            {{-- @elseif ($sorder->delivery_reference_number != null )
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div> --}}
                        @elseif ($ph->qty_supplied < -1)
                        @break;
                        <div class="card-footer">
                            <div class="alert alert-warning mb-0">Enter the quantity to be supplied before finalizing.</div>
                        </div>
                    @elseif($ph->qty_supplied > -1)
                        @if ($loop->last)
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">Finalize Supply</button>
                                {{-- <label for="">input qty field</label> --}}
                            </div>
                        @endif
                    @endif
                @endforeach
        </form>
    </div>
    @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
        <div class="card-body" id="myDiv">
            <div class="table-responsive">
                @if (session('message'))
                    <div class="alert alert-danger">{{ session('message') }}</div>
                @endif
                {{-- <label for="" style="font-size:15px;" class="label label-danger float-right">Input Qty
                        Supplied To Submit Order </label> --}}
                <table id="editable" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Quantity Supplied</th>
                            <th>Remarks</th>
                            <th>Unit Price</th>
                            <th>Sub Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sorder_parts as $ph)
                            <tr>
                                <td>{{ $ph->id }}</td>
                                <td>{{ $ph->item_details->item_description ?? '' }}</td>
                                <td>{{ $ph->item_details->item_part_number ?? $ph->item_details->item_stock_code ?? '' }}</td>
                                <td><input value="{{ old('location', $ph->item_parts->location->name ?? '') }}"
                                        style="width:120px;" type="text" name="location" readonly
                                        class="form-control"></td>
                                <form action="{{ route('stores.sorder_update', $ph->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <td><input value="{{ old('quantity', $ph->quantity) }}" style="width:60px;"
                                            type="text" name="quantity" class="form-control" readonly></td>

                                    <td><input value="{{ old('qty_supplied', $ph->qty_supplied) }}"
                                            style="width:80px;" type="number" name="qty_supplied"
                                            class="form-control" min="0" max="{{ $ph->quantity }}"></td>
                                    <td><input value="{{ old('remarks', $ph->remarks) }}" style="width:120px;"
                                            type="text" name="remarks" readonly class="form-control"></td>
                                    <td>{{ $ph->unit_price ?? '' }}</td>
                                    <td>{{ $ph->sub_total ?? '' }}</td>
                                    @if ($sorder->edited_at == '1')
                                        {{-- <td> <button type="submit" class="btn btn-primary" >Submit</button></td> --}}
                                    @else
                                        <td> <button type="submit" class="btn btn-primary">Submit</button></td>
                                    @endif
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if (Auth::user()->hasRole('store_officer') || Auth::user()->hasRole('store_assistant'))
    <div class="card-footer">
        <form action="{{ route('stores.update_manual_remarks',$sorder->id) }}" method="post">
            @method('Put')
            @csrf
            <label for="manual_remarks">Remarks</label> <br>
            <input type="text" name="manual_remarks" id="manual_remarks" style="width:30%"
                class="form-control" value="{{ $sorder->manual_remarks }}"> <br>
            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
    @endif
</div>

</div>
</body>
@if (auth()->user()->roles->contains('name', 'store_officer'))
<style>
    /*Select2 ReadOnly Start*/
    select[readonly].select2-hidden-accessible+.select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
        display: none;
    }
</style>
@endif

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
            url: '{{ route('stores.action') }}',
            dataType: "json",
            columns: {
                identifier: [0, 'id'],
                editable: [
                    [4, 'quantity'],
                    [5, 'remarks'],
                    [6, 'unit_price'],

                ]
            },

            restoreButton: false,
            buttons: {
                delete: {
                    class: 'btn btn-sm btn-danger',
                    html: '<span class="glyphicon glyphicon-trash"></span> &nbsp',
                    action: 'delete'
                },
                confirm: {
                    class: 'btn btn-sm btn-default',
                    html: '<h6>click to del? </h6>'
                }
            },

        });

    });
</script>
<script>
    var form = document.getElementById("formId");
    var opTag = document.getElementById("opTag");

    function submitForm(event) {
        event.preventDefault();
        form.style.display = "none";
        opTag.innerHTML = "<b>Form submit successful</b>";
    }
    form.addEventListener('submit', submitForm);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
{!! Toastr::message() !!}

</html>
@endsection
