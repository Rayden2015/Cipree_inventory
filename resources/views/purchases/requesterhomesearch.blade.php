<h5 style="color:white;">Check Item Availability <p style="float:right;"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <span
            class="badge badge-pill badge-danger">
            @if (count((array) session('cart')) == '0')
                <a href="{{ route('stores.request_search') }}">
                    {{ count((array) session('cart')) }}
                </a>
            @elseif(session('cart') > '0')
                <a href="{{ route('cart') }}">
                    {{ count((array) session('cart')) }}
                </a>
            @endif
        </span> </p>
</h5>

<form action="{{ route('stores.requester_search') }}" method="GET">
    <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Enter Description or Part number or Stock Code"
            aria-describedby="basic-addon2" required name="search">
        <div class="input-group-append">
            <button class="btn btn-secondary" type="submit">Search</button>
        </div>
    </div>
</form>
