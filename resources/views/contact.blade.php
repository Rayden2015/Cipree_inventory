<!DOCTYPE html>
<html lang="en">
@include('partials.headerlinks')

<body>
    @include('partials.navbar')

    <div class="hero page-inner overlay" style="background-image: url({{ asset('assets/images/hero_bg_1.jpg') }})">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-9 text-center mt-5">
                    <h1 class="heading" data-aos="fade-up">Contact Us</h1>

                    <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="200">
                        <ol class="breadcrumb text-center justify-content-center">
                            <li class="breadcrumb-item"><a href="{{ route('pages.index') }}">Home</a></li>
                            <li class="breadcrumb-item active text-white-50" aria-current="page">
                                Contact
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-info">
                        <div class="address mt-2">
                            <i class="icon-room"></i>
                            <h4 class="mb-2">Location:</h4>
                            <p>
                                43 Raymouth Rd. Baltemoer,<br />
                                London 3910
                            </p>
                        </div>

                        <div class="open-hours mt-4">
                            <i class="icon-clock-o"></i>
                            <h4 class="mb-2">Open Hours:</h4>
                            <p>
                                Sunday-Friday:<br />
                                11:00 AM - 2300 PM
                            </p>
                        </div>

                        <div class="email mt-4">
                            <i class="icon-envelope"></i>
                            <h4 class="mb-2">Email:</h4>
                            <p>info@Untree.co</p>
                        </div>

                        <div class="phone mt-4">
                            <i class="icon-phone"></i>
                            <h4 class="mb-2">Call:</h4>
                            <p>+1 1234 55488 55</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <form action="{{ route('contact.store') }}" method="post">
                        {{ csrf_field() }}

                        @if (session()->has('success'))
                            <div class="alert alert-success">
                                {{ session()->get('success') }}
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-6 mb-3">
                              <input class="form-control valid" name="name" id="name" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter your name'" placeholder="Enter your name">
                            </div>
                            <div class="col-6 mb-3">
                              <input class="form-control valid" name="email" id="email" type="email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter email address'" placeholder="Email">
                            </div>
                            <div class="col-6 mb-3">
                              <input class="form-control" name="subject" id="subject" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Subject'" placeholder="Enter Subject">
                            </div>
                            <div class="col-6 mb-3">
                              <input class="form-control" name="phone" id="phone" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Phone'" placeholder="Enter Phone">
                            </div>
                            <div class="col-12 mb-3">
                              <textarea class="form-control w-100" name="message" id="message" cols="30" rows="7" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter Message'" placeholder=" Enter Message"></textarea>
                            </div>

                            <div class="col-12">
                                <input type="submit"  class="btn btn-primary" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /.untree_co-section -->

    @include('partials.footer')
</body>

</html>
