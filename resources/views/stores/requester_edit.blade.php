@extends('layouts.admin2')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Stock Edit</title>
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

            <div class="card-header">

                <a href="{{ route('stores.requester_store_lists') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">
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

                <form action="{{ route('stores.requester_store_update', $sorder->id) }}" method="POST"
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
                            <h3 class="card-title">Process Supply</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6" id="edl">
                                    <div class="form-group">
                                        <label>Enduser: </label>
                                        <select data-placeholder="Choose..." name="enduser_id" id="enduser_id"
                                            class="select-search form-control">
                                            <option value=""></option>
                                            @foreach ($endusers as $ed)
                                                <option {{ $sorder->enduser_id == $ed->id ? 'selected' : '' }}
                                                    value="{{ $ed->id }}">{{ $ed->asset_staff_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button class="btn btn-success">Submit</button>
                        </div>
        </form>
    </div>
    @if (Auth::user()->hasRole('requester'))
        <div class="card-body" id="myDiv">
            <div class="table-responsive">
                @if (session('message'))
                    <div class="alert alert-danger">{{ session('message') }}</div>
                @endif
                
                <table id="editable" class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Location</th>
                            <th>Quantity</th>
                          
                            <th>Unit Price</th>
                            <th>Sub Total</th> 
                            <th>Action</th>                         
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sorder_parts as $ph)
                            <tr>
                                <td>{{ $ph->id }}</td>
                                <td>{{ $ph->item_details->item_description ?? '' }}</td>
                                <td>{{ $ph->item_details->item_part_number ?? '' }}</td>
                                <td>{{ $ph->item_details->item_stock_code ?? '' }}</td>
                                <td><input value="{{ old('location', $ph->item_parts->location->name ?? '') }}"
                                        style="width:120px;" type="text" name="location" readonly
                                        class="form-control"></td>
                                <form action="{{ route('stores.requester_sorder_update',$ph->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf
                                    <td><input value="{{ old('quantity', $ph->quantity) }}" style="width:60px;"
                                            type="text" name="quantity" class="form-control"></td>

                                    <td>{{ $ph->unit_price ?? '' }}</td>
                                    <td>{{ $ph->sub_total ?? '' }}</td>
                                    {{-- <td><a href="{{ route('stores.requester_store_delete', $ph->id) }}"></a></td> --}}
                                    {{-- <td>

                                        <form action="{{ route('stores.requester_store_delete', $ph->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                  --}}
                                    <td> <button type="submit" class="btn btn-primary">Submit</button></td>  
                                </form>
                                 <td>

                                        <form action="{{ route('stores.requester_store_delete', $ph->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
    integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
{!! Toastr::message() !!}

</html>
@endsection
