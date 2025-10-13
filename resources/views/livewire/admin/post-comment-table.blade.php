<div>
    <div class="float-right">
        <label>Lọc tác giả</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tác giả</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày bình luận</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Tác giả</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Ngày bình luận</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach($comments as $comment)
            <tr>
                <td>{{$comment->id}}</td>
                <td>{{$comment->user_info['name']}}</td>
                <td>{{$comment->post->title}}</td>
                <td>{{$comment->comment}}</td>
                <td>{{$comment->created_at->format('M d D, Y g: i a')}}</td>
                <td>
                    @if($comment->status=='active')
                        <span class="badge badge-success">{{$comment->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$comment->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('comment.edit',$comment->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('comment.destroy',[$comment->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$comment->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$comments->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
