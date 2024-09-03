@extends('layouts.admin')
@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Add Supplier</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
            integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA=="
            crossorigin="anonymous" />
    </head>

    <body>
        <div>

            <br>
        </div>

        <div class="card">

            <div class="card-header">
                Add Supplier
                <a href="{{ route('suppliers.index') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">
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

                <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card card-primary">
                        <div class="card-header">
                            {{-- <h3 class="card-title">Quick Example</h3> --}}
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Name: <span class="text-danger"></span></label>
                                        <input value="{{ old('name') }}" required type="text" name="name"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address: </label>
                                        <input type="text" value="{{ old('address') }}" name="address"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Location: <span class="text-danger"></span></label>
                                        <input value="{{ old('location') }}" type="text" name="location"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Telephone: </label>
                                        <input type="text" value="{{ old('tel') }}" name="tel"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Phone: <span class="text-danger"></span></label>
                                        <input value="{{ old('phone') }}" type="text" name="phone"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email: </label>
                                        <input type="text" value="{{ old('email') }}" name="email"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Items Supplied: <span class="text-danger"></span></label>
                                        <input value="{{ old('items_supplied') }}" type="text" name="items_supplied"
                                            class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Contact Person: <span class="text-danger"></span></label>
                                        <input value="{{ old('contact_person') }}" type="text" name="contact_person"
                                            class="form-control">
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Primary Currency: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="currency" name="primary_currency" required
                                            data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            
                                            <option {{ old('primary_currency') == 'Dollars' ? 'selected' : '' }}
                                                value="Dollars">Dollars
                                            </option>
                                            <option {{ old('primary_currency') == 'Pounds' ? 'selected' : '' }}
                                                value="Pounds">Pounds
                                            </option>

                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Company Registration Number: <span class="text-danger"></span></label>
                                            <input value="{{ old('comp_reg_no') }}" type="text" name="comp_reg_no"
                                                class="form-control">
                                        </div>
                                    </div>


                                </div>

                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Vat Registration Number: <span class="text-danger"></span></label>
                                            <input value="{{ old('vat_reg_no') }}" type="text" name="vat_reg_no"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Item Category 1: <span class="text-danger"></span></label>
                                            <input value="{{ old('item_cat1') }}" type="text" name="item_cat1"
                                                class="form-control">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Item Category 2: <span class="text-danger"></span></label>
                                            <input value="{{ old('item_cat2') }}" type="text" name="item_cat2"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> Item Category 3: <span class="text-danger"></span></label>
                                            <input value="{{ old('item_cat3') }}" type="text" name="item_cat3"
                                                class="form-control">
                                        </div>
                                    </div>


                                </div>



                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                </form>
            </div>

        </div>

        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    </html>
@endsection
