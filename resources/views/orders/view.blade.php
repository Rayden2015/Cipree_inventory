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
                                    <div class="table-rep-plugin">
                                        <div class="table-responsive" data-pattern="priority-columns">
                                            <table id="tech-companies-1" class="table table-striped mb-0">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>

                                                    <th>Characteristics</th>


                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <td>{{ $order->id }}</td>
                                                 <td>{{ $order->desc ?? 'no characteristics set' }}</td>
                                                </tbody>
                                            </table>
                                        </div>



                                    </div>


                                </div>

                            </div>
                        </div>
                        <!-- end row -->

                    </div> <!-- container-fluid -->

                </div> <!-- content -->
            </div>


      @endsection()
