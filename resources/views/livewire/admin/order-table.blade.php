<div>
    <div class="float-right">
        <label>Lọc mã</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã đơn hàng</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số lượng hàng</th>
                <th>Phí vận chuyển</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Mã đơn hàng</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Số lượng hàng</th>
                <th>Phí vận chuyển</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </tfoot>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->name}}</td>
                    <td>{{$order->email}}</td>
                    <td>{{$order->quantity}}</td>
                    <td>{{number_format($order->shipping->price, 0, ',', '.')}} đ</td>
                    <td>{{number_format($order->total_amount, 0, ',', '.')}} đ</td>
                    <td>
                        @if($order->status=='new')
                            <span class="badge badge-primary">{{$order->status}}</span>
                        @elseif($order->status=='process')
                            <span class="badge badge-warning">{{$order->status}}</span>
                        @elseif($order->status=='delivered')
                            <span class="badge badge-success">{{$order->status}}</span>
                        @else
                            <span class="badge badge-danger">{{$order->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('order.show',$order->id)}}" class="btn btn-warning btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="view" data-placement="bottom"><i class="fas fa-eye"></i></a>
                        <a href="{{route('order.edit',$order->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('order.destroy',[$order->id])}}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id="{{$order->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$orders->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
