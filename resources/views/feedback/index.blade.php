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

            </div>


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reviews</h3>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Message</th>
                             <th>Status</th>
                             <th>Created At</th>
                                    <th>Show</th>
                                    <th>Delete</th>
                               
                            </tr>
                        </thead>
                        @forelse ($feedbacks as $st)
                            <tbody>
                                <tr>

                                    <td>{{ $st->id }}</td>
                                    <td>{{ $st->type ?? '' }}</td>
                                    <td>{{Str::limit($st->message ?? '', 30) }}</td>
                                   
                                    @if ($st->reviewed == 0)
                                    <td><label for="">Unresolved</label></td>
                                        @else
                                        <td><label for="">Resolved</label></td>
                                    @endif
                                    <td>{{ $st->created_at ?? '' }}</td>
                                    {{-- <td><img src="{{ asset('storage/screenshots/'.$st->screenshot) }}" width="26" height="26"
                                        alt="" /></td> --}}
                                  
                                   
                                    {{-- <td>{{ $st->user_info ?? '' }}</td> --}}
                                   
                                        <td>
                                            <a href="{{ route('reviews.show', $st->id) }}" class ="btn btn-success">show</a>

                                        </td>
                                        <td>

                                            <form action="{{ route('reviews.destroy', $st->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                    class="btn btn-danger">Delete</button>
                                            </form>
                                        </td>
                                   

                                @empty
                                <tr>
                                    <td class="text-center" colspan="12">Data Not Found!</td>
                                </tr>
                        @endforelse

                        </tr>
                        </tbody>

                    </table>
                </div>
                {{-- {{ $feedbacks->links('pagination::bootstrap-4') }} --}}
                <!-- /.card-body -->
            </div>


        </body>
        <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/toastr.js/latest/js/toastr.min.js"></script>
        {!! Toastr::message() !!}

        </html>
   
@endsection
