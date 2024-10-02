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
                <h3 class="card-title">Items List at {{ Auth::user()->site->name }}</h3> <br>
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('exportItemsListSite') }}" class="btn btn-success mr-2">Export Excel</a>
                    <a href="{{ route('downloadItemsListPdf') }}" class="btn btn-danger">Export PDF</a>
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
                            <th>Unit Cost</th>
                            <th>Amount</th>
                            <th>Age</th>
                            <th>Location</th>
                            <th>Puchase Type</th>
                            <th>GRN Number</th>
                            <th>PO Number</th>
                            <th>Supplier</th>

                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($items as $rq)
                            <tr>
                               
                                <td>{{ $rq->item->item_description ?? '' }}</td>
                                <td>{{ $rq->item->item_part_number ?? '' }}</td>
                                <td>{{ $rq->item->item_stock_code ?? '' }}</td>
                                <td>{{ $rq->item->stock_quantity ?? '' }}</td>
                                <td>{{ $rq->unit_cost_exc_vat_gh ?? '' }}</td>
                                <td>{{ $rq->amount ?? '' }}</td>
                                <td>
                                    @if ($rq->created_at)
                                        {{ \Carbon\Carbon::parse($rq->created_at)->diffInDays(now()) }} days
                                    @else
                                        N/A
                                    @endif
                                </td>
                                
                                <td>{{ $rq->location->name ?? '' }}</td>
                                <td>{{ $rq->trans_type ?? '' }}</td>
                                <td>{{ $rq->grn_number ?? '' }}</td>
                                <td>{{ $rq->po_number ?? '' }}</td>
                                <td>{{ $rq->supplier->name ?? '' }}</td>
                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Data Not Found!</td>
                            </tr>
                        @endforelse


                    </tbody>
                </table>
            </div>
            {{-- {{ $items->links('pagination::bootstrap-4') }} --}}
            {{ $items->count() }}
            <!-- /.card-body -->
        </div>



    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
