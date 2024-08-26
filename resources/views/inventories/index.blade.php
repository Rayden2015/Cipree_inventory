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
            @can('add-grn')
                <p>
                    <a href="{{ route('inventories.create') }}" class="btn btn-primary mr-3 my-3">Add</a>
                </p>
            @endcan
        </div>


        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Goods Received Notes </h3>
                <form action="{{ route('inventory_home_search') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Search GRN or Waybill "
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
                           
                            <th>GRN Number</th>
                            <th>WB</th>
                            <th>Supplier </th>
                            <th>Invoice Number</th>
                            <th>Purchase Type</th>
                           <th>Modified By</th>
                            <th>Last Updated</th>


                            <th>View</th>
                            {{-- <th>Download</th> --}}
                            @can('edit-grn')
                                <th>Edit</th>
                                @endcan
                                @can('delete-grn')
                                <th>Delete</th>
                            @endcan
                        </tr>
                    </thead>
                    @forelse ($inventories as $in)
                        <tbody>
                            <tr>
                                <td>{{ $in->id }}</td>
                              
                                <td>{{ $in->grn_number ?? '' }}</td>
                                <td>{{ $in->waybill ?? '' }}</td>
                                <td>{{ $in->supplier->name ?? '' }}</td>
                                <td>{{ $in->invoice_number ?? '' }}</td>
                                <td>{{ $in->trans_type ?? '' }}</td>
                              <td>{{ $in->editedby->name ?? '' }}</td>

                                <td>{{ date('d-m-Y (H:i)', strtotime($in->updated_at)) }}</td>
                                {{-- <td>{{ $in->date ?? '' }}</td>                              --}}
                                <td>
                                    <a href="{{ route('inventories.show', $in->id) }}" class="btn btn-primary">View</a>

                                </td>
                                {{-- <td>
                                    <a href="{{ route('inventories.generateinventoryPDF', $in->id) }}" class="btn btn-secondary">Download</a>

                                </td> --}}
                                @can('edit-grn')
                                    <td>
                                        <a href="{{ route('inventories.edit', $in->id) }}" class="btn btn-success">Edit</a>

                                    </td>
                                @endcan
                                @can('delete-grn')
                                    <td>

                                        <form action="{{ route('inventories.destroy', $in->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                @endcan

                            @empty
                            <tr>
                                <td class="text-center" colspan="12">Item not available!</td>
                            </tr>
                    @endforelse

                    </tr>
                    </tbody>

                </table>
            </div>
            {{ $inventories->links('pagination::bootstrap-4') }}
            <!-- /.card-body -->
        </div>




    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {!! Toastr::message() !!}

    </html>
    
@endsection
