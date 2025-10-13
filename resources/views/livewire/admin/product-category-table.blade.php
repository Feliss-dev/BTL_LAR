<div>
    <div class="float-right">
        <label>Lọc tên</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Là danh mục cha</th>
                <th>Danh mục cha</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>S.N.</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Là danh mục cha</th>
                <th>Danh mục cha</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </tfoot>

        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{$category->id}}</td>
                    <td>{{$category->title}}</td>
                    <td>{{$category->slug}}</td>
                    <td>{{(($category->is_parent==1)? 'Yes': 'No')}}</td>
                    <td>
                        {{$category->parent_info->title ?? ''}}
                    </td>
                    <td>
                        @if($category->photo)
                            <img src="{{$category->photo}}" class="img-fluid" style="max-width:80px"
                                 alt="{{$category->photo}}">
                        @else
                            <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid"
                                 style="max-width:80px" alt="avatar.png">
                        @endif
                    </td>
                    <td>
                        @if($category->status=='active')
                            <span class="badge badge-success">{{$category->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$category->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('category.edit',$category->id)}}"
                           class="btn btn-primary btn-sm float-left mr-1"
                           style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                           title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>

                        <form method="POST" action="{{route('category.destroy',[$category->id])}}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn"
                                    data-id="{{$category->id}}"
                                    style="height:30px;width:30px;border-radius:50%
                            " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$categories->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
