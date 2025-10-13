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
        @foreach($tags as $tag)
            <tr>
                <td>{{$tag->id}}</td>
                <td>{{$tag->title}}</td>
                <td>{{$tag->slug}}</td>
                <td>
                    @if($tag->status=='active')
                        <span class="badge badge-success">{{$tag->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$tag->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('post-tag.edit',$tag->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('post-tag.destroy',[$tag->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id="{{$tag->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$tags->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
