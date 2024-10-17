<!-- /*
* Template Name: Property
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->
<!DOCTYPE html>
<html lang="en">
@include('partials.headerlinks')

<body>
    @include('partials.navbar')

    <div class="hero page-inner overlay" style="background-image: url({{ asset('assets/images/hero_bg_3.jpg') }})">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-9 text-center mt-5">
                    <h1 class="heading" data-aos="fade-up">

                    </h1>

                    <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="200">
                        <ol class="breadcrumb text-center justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('pages.index') }}">Home</a></li>
                            <li class="breadcrumb-item">
                                <a href="properties.html">Properties</a>
                            </li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">
                             {{ $properties->location ?? '' }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-7">
                    <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner ">
                            @foreach ($properties->images as $images)
                                <div class="carousel-item @if ($loop->first) active @endif">
                                    <div class="slider-image text-center">
                                        <img src="{{ asset('/images/properties/' . $images->image) }}"
                                            class="d-block w-100" style="width:300px; height:400px;"
                                            alt="{{ $images->image }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    <br>
                    <div class="row section-t3">
                        <div class="col-sm-12">
                            <div class="title-box-d">
                                <h3 class="title-d">Amenities</h3>
                            </div>
                        </div>
                    </div>
                    <div class="amenities-list color-text-a">

                        <div class="row">
                            <div class="col col-md-4">
                                <ul class="list-inline">

                                    @if ($properties->balcony == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">-
                                            Balcony
                                        </li>
                                    @endif
                                    @if ($properties->outdoor_kitchen == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Outdoor Kitchen</li>
                                    @endif
                                    @if ($properties->flat_tv == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Flat Tv</li>
                                    @endif
                                    @if ($properties->deck == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Deck</li>
                                    @endif
                                    @if ($properties->tennis_court == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Tennis Courts</li>
                                    @endif
                                    @if ($properties->internet == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Internet</li>
                                    @endif
                                    @if ($properties->parking == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Parking</li>
                                    @endif
                                    @if ($properties->sun_room == 'on')
                                        <li style="font-weight:bold"class="list-inline-item">- Sun Room</li>
                                    @endif

                                </ul>
                            </div>


                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <h2 class="heading text-primary"> {{ $properties->location ?? '' }}</h2>
                    <p class="meta" style="font-weight:bold;">{{ $properties->state->name ?? '' }}, {{ $properties->country->name ?? '' }}</p>
                    <p class="text-black-50" style="font-weight:bold;">
                       {{ $properties->description ?? '' }}
                    </p>
                    

                    <div class="d-block agent-box p-5">
                        <div class="img mb-4">
                            <img src="{{ asset('/images/users/'.$properties->user->picture) }}" alt="Image" class="img-fluid" />
                        </div>
                        <div class="text">
                            <h3 class="mb-0">{{ $properties->user->name ?? ''}}</h3>
                            <div class="meta mb-3">Real Estate</div>
                            <p>
                                {{ $properties->user->description ?? '' }}
                            </p>
                            <ul class="list-unstyled social dark-hover d-flex">
                                <li class="me-1">
                                    <a href="{{ $properties->user->instagram_url ?? '' }}" target="_blank"><span class="icon-instagram"></span></a>
                                </li>
                                <li class="me-1">
                                    <a href="{{ $properties->user->twitter_url ?? '' }}"><span class="icon-twitter"></span></a>
                                </li>
                                <li class="me-1">
                                    <a href="{{ $properties->user->facebook_url ?? '' }}"><span class="icon-facebook"></span></a>
                                </li>
                                <li class="me-1">
                                    <a href="{{ $properties->user->linkedin_url ?? '' }}"><span class="icon-linkedin"></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')
</body>

</html>
