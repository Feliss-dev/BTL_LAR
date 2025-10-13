<div>
    <div class="float-right">
        <label>Lọc tiêu đề</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Danh mục</th>
            <th>Thẻ</th>
            <th>Tác giả</th>
            <th>Ảnh</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Danh mục</th>
            <th>Thẻ</th>
            <th>Tác giả</th>
            <th>Ảnh</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>

        @foreach($posts as $post)
            @php
                $author_info = DB::table('users')->select('name')->where('id',$post->added_by)->get();
                // dd($sub_cat_info);
                // dd($author_info);

            @endphp
            <tr>
                <td>{{$post->id}}</td>
                <td>{{$post->title}}</td>
                <td>{{$post->cat_info->title}}</td>
                <td>{{$post->tags}}</td>

                <td>
                    @foreach($author_info as $data)
                        {{$data->name}}
                    @endforeach
                </td>
                <td>
                    @if($post->photo)
                        <img src="{{$post->photo}}" class="img-fluid zoom" style="max-width:80px" alt="{{$post->photo}}">
                    @else
                        <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                    @endif
                </td>
                <td>
                    @if($post->status=='active')
                        <span class="badge badge-success">{{$post->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$post->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('post.edit',$post->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('post.destroy',[$post->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$post->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$posts->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
