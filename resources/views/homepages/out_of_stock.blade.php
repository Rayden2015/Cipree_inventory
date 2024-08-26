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

        <title>Document</title>
       
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
                <h3 class="card-title">Items out of stock</h3>
                <form action="{{ route('out_of_stock_search') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search Description "
                            aria-describedby="basic-addon2" name="search">
                        <div class="input-group-append">
                            <button class="btn btn-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- /.card-header -->

            <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Description </th>
                            <th>Part Number</th>
                            <th>Stock Code</th>
                            <th>Stock Qty</th>
                            <th>Reorder Level</th>
                            <th>Action</th>
                            {{-- <th>Last Updated</th> --}}
                            {{-- <th>Date</th> --}}                       
                            
                            {{-- <th>Edit</th>                            --}}

                        </tr>
                    </thead>
                    @forelse ($unstocked as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id }}</td>
                                <td>{{ $in->item_description ?? 'Not found in items table, modify inventory records' }}</td>
                                <td>{{ $in->item_part_number ?? 'Not found in items table, modify inventory records' }}</td>
                                <td>{{ $in->item_stock_code ?? 'Not found in items table, modify inventory records' }}</td>
                                <td>{{ $in->stock_quantity ?? '' }}</td>
                                <td>{{ $in->reorder_level ?? 'Null' }}</td>

                                <td> <a href="{{ route('dashboard.out_of_stock_view',$in->id) }}" class="btn btn-primary">View</a></td>
                                {{-- <td>{{ $in->inventory->trans_type ?? 'Null' }}</td> --}}

                                {{-- <td>{{ $in->unit_cost_exc_vat_gh ?? '' }}</td> --}}
                                {{-- <td>{{ date('d-m-Y (H:i)', strtotime($in->updated_at))}}</td> --}}
                                {{-- <td>{{ $in->date ?? '' }}</td>                              --}}
                               
                                {{-- <td>
                                    <a href="{{ route('inventories.inventory_history_edit', $in->id) }}" class="btn btn-success">Edit</a>

                                </td> --}}
                               


                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            <tr>
                <td>Count:</td>
                <td>{{ $unstocked->pluck('id')->count() }}</td>
            </tr>
            <!-- /.card-body -->
        </div>




    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
@endsection
