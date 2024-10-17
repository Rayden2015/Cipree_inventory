@extends('layouts.admin')
@section('content')
    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <!-- Font-awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">

    </head>

    <body>

        <div class="container">
            <div class="row">



                <div class="col-lg-9" style="margin-left:30px;">
                    <h3 class="text-center text-primary"><b>Add New Property</b> </h3>
                    <div class="form-group">
                        <form action="{{ route('property.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            {{-- <input type="text" name="location" class="form-control m-2" placeholder="location">
        				 <input type="text" name="property " class="form-control m-2" placeholder="property ">

                         <Textarea name="body" cols="20" rows="4" class="form-control m-2" placeholder="body"></Textarea> --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Price: <span class="text-danger"></span></label>
                                        <input name="price" value="{{ old('price') }}" type="number"
                                            class="form-control" placeholder="Price">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Location: <span class="text-danger"></span></label>
                                        <input name="location" value="{{ old('location') }}" type="text"
                                            class="form-control date-pick" placeholder="Type location...">
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Status: <span class="text-danger"></span></label>
                                        <select class="form-select" id="status" name="status">

                                            <option value="">click to choose </option>
                                            <option {{ old('status') == 'Rent' ? 'selected' : '' }} value="Rent">
                                                Rent</option>
                                            <option {{ old('status') == 'Sale' ? 'selected' : '' }} value="Sale">
                                                Sale</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Property Type: <span class="text-danger"></span></label>
                                        <select class="form-select" id="property_type" name="property_type">

                                            <option value="">click to choose </option>
                                            <option {{ old('property_type') == 'House' ? 'selected' : '' }} value="House">
                                                House</option>
                                            <option {{ old('property_type') == 'Apartment' ? 'selected' : '' }}
                                                value="Apartment">
                                                Apartment</option>
                                        </select>
                                    </div>
                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Beds: <span class="text-danger"></span></label>
                                        <input name="beds" value="{{ old('beds') }}" type="number"
                                            class="form-control" placeholder="number of beds...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Baths: <span class="text-danger"></span></label>
                                        <input name="baths" value="{{ old('baths') }}" type="number"
                                            class="form-control" placeholder="number of baths...">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Garage: <span class="text-danger"></span></label>
                                        <input name="garage" value="{{ old('garage') }}" type="number"
                                            class="form-control" placeholder="number of garage...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Area: <span class="text-danger"></span></label>
                                        <input name="area" value="{{ old('area') }}" type="text"
                                            class="form-control" placeholder="Type area/size of land...">
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Country: <span class="text-danger"></span></label>
                                        <select id="country-dropdown" class="form-control" name="country_id">
                                            <option value="">-- Select Country --</option>
                                            @foreach ($countries as $data)
                                                <option value="{{ $data->id }}">
                                                    {{ $data->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> State: <span class="text-danger"></span></label>
                                        <select id="state-dropdown" class="form-control" name="state_id">
                                        </select>
                                    </div>
                                </div>


                            </div>
                        

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Description: <span class="text-danger"></span></label><br>
                                       
                                            <textarea name="description" style="width:920px;" class="form-control" id="description" value="{{ old('description') }}" cols="100" rows="10"></textarea>
                                    </div>
                                </div>


                            </div>
                            <br>

                            {{--  --}}

                            <div class="row">

                                <div class="form-check">
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Balcony </label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="balcony"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Deck</label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="deck"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Parking </label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="parking"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Outdoor Kitchen
                                    </label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="outdoor_kitchen"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Tennis
                                        Court</label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="tennis_court"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Sun Room </label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="sun_room"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Flat TV</label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="flat_tv"
                                        id="exampleCheck1">
                                    <br>
                                    <label class="form-check-label font-weight-bold" for="exampleCheck1">Internet</label>
                                    <input type="checkbox" class="form-check-input mx-auto" name="internet"
                                        id="exampleCheck1">

                                </div>
                            </div>
                            {{--  --}}


                            <label class="m-2">Cover Image</label>
                            <input type="file" id="input-file-now-custom-3" class="form-control m-2" name="cover">

                            <label class="m-2">Images</label>
                            <input type="file" id="input-file-now-custom-3" class="form-control m-2" name="images[]"
                                multiple>

                            <button type="submit" class="btn btn-danger mt-3 ">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script>
                $(document).ready(function() {

                            /*------------------------------------------
                            --------------------------------------------
                            Country Dropdown Change Event
                            --------------------------------------------
                            --------------------------------------------*/
                            $('#country-dropdown').on('change', function() {
                                var idCountry = this.value;
                                $("#state-dropdown").html('');
                                $.ajax({
                                    url: "{{ url('fetch-states') }}",
                                    type: "POST",
                                    data: {
                                        country_id: idCountry,
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json',
                                    success: function(result) {
                                        $('#state-dropdown').html(
                                            '<option value="">-- Select State --</option>');
                                        $.each(result.states, function(key, value) {
                                            $("#state-dropdown").append('<option value="' + value
                                                .id + '">' + value.name + '</option>');
                                        });
                                        $('#city-dropdown').html('<option value="">-- Select City --</option>');
                                    }
                                });
                            });
                        });
                        
            </script>

    </body>

    </html>
@endsection
