<div {{ $attributes }}>
    <!-- Start Single Blog  -->
    <div class="shop-single-blog">
        <img src="{{$post->photo}}" alt="{{$post->photo}}">
        <div class="content">
            <p class="date">
                <i class="fa fa-calendar" aria-hidden="true"></i> {{$post->created_at->format('d/m/Y')}}

                <span class="float-right">
                    <i class="fa fa-user" aria-hidden="true"></i>
                     {{$post->author_info->name ?? 'Anonymous'}}
                </span>
            </p>

            <a href="{{route('blog.detail',$post->slug)}}" class="title">{{$post->title}}</a>

            <p>{!! html_entity_decode($post->summary) !!}</p>

            <a href="{{route('blog.detail',$post->slug)}}" class="more-btn">Đọc tiếp</a>
        </div>
    </div>
    <!-- End Single Blog  -->
</div>
