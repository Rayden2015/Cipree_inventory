@extends('layouts.admin')

    @section('content')
           <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-12">
                                <div class="card-box">
                                    <h2>Orders</h2>
                                    <form action="{{ route('orders.orders_search') }}" method="GET">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Search name or phone"
                                                aria-describedby="basic-addon2" name="search" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-secondary" type="submit">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                    <p>
                                        <a href="{{ route('orders.create') }}" class="btn btn-primary mr-3 my-3 float-right">Add New</a>
                                    </p>
                                    <div class="responsive-table-plugin" style="padding-bottom: 15px;">
                                        @if (Session::has('success'))
                                            <div class="alert alert-success">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <strong>Success!</strong> {{Session::get('success')}}
                                        </div>
                                        @endif

                                        <div class="table-rep-plugin">
                                            <div class="table-responsive" data-pattern="priority-columns">
                                                <table id="tech-companies-1" class="table table-striped mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th data-priority="1">Customer Name</th>
                                                        {{-- <th>Initial Payment</th> --}}
                                                        {{-- <th>Payment method</th> --}}
                                                        <th>Order Status</th>
                                                        <th>Order By</th>
                                                        <th>Taken At</th>
                                                        <th>View Details</th>
                                                        {{-- <th>Delete</th> --}}
                                                        <th>Print</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if (isset($orders))


                                                        @foreach ($orders as $order)
                                                            {{-- expr --}}

                                                    <tr>
                                                        <td>{{$order->id}}</td>
                                                        <td>{{$order->name ?? 'not set'}}</td>
                                                        {{-- <th>{{$order->total_amount}}</th> --}}
                                                        {{-- <th>{{$order->payment_method}}</th> --}}
                                                        <th>{{$order->status}}</th>
                                                        <th>{{$order->user ?? 'not set'}}</th>
                                                        <th> {{ date('d-m-Y (H:i)', strtotime($order->created_at))}}</th>
                                                        {{-- <td><a href="{{url('order/'.$order->id)}}" class="btn btn-bordred-primary waves-effect  width-md waves-light">View Details</a></td>
                                                        <td>
                                                            <p  onclick="event.preventDefault();document.getElementById('del-form-{{$order->id}}').submit()" class="btn btn-bordred-danger waves-effect  width-md waves-light">Delete</p></td>

                                                        <form id="del-form-{{$order->id}}" action="{{url('order/'.$order->id)}}" method="POST" style="display:none;">
                                                            @method('DELETE')
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{$order->id}}">

                                                        </form> --}}
                                                        <td>
                                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-success">View</a>

                                                        </td>


                                                        <td>
                                                            <a href="{{ route('orders.invoice_print', $order->id) }}" target="_blank" class="btn btn-primary">Print</a>

                                                        </td>


                                                    </tr>
                                                      @endforeach
                                                      @else
                                                       <tr>
                                                        <th colspan="6">
                                                            <h2 class="text-center">No data found</h2>
                                                        </th>
                                                       </tr>
                                                      @endif
                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>

                                    </div>
                                    {{-- {{$orders->links('pagination::bootstrap-4')}} --}}

                                    {{-- $posts->links('pagination::bootstrap-4') --}}
                                </div>

                            </div>
                        </div>
                        <!-- end row -->

                    </div> <!-- container-fluid -->

                </div> <!-- content -->
            </div>


      @endsection()
