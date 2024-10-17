@extends('layouts.app')
@section('content')

    @include('partials.headerfiles')
    @include('partials.top_header')
    @include('partials.header')

    <div class="col-md-4 col-sm-4 col-xs-12">
        <div class="single-blog">
            <div class="single-blog-img">
                <a href="blog.html">
                    <img src="{{ asset('images/' . $media->image) }}" alt="">
                </a>
            </div>
            <div class="blog-meta">
                <span class="comments-type">
                    <i class="fa fa-comment-o"></i>
                    <a href="#">130 comments</a>
                </span>
                <span class="date-type">
                    <i class="fa fa-calendar"></i>{{ $media->created_at }}
                </span>
            </div>
            <div class="blog-text">
                <h4>
                    <a href="blog.html">{{ $media->headline }}</a>
                </h4>
                <p>
                    {{ $media->body }}
                </p>
            </div>
<button class="btn btn-primary">Hackman</button>
        </div>
       
    </div>

    @include('partials.footer')
@endsection
