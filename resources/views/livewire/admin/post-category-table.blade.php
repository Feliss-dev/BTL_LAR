<div>
    <div class="float-right">
        <label>Lọc tiêu đề</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Slug</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Slug</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach ($categories as $category)
            <tr>
                <td>{{$category->id}}</td>
                <td>{{$category->title}}</td>
                <td>{{$category->slug}}</td>
                <td>
                    @if($category->status=='active')
                        <span class="badge badge-success">{{$category->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$category->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('post-category.edit',$category->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('post-category.destroy',[$category->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$category->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
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
