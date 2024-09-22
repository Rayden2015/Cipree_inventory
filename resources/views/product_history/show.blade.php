@extends('layouts.admin')
@section('content')
   
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Product History Show</title>
            <link rel="stylesheet" href="http://cdn.bootcss.com/toastr.js/latest/css/toastr.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
                integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
                crossorigin="anonymous" />
        </head>

        <body>
            <div class="title d-flex justify-content-between">
                <h3 class="page-title"></h3>
                <p>
                    <a href="{{ route('product_history') }}" class="btn btn-primary mr-3 my-3">Back</a>
                </p>
            </div>


            <div class="card">  
                <div class="card-header">
                    <h3 class="card-title">Product History</h3>
                </div>
                <!-- /.card-header -->
            
                <div class="card-body">
                    Received 
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Part Number</th>
                                <th>Stock Code</th>
                                <th>Received Quantity</th>
                                <th>Date Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($received as $ct)
                                <tr>
                                    <td>{{ $ct->id }}</td>
                                    <td>{{ $ct->item->item_description ?? ''}}</td>
                                    <td>{{ $ct->item->item_part_number ?? ''}}</td>
                                    <td>{{ $ct->item->item_stock_code ?? ''}}</td>
                                    <td>{{ $ct->quantity ?? ''}}</td>
                                    <td>{{ $ct->created_at ?? ''}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                            @endforelse
                    
                            <!-- Total Row -->
                            @if($received->count() > 0)
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total Quantity Received:</strong></td>
                                    <td><strong>{{ $received->sum('quantity') }}</strong></td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    
                </div>
              

                <div class="card-body">
                    Supplied
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Description</th>
                                <th>Part Number</th>
                                <th>Stock Code</th>
                                <th>Quantity Supplied</th>
                                <th>Date Supplied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($supplied as $rc)
                                <tr>
                                    <td>{{ $rc->id }}</td>
                                    <td>{{ $rc->item->item_description ?? ''}}</td>
                                    <td>{{ $rc->item->item_part_number ?? ''}}</td>
                                    <td>{{ $rc->item->item_stock_code ?? ''}}</td>
                                    <td>{{ $rc->qty_supplied ?? ''}}</td>
                                    <td>{{ $rc->delivered_on ?? ''}}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="6">Data Not Found!</td>
                                </tr>
                            @endforelse
                
                            <!-- Total Row -->
                            @if($supplied->count() > 0)
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total Quantity Supplied:</strong></td>
                                    <td><strong>{{ $supplied->sum('qty_supplied') }}</strong></td>
                                    <td></td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- /.card-body -->
            </div>




        </body>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

    {{-- @endif --}}
@endsection
