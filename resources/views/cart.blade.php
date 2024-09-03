@extends('layouts.admin')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cart</title>

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    {{-- <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> --}}
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
@section('content')
    <table id="cart" style="width: 90%;" class="table table-hover table-condensed">
        <thead>
            <tr>
                <th style="width:40%">Description</th>
                <th style="width:50%">Part Number</th>
                <th style="width:50%">Stock Code</th>
                <th style="width:8%">UoM</th>
                <th style="width:50%">Unit Cost</th>
                {{-- <th style="width:50%">Location</th> --}}
                {{-- <th style="width:10%">Price</th> --}}
                <th style="width:8%">Quantity</th>


                {{-- <th style="width:22%" class="text-center">Subtotal</th> --}}
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
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <strong>Success!</strong> <span>{{ Session::get('success') }}</span>

                                    </div>
                                @endif
                                @if (Session::has('errors'))
                                    <div class="alert alert-danger">
                                        <button type="button" class="close" data-dismiss="alert"
                                            aria-hidden="true">&times;</button>
                                        <strong>Oops, out of quantity!</strong> <span>{{ Session::get('danger') }}</span>

                                    </div>
                                @endif


                                <form role="form" enctype="multipart/form-data"
                                    action=@if (isset($category)) "{{ route('category.update', $category->id) }}" @else() "{{ route('sorders.store') }}" @endif
                                    method="post">
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
                                               
                                                <div class="form-row">

                                                    <div class="form-group col-md-4">

                                                        <label for="inputCity" class="col-form-label">Request
                                                            Number</label>
                                                        <input style="width:250px;" name="request_number"
                                                            value="{{ $request_number }}" type="text" readonly
                                                            class="form-control date-pick ml-3"
                                                            placeholder={{ $request_number }}>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label for="inputCity" class="col-form-label">Request Date</label>
                                                        <input name="request_date" value="{{ $request_date }}"
                                                            type="text" readonly class="form-control date-pick"
                                                            placeholder={{ $request_date }}>
                                                    </div>
                                                </div>

                                            </div>
                                            <br><br>
                                        </div>

                                    </div>

                                    <div class="col-md-11">
                                        <div class="form-group" id="rows bg-info"
                                            style="box-shadow: 0 25px 35px 0 lightgrey">

                                            @php $total = 0 @endphp
                                            @if (session('cart'))
                                                @foreach (session('cart') as $id => $details)
                                                    {{-- @php $total += $details['price'] * $details['quantity'] @endphp --}}

                                                    <tr data-id="{{ $id }}">
                                                        <td data-th="Product" style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    {{ $details['item_description'] }}</div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    {{ $details['item_part_number'] }}</div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    {{ $details['item_stock_code'] }}</div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    {{ $details['item_uom'] }}</div>

                                                            </div>
                                                        </td>
                                                        <td style="width:80%;">
                                                            <div class="row">
                                                                <div class="col-sm-3 hidden-xs">
                                                                    {{ $details['unit_cost_exc_vat_gh'] }}</div>

                                                            </div>
                                                        </td>

                                                        {{-- <td data-th="location">
                        <input type="text"  value="{{ $details['location->name'] ?? ''}}" class="form-control" />
                    </td> --}}
                                                        {{-- <td>{{ $details['location_id'] }}</td> --}}
                                                        {{-- <td data-th="Price">${{ $details['price'] }}</td> --}}
                                                        <td data-th="Quantity">
                                                            <input type="number" value="{{ $details['quantity'] }}"
                                                                class="form-control quantity update-cart" />
                                                        </td>

                                                        {{-- <td data-th="Subtotal" class="text-center">${{ $details['price'] * $details['quantity'] }}</td> --}}
                                                        <td class="actions" data-th="">
                                                            <button
                                                                class="btn btn-danger btn-sm remove-from-cart">X</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
        </tbody>

        <tfoot>
            {{-- <tr>
            <td colspan="5" class="text-right"><h3><strong>Total ${{ $total }}</strong></h3></td>
        </tr> --}}
            <br>

            <tr>
                <td colspan="5" class="text-left">
                    {{-- <div class="form-group col-md-4">
                        <label for="inputCity" class="col-form-label">Enduser </label>
                        <select class="livesearch3 form-control p-3" required name="enduser_id"></select>

                    </div> --}}
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
                    {{-- <a href="{{ url('/') }}" class="btn btn-warning"><i class="fa fa-angle-left"></i> Continue Shopping</a> --}}
                    <button type="submit" class="btn btn-success float-right">Checkout</button>
                </td>

            </tr>

        </tfoot>


        </form>
    </table>

    </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(".update-cart").change(function(e) {
            e.preventDefault();

            var ele = $(this);

            $.ajax({
                url: '{{ route('update.cart') }}',
                method: "patch",
                data: {
                    _token: '{{ csrf_token() }}',
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
                    url: '{{ route('remove.from.cart') }}',
                    method: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}',
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
@endsection
