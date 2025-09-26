<div class="single-widget category">
    <h3 class="title">Danh mục</h3>
    <ul class="category-list">
        @php
            $parentCategories = App\Models\Category::getAllParentWithChild();
        @endphp

        @if($parentCategories)
            <li>
                @foreach($parentCategories as $cat_info)
                    @if ($cat_info->child_cat->count()>0)
                        <li>
                            <a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a>

                            <ul>
                                @foreach($cat_info->child_cat as $sub_menu)
                                    <li><a href="{{route('product-sub-cat',[$cat_info->slug,$sub_menu->slug])}}">{{$sub_menu->title}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li><a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a></li>
                    @endif

                @endforeach
            </li>
        @endif
    </ul>
</div>
