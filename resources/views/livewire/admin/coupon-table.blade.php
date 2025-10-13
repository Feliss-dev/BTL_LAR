<div>
    <div class="float-right">
        <label>Lọc mã</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Phiếu giảm giá</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tfoot>
            <tr>
                <th>ID</th>
                <th>Phiếu giảm giá</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </tfoot>

        <tbody>
            @foreach($coupons as $coupon)
                <tr>
                    <td>{{$coupon->id}}</td>
                    <td>{{$coupon->code}}</td>
                    <td>
                        @if($coupon->type=='fixed')
                            <span class="badge badge-primary">{{$coupon->type}}</span>
                        @else
                            <span class="badge badge-warning">{{$coupon->type}}</span>
                        @endif
                    </td>
                    <td>
                        @if($coupon->type=='fixed')
                            {{number_format($coupon->value, 0, ',', '.')}} đ
                        @else
                            {{$coupon->value}}%
                        @endif</td>
                    <td>
                        @if($coupon->status=='active')
                            <span class="badge badge-success">{{$coupon->status}}</span>
                        @else
                            <span class="badge badge-warning">{{$coupon->status}}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{route('coupon.edit',$coupon->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{route('coupon.destroy',[$coupon->id])}}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id="{{$coupon->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$coupons->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
