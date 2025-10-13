<div>
    <div class="float-right">
        <label>Lọc tên</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Loại</th>
            <th>Giá cả</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>ID</th>
            <th>Loại</th>
            <th>Giá cả</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        </tfoot>
        <tbody>
        @foreach($shippings as $shipping)
            <tr>
                <td>{{$shipping->id}}</td>
                <td>{{$shipping->type}}</td>
                <td>{{number_format($shipping->price, 0, ',', '.')}} đ</td>
                <td>
                    @if($shipping->status=='active')
                        <span class="badge badge-success">{{$shipping->status}}</span>
                    @else
                        <span class="badge badge-warning">{{$shipping->status}}</span>
                    @endif
                </td>
                <td>
                    <a href="{{route('shipping.edit',$shipping->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="{{route('shipping.destroy',[$shipping->id])}}">
                        @csrf
                        @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id={{$shipping->id}} style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$shippings->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
