@extends('layouts.admin')
@section('content')
  

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
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
                Add New Item
                <a href="{{ route('items.index') }}" class="btn btn-primary float-right">Back</a>
            </div>

            <div class="card-body">

                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Description: <span class="text-danger"></span></label>
                                        <input value="{{ old('item_description') }}" required type="text"
                                            name="item_description" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Part Number: <span class="text-danger"></span></label>
                                        <input value="{{ old('item_part_number') }}" required type="text"
                                            name="item_part_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> UOM: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="item_uom" name="item_uom" required
                                            data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            <option {{ old('item_uom') == 'Kilograms' ? 'selected' : '' }}
                                                value="Kilograms">
                                                Kilograms
                                            </option>
                                            <option {{ old('item_uom') == 'Meters' ? 'selected' : '' }} value="Meters">
                                                Meters</option>
                                            <option {{ old('item_uom') == 'Litres' ? 'selected' : '' }} value="Litres">
                                                Litres
                                            </option>
                                            <option {{ old('item_uom') == 'Pieces' ? 'selected' : '' }} value="Pieces">
                                                Pieces</option>
                                            <option {{ old('item_uom') == 'Kits' ? 'selected' : '' }} value="Kits">Kits
                                            </option>

                                            <option {{ old('item_uom') == 'Pair' ? 'selected' : '' }} value="Pair">
                                                Pair</option>
                                            <option {{ old('item_uom') == 'Bale' ? 'selected' : '' }} value="Bale">
                                                Bale
                                            </option>
                                            <option {{ old('item_uom') == 'Bottle' ? 'selected' : '' }} value="Bottle">
                                                Bottle</option>
                                            <option {{ old('item_uom') == 'Box' ? 'selected' : '' }} value="Box">Box
                                            </option>
                                            <option {{ old('item_uom') == 'Bucket' ? 'selected' : '' }} value="Bucket">
                                                Bucket</option>
                                            <option {{ old('item_uom') == 'Carton' ? 'selected' : '' }} value="Carton">
                                                Carton
                                            </option>
                                            <option {{ old('item_uom') == 'Drum' ? 'selected' : '' }} value="Drum">
                                                Drum</option>
                                            <option {{ old('item_uom') == 'Gallon' ? 'selected' : '' }} value="Gallon">Gallon
                                            </option>
                                            <option {{ old('item_uom') == 'Pack' ? 'selected' : '' }} value="Pack">
                                                Pack</option>
                                            <option {{ old('item_uom') == 'Rim' ? 'selected' : '' }} value="Rim">
                                                Rim
                                            </option>
                                            <option {{ old('item_uom') == 'Roll' ? 'selected' : '' }} value="Roll">
                                                Roll</option>
                                           

                                        </select>
                                    </div>
                                </div>


                            </div>

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for=""> Reorder Level</label>
                                        <input type="number" class="form-control" min="0" name="reorder_level"
                                            value="{{ old('reorder_level') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Group: </label>
                                        <select id="item_category_id" type="text"
                                            class="form-control @error('item_category_id') is-invalid @enderror"
                                            name="item_category_id" autocomplete="item_category_id" autofocus>
                                            <option value="" selected hidden>Please Select</option>

                                            @foreach ($categories as $ct)
                                                <option {{ old('ct') == $ct->id ? 'selected' : '' }}
                                                    value="{{ $ct->id }}">{{ $ct->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Category: <span class="text-danger"></span></label>
                                        <select class="select form-control" id="new_categort" name="new_categort"
                                            required data-fouc data-placeholder="Choose..">
                                            <option value=""></option>
                                            <option {{ old('new_categort') == 'Goods' ? 'selected' : '' }}
                                                value="Goods">Goods
                                            </option>
                                            <option {{ old('new_categort') == 'Services' ? 'selected' : '' }}
                                                value="Services">
                                                Services</option>
                                        </select>
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
    {!! Toastr::message() !!}

    </html>

@endsection
