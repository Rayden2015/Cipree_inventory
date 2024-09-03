@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="https://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('/assets/plugins/fontawesome-free/css/all.min.css') }}">
        <!-- DataTables -->
        <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('/assets/dist/css/adminlte.min.css') }}">

        <title>Supply History</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('inventories.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
            </p>
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Supply History</h3>
                <br>
                <form action="{{ route('stores.supply_history_search_item') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search Description or Part Number, Enduser or Stock Code" aria-describedby="basic-addon2" name="search">
                    </div>
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" name="start_date">
                        <input type="date" class="form-control" name="end_date">
                    </div>
                    <div class="input-group mb-3">
                        <button class="btn btn-secondary" type="submit">Search</button>
                        <a href="{{ route('stores.supply_history') }}" class="btn btn-primary pull-left ml-2">
                            <h6>Reset</h6>
                        </a>
                    </div>
                </form>

                {{-- <form action="{{ route('exportSearchResults') }}" method="GET">
                   
                           
                            <button class="btn btn-success ml-2" type="submit">Export Excel</button>
                     
                </form> --}}
            </div>
            <!-- /.card-header -->
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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supply Date</th>
                            <th>SR Number</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Quantity</th>

                            <th>Cost</th>
                            <th>End User</th>
                            <th>Location</th>
                            {{-- @if (Auth::user()->role->name == 'store_officer')
                            <th>Delete</th>
                            @endif --}}
                            {{-- <th>Date</th> --}}

                            {{-- <th>View</th> --}}


                        </tr>
                    </thead>
                    @forelse ($total_cost_of_parts_within_the_month as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id }}</td>

                                <td>{{ date('d-m-Y (H:i)', strtotime($in->delivered_on ?? '')) }}</td>
                                <td>{{ $in->delivery_reference_number ?? '' }}</td>
                                <td>{{ $in->item_description ?? ' ' }}</td>
                                <td>{{ $in->item_part_number ?? ' ' }}</td>
                                <td>{{ $in->item_stock_code ?? ' ' }}</td>


                                <td>{{ $in->qty_supplied ?? '' }}</td>

                                <td>{{ $in->sub_total ?? '' }}</td>


                                <td>{{ $in->enduser->asset_staff_id ?? 'Not Set' }}</td>
                                <td>{{ $in->location->name ?? 'Not Set' }}</td>

                                {{-- @if (Auth::user()->role->name == 'store_officer')
                                    <td>
                                        <form action="{{ route('sorderpart_delete', $in->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                @endif --}}
                            @empty
                            <tr>

                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    @endforelse

                    </tr>

                    </tbody>

                </table>
            </div>
            {{ $total_cost_of_parts_within_the_month->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>

        </div>

        <script>
            document.getElementById('exportBtn').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Change the action attribute of the form
                document.getElementById('supplyHistoryForm').action = this.getAttribute('href');

                // Submit the form
                document.getElementById('supplyHistoryForm').submit();
            });
        </script>

    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
