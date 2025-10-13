<div>
    <div class="float-right">
        <label>Lọc tên người gửi</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tác giả</th>
            <th>Sản phẩm</th>
            <th>Nội dung</th>
            <th>Đánh giá</th>
            <th>Ngày đánh giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Tác giả</th>
            <th>Sản phẩm</th>
            <th>Nội dung</th>
            <th>Đánh giá</th>
            <th>Ngày đánh giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>
            @foreach($reviews as $review)
                <tr>
                    <td>{{$review->id}}</td>
                    <td>{{$review->user_info['name']}}</td>
                    <td>{{$review->product->title}}</td>
                    <td>{{$review->review}}</td>
                    <td>
                        <ul style="list-style:none">
                            @for($i = 1; $i<=5;$i++)
                                @if($review->rate >=$i)
                                    <li style="float:left;color:#F7941D;"><i class="fa fa-star"></i></li>
                                @else
                                    <li style="float:left;color:#F7941D;"><i class="far fa-star"></i></li>
                                @endif
                            @endfor
                        </ul>
                    </td>
                    <td>{{$review->created_at->format('M d D, Y g: i a')}}</td>
                    <td>
                        @if($review->status=='active')
                            <span class="badge badge-success">{{$review->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$review->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('review.edit',$review->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('review.destroy',[$review->id])}}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id={{$review->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$reviews->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
