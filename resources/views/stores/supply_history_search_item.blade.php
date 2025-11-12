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

        <title>Supply History Search</title>

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
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Supply Date</th>
                            <th>SR Number</th>
                            <th>GRN Number</th>
                            <th>Description</th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>End User</th>
                            <th>Location</th>
                            @if (Auth::user()->hasRole('Super Admin'))
                                <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($total_cost_of_parts_within_the_month as $in)
                            <tr>
                                <td>{{ $in->id }}</td>
                                <td>{{ date('d-m-Y (H:i)', strtotime($in->delivered_on)) }}</td>
<!-- SR Number -->
<td>
    @if ($in->sorder_id)
        <a href="{{ route('sorders.store_list_view', ['id' => $in->sorder_id]) }}">
            {{ $in->request_number ?? $in->delivery_reference_number ?? 'Not Found' }}
        </a>
    @else
        Not Found
    @endif
</td>

<!-- GRN Number -->
<td>
    @if ($in->inventory_id && $in->grn_number)
        <a href="{{ route('inventories.show', ['inventory' => $in->inventory_id]) }}">
            {{ $in->grn_number }}
        </a>
    @else
        Not Found
    @endif
</td>


                                <td>{{ $in->item_description ?? ' ' }}</td>
                                <td>{{ $in->item_part_number ?? ' ' }}</td>
                                <td>{{ $in->item_stock_code ?? ' ' }}</td>
                                <td>{{ $in->qty_supplied ?? '' }}</td>
                                <td>{{ $in->sub_total ?? '' }}</td>
                                <td>{{ $in->enduser->asset_staff_id ?? 'Not Set' }}</td>
                                <td>{{ $in->location->name ?? 'Not Set' }}</td>
                                @if (Auth::user()->hasRole('Super Admin'))
                                    <td>
                                        <form action="{{ route('sorderpart_delete', $in->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                {{ $total_cost_of_parts_within_the_month->links() }}
            </div>
        </div>

        <!-- jQuery -->
        <script src="{{ asset('/assets/plugins/jquery/jquery.min.js') }}"></script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/jszip/jszip.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
        <script src="{{ asset('/assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
        <!-- AdminLTE App -->
        <script src="{{ asset('/assets/dist/js/adminlte.min.js') }}"></script>
        <!-- Toastr -->
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>

    </body>

    </html>
@endsection
