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
                <div class="card-header">
                 
                    <a href="{{ route('property.allproperties') }}" class="btn btn-primary float-right">Back</a>
                </div>

                <div class="col-lg-3">
                    <p>Cover:</p>
                    <form action="/deletecover/{{ $properties->id }}" method="post">
                        <button class="btn text-danger">X</button>
                        @csrf
                        @method('delete')
                    </form>
                    <img src="/cover/{{ $properties->cover }}" class="img-responsive"
                        style="max-height: 100px; max-width: 100px;" alt="" srcset="">
                    <br>



                    @if (count($properties->images) > 0)
                        <p>Images:</p>
                        @foreach ($properties->images as $img)
                            <form action="/deleteimage/{{ $img->id }}" method="post">
                                <button class="btn text-danger">X</button>
                                @csrf
                                @method('delete')
                            </form>
                            <img src="/images/properties/{{ $img->image }}" class="img-responsive"
                                style="max-height: 100px; max-width: 100px;" alt="" srcset="">
                        @endforeach
                    @endif

                </div>


                <div class="col-lg-6">
                    <h3 class="text-center text-danger"><b>Udate Post</b> </h3>
                    <div class="form-group">
                        <form action="/update/{{ $properties->id }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Location: </label>
                                        <input type="text" value="{{ $properties->location }}" name="location"
                                            class="form-control m-2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price: </label>
                                        <input type="number" value="{{ $properties->price }}" name="price"
                                            class="form-control m-2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Beds: </label>
                                        <input type="number" value="{{ $properties->beds }}" name="beds"
                                            class="form-control m-2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Baths: </label>
                                        <input type="number" value="{{ $properties->baths }}" name="baths"
                                            class="form-control m-2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Garage: </label>
                                        <input type="text" value="{{ $properties->garage }}" name="garage"
                                            class="form-control m-2">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price: </label>
                                        <input type="number" value="{{ $properties->price }}" name="price"
                                            class="form-control m-2">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Property Type: </label>
                                        <select class="select form-control m-2" id="property_type" name="property_type"
                                            required data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $properties->property_type == 'House' ? 'selected' : '' }}
                                                value="House">House</option>
                                            <option {{ $properties->property_type == 'Apartment' ? 'selected' : '' }}
                                                value="Apartment">Apartment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status: </label>
                                        <select class="select form-control m-2" id="status" name="status" required
                                            data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            <option {{ $properties->status == 'Rent' ? 'selected' : '' }}
                                                value="Rent">Rent</option>
                                            <option {{ $properties->status == 'Sale' ? 'selected' : '' }}
                                                value="Sale">Sale</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                             <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Country: </label>
                                        <select class="select form-control m-2" id="country_id" name="country_id"
                                            required data-fouc data-placeholder="Choose..">

                                            <option value=""></option>
                                            @foreach ($countries as $ct)
                                                <option {{ $properties->country_id == $ct->id ? 'selected' : '' }}
                                                    value="{{ $ct->id }}">{{ $ct->name }}</option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>States: </label>
                                        <select id="state-dropdown" class="select form-control m-2" name="state_id">
                                            <option value=""></option>
                                            @foreach ($states as $st)
                                                <option {{ $properties->state_id == $st->id ? 'selected' : '' }}
                                                    value="{{ $st->id }}">{{ $st->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Balcony: </label>
                                        <input type="checkbox" name="balcony"
                                            {{ $properties->balcony ? 'checked' : '' }} />
                                        <br>
                                        <label>Deck: </label>
                                        <input type="checkbox" name="deck" {{ $properties->deck ? 'checked' : '' }} />
                                        <br>
                                        <label>Parking: </label>
                                        <input type="checkbox" name="parking"
                                            {{ $properties->parking ? 'checked' : '' }} />
                                        <br>
                                        <label>Outdoor Kitchen: </label>
                                        <input type="checkbox" name="outdoor_kitchen"
                                            {{ $properties->outdoor_kitchen ? 'checked' : '' }} />
                                        <br>
                                        <label>Tennis Court: </label>
                                        <input type="checkbox" name="tennis_court"
                                            {{ $properties->tennis_court ? 'checked' : '' }} />
                                        <br>
                                        <label>Sun Room: </label>
                                        <input type="checkbox" name="sun_room"
                                            {{ $properties->sun_room ? 'checked' : '' }} />
                                        <br>
                                        <label>Flat Tv: </label>
                                        <input type="checkbox" name="flat_tv"
                                            {{ $properties->flat_tv ? 'checked' : '' }} />
                                        <br>
                                        <label>Internet: </label>
                                        <input type="checkbox" name="internet"
                                            {{ $properties->internet ? 'checked' : '' }} />
                                    </div>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Description: </label>
                                        <Textarea name="description" cols="90" rows="6" class="form-control m-2" placeholder="description"
                                            style="width:600px;">{{ $properties->description }}</Textarea>
                                    </div>
                                </div>
                            </div>



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
