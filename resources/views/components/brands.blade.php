<div class="single-widget category">
    <h3 class="title">Hãng</h3>
    <ul class="category-list">
        @foreach($brands as $brand)
            <li><a href="{{route('product-brand',$brand->slug)}}">{{$brand->title}}</a></li>
        @endforeach
    </ul>
</div>
