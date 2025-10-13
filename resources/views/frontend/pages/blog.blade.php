@extends('frontend.layouts.master')

@section('title','E-SHOP || Blog Page')

@section('main-content')
    @php
        $breadcrumbPath = [
            new \App\View\Components\BreadcrumbElement('Trang chủ', route('home')),
            new \App\View\Components\BreadcrumbElement('Blog', null),
        ];
    @endphp

    <x-breadcrumb :elements="$breadcrumbPath"/>

    <!-- Start Blog Single -->
    <section class="blog-single shop-blog grid section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="row">
                        @foreach($posts as $post)
                            <x-post-card :post="$post" class="col-lg-6 col-md-6 col-12"/>
                        @endforeach
                        <div class="col-12">
                            <!-- Pagination -->
                            {{-- {{$posts->appends($_GET)->links()}} --}}
                            <!--/ End Pagination -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-12">
                    <div class="main-sidebar">
                        <!-- Single Widget -->
                        <div class="single-widget search">
                            <form class="form" method="GET" action="{{route('blog.search')}}">
                                <input type="text" placeholder="Search Here..." name="search">
                                <button class="button" type="sumbit"><i class="fa fa-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Single Widget -->
                        <!-- Single Widget -->
                        <div class="single-widget category">
                            <h3 class="title">Danh mục</h3>
                            <ul class="category-list">
                                @if(!empty($_GET['category']))
                                    @php
                                        $filter_cats = explode(',',$_GET['category']);
                                    @endphp
                                @endif
                            <form action="{{route('blog.filter')}}" method="POST">
                                    @csrf
                                    {{-- {{count(Helper::postCategoryList())}} --}}
                                    @foreach(\App\Http\Helper::postCategoryList('posts') as $cat)
                                    <li>
                                        <a href="{{route('blog.category',$cat->slug)}}">{{$cat->title}} </a>
                                    </li>
                                    @endforeach
                                </form>
                            </ul>
                        </div>

                        <div class="single-widget recent-post">
                            <h3 class="title">Bài viết gần đây</h3>
                            @foreach($recent_posts as $post)
                                <div class="single-post">
                                    <div class="image">
                                        <img src="{{$post->photo}}" alt="{{$post->photo}}">
                                    </div>
                                    <div class="content">
                                        <h5><a href="#">{{$post->title}}</a></h5>
                                        <ul class="comment">
                                            <li><i class="fa fa-calendar" aria-hidden="true"></i>{{$post->created_at->format('d M, y')}}</li>
                                            <li><i class="fa fa-user" aria-hidden="true"></i>
                                                {{$post->author_info->name ?? 'Anonymous'}}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="single-widget side-tags">
                            <h3 class="title">Thẻ</h3>
                            <ul class="tag">
                                @if(!empty($_GET['tag']))
                                    @php
                                        $filter_tags = explode(',',$_GET['tag']);
                                    @endphp
                                @endif
                                <form action="{{route('blog.filter')}}" method="POST">
                                    @csrf
                                    @foreach(\App\Http\Helper::postTagList('posts') as $tag)
                                        <li>
                                            <li>
                                                <a href="{{route('blog.tag',$tag->title)}}">{{$tag->title}} </a>
                                            </li>
                                        </li>
                                    @endforeach
                                </form>
                            </ul>
                        </div>

{{--                        <div class="single-widget newsletter">--}}
{{--                            <h3 class="title">Newslatter</h3>--}}
{{--                            <div class="letter-inner">--}}
{{--                                <h4>Subscribe & get news <br> latest updates.</h4>--}}
{{--                                <form method="POST" action="{{route('subscribe')}}" class="form-inner">--}}
{{--                                    @csrf--}}
{{--                                    <input type="email" name="email" placeholder="Enter your email">--}}
{{--                                    <button type="submit" class="btn " style="width: 100%">Submit</button>--}}
{{--                                </form>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <style>
        .pagination{
            display:inline-flex;
        }
    </style>

@endpush
