@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
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

        <title>Items List</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>

        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-3">Items List &mdash; {{ $site_name ?? Auth::user()->site->name ?? 'Unknown' }}</h3>
                <form action="{{ route('items_list_site') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" 
                               placeholder="Search by Description, Part Number, Stock Code, Location, or Purchase Type" 
                               aria-describedby="basic-addon2" 
                               name="search"
                               value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">Search</button>
                            <a href="{{ route('items_list_site') }}" class="btn btn-primary ml-2">Reset</a>
                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('exportItemsListSite') }}" class="btn btn-success mr-2">Export Excel</a>
                    {{-- <a href="{{ route('downloadItemsListPdf') }}" class="btn btn-danger">Export PDF</a> --}}
                </div>
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
                            <th>Description</th>
                            <th data-priority="1">Part Number</th>
                            <th>Stock Code</th>
                            <th>Qty in Stock</th>
                            <th>Amount</th>
                            <th>Age (Days)</th>
                            <th>Location</th>
                            <th>Purchase Type</th>
                            <th>Site</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->item_description ?? '' }}</td>
                                <td>{{ $item->item_part_number ?? '' }}</td>
                                <td>{{ $item->item_stock_code ?? '' }}</td>
                                <td>{{ $item->stock_quantity ?? 0 }}</td>
                                <td>{{ $item->inventory_amount ?? '0.00' }}</td>
                                <td>
                                    @if ($item->inventory_created_at)
                                        {{ \Carbon\Carbon::parse($item->inventory_created_at)->diffInDays(now()) }} days
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $item->location_name ?? 'Not Set' }}</td>
                                <td>{{ $item->trans_type ?? 'N/A' }}</td>
                                <td><strong>{{ $site_name ?? Auth::user()->site->name ?? 'Unknown' }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="9">No items found for this site!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if ($items->total() > 0)
                            Showing {{ $items->firstItem() }} - {{ $items->lastItem() }} of {{ $items->total() }} items
                        @else
                            No items to display
                        @endif
                    </div>
                    <div>
                        {{ $items->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
