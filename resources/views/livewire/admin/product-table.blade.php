<div>
    <div class="float-right">
        <label>Lọc tên</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Đề xuất</th>
            <th>Giá</th>
            <th>Giảm giá</th>
            <th>Kích cỡ</th>
            <th>Tình trạng</th>
            <th>Hãng</th>
            <th>Tồn kho</th>
            <th>Ảnh</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Tên</th>
            <th>Danh mục</th>
            <th>Đề xuất</th>
            <th>Giá</th>
            <th>Giảm giá</th>
            <th>Kích cỡ</th>
            <th>Tình trạng</th>
            <th>Hãng</th>
            <th>Tồn kho</th>
            <th>Ảnh</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach($products as $product)
            @php
                $sub_cat_info = DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
                // dd($sub_cat_info);
                $brands = DB::table('brands')->select('title')->where('id',$product->brand_id)->get();
            @endphp
            <tr>
                <td>{{$product->id}}</td>
                <td>{{$product->title}}</td>
                <td>{{$product->cat_info['title']}}
                    <sub>
                        {{$product->sub_cat_info->title ?? ''}}
                    </sub>
                </td>
                <td>{{(($product->is_featured==1)? 'Yes': 'No')}}</td>
                <td>{{number_format($product->price, 0, ',', '.')}} đ</td>
                <td>  {{$product->discount}}% OFF</td>
                <td>{{$product->size}}</td>
                <td>
                    @switch ($product->condition)
                        @case('default')
                            Mặc định
                            @break

                        @case('new')
                            Mới
                            @break

                        @case('hot')
                            Nổi bật
                            @break

                        @default
                            ???
                            @break
                    @endswitch
                </td>
                <td> {{ucfirst($product->brand->title)}}</td>
                <td>
                    @if($product->stock>0)
                        <span class="badge badge-primary">{{$product->stock}}</span>
                    @else
                        <span class="badge badge-danger">{{$product->stock}}</span>
                    @endif
                </td>
                <td>
                    @if($product->photo)
                        @php
                            $photo = explode(',',$product->photo);
                            // dd($photo);
                        @endphp
                        <img src="{{$photo[0]}}" class="img-fluid zoom" style="max-width:80px" alt="{{$product->photo}}">
                    @else
                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                    @endif
                </td>
                <td>
                    @if($product->status=='active')
                        <span class="badge badge-success">{{$product->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$product->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('product.edit',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$product->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$products->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
