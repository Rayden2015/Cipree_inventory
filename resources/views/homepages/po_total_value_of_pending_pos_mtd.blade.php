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

        <title>Document</title>

    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            {{-- <p>
                <a href="{{ route('inventories.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
            </p> --}}
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Total Value of Approved POs - MTD</h3>
                <br>
                {{-- <form action="{{ route('stores.supply_history_search_item') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search Description or Part Number, Enduser or Stock Code"
                            aria-describedby="basic-addon2" name="search">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
                <br>
                <form action="supply_history_search" method="GET">
                    <div class="row">
                        <div class="input-group mb-3">
                            <input type="date" class="form-control" name="start_date" required>
                            <input type="date" class="form-control" name="end_date" required>
                            <button class="btn btn-primary ml-2" type="submit">Generate</button>

                            <a href="{{ route('stores.supply_history') }}" class="btn btn-primary pull-left ml-4">
                                <h6>Reset</h6>
                            </a>

                         
                        </div>
                    </div>
                </form> --}}
            </div>    
                <!-- /.card-header -->

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th> Date</th>
                                <th>Description</th>
                                <th>Part Number</th>
                                <th>Priority</th>
                                <th>Remarks</th>
                                <th>Quantity</th>

                              


                            </tr>
                        </thead>
                        @forelse ($po_total_value_of_pending_pos_mtd as $in)
                            <tbody>
                                <tr>
                                    <td>{{ $in->id }}</td>

                                    <td>{{ date('d-m-Y (H:i)', strtotime($in->created_at)) }}</td>
                                    <td>{{ $in->description ?? ' ' }}</td>
                                    <td>{{ $in->part_number ?? ' ' }}</td>
                                    <td>{{ $in->priority ?? ' ' }}</td>
                                    <td>{{ $in->remarks ?? ' ' }}</td>
                                    <td>{{ $in->quantity ?? '' }}</td>


                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Item not available!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
                {{ $po_total_value_of_pending_pos_mtd->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>

        </div>


    </body>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
