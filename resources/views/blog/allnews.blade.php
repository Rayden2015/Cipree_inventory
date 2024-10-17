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
        <script type="text/javascript">
            function EnableDisableTextBox(ddlModels) {
                var selectedValue = ddlModels.options[ddlModels.selectedIndex].value;
                var txtOther = document.getElementById("txtOther");
                txtOther.disabled = selectedValue == "YES" ? false : true;
                if (!txtOther.disabled) {
                    txtOther.focus();
                }
            }
        </script>
    </head>

    <body>
        <div class="title d-flex justify-content-between">
            <h3 class="page-title"></h3>
            <p>
                <a href="{{ route('blog.create') }}" class="btn btn-primary mr-3 my-3">Add News</a>
            </p>
        </div>


        <div class="card">
            <div class="card-header">
                {{-- <h3 class="card-title">DataTable with default features</h3> --}}
            </div>
            <!-- /.card-header -->

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Headline</th>
                                <th>Image</th>

                                <th>Edit</th>
                                <th>Delete</th>

                            </tr>
                        </thead>
                        @forelse ($allnews as $sp)
                            <tbody>



                                <tr>

                                    <td>{{ $sp->id }}</td>
                                    <td>{{ $sp->headline }}</td>
                                    <td><img class="rounded-circle" style="height: 80px; width: 70px;" src="{{asset('images/blog/'. $sp->image) }}" alt="photo"></td>
                                    {{-- <td> <img src="{{ asset('images/' . $sp->image) }}" alt=""></td> --}}
                                    <td>
                                        <a href="{{route('blog.edit',$sp->id)}}" class ="btn btn-success">Edit</a>

                                    </td>
                                    <td>

                                        <form action="{{ route('blog.destroy', $sp->id) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="btn btn-danger">Delete</button>
                                        </form></td>


                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
            <!-- /.card-body -->
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
        <!-- AdminLTE for demo purposes -->
        <script src="{{ asset('/assets/dist/js/demo.js') }}"></script>
        <!-- Page specific script -->
        <script>
            $(function() {
                $("#example1").DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                $('#example2').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                });
            });
        </script>

    </body>
    <script src="http://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
    <script src="http://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
    {{-- {!! Toastr::message() !!} --}}

    </html>
@endsection
