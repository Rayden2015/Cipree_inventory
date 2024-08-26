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
                @can('add-item')
                    <p>
                        <a href="{{ route('items.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
                    </p>
              @endcan
            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Items </h3>
                    <form action="{{ route('item_search') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control"
                                placeholder="Search Description, Part Number, Stock Code" aria-describedby="basic-addon2"
                                name="search">
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
                                <th>Description</th>
                                <th>Stock Code</th>
                                <th>Part Number</th>
                                <th>Created By</th>
                                <th>Qty In Stock</th>
                                {{-- <th>Amount</th> --}}
                                <th>View</th>

                                @can('edit-item')
                                    <th>Edit</th>
                              @endcan
                              

                            </tr>
                        </thead>
                        @forelse ($items as $ct)
                            <tbody>
                                <tr>
                                    <td>{{ $ct->id }}</td>
                                    <td>{{ $ct->item_description ?? '' }}</td>
                                    <td>{{ $ct->item_stock_code ?? '' }}</td>
                                    <td>{{ $ct->item_part_number ?? '' }}</td>
                                    <td>{{ $ct->user->name ?? '' }}</td>

                                    <td>{{ $ct->stock_quantity ?? '' }}</td>

                                    {{-- <td>{{ $ct->amount ?? '' }}</td> --}}
                                    @can('view-item')
                                    <td><a href="{{ route('items.show', $ct->id) }}" class="btn btn-primary">View</a></td>
                                    @endcan
                                    @can('edit-item')
                                        <td>
                                            <a href="{{ route('items.edit', $ct->id) }}" class="btn btn-success">Edit</a>

                                        </td>
                                  @endcan
                                  

                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
                {{ $items->links('pagination::bootstrap-4') }}
                <!-- /.card-body -->
            </div>




        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
  
@endsection
