<div>
    <div class="float-right">
        <label>Lọc tên</label>
        <input type="text" wire:model.live.debounce.250ms="filter">
    </div>

    <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
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
            @foreach ($brands as $brand)
                <tr>
                    <td>{{ $brand->id }}</td>
                    <td>{{ $brand->title }}</td>
                    <td>{{ $brand->slug }}</td>
                    <td>
                        @if ($brand->status == 'active')
                            <span class="badge badge-success">Hoạt động</span>
                        @else
                            <span class="badge badge-warning">Không hoạt động</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('brand.edit', $brand->id) }}"
                           class="btn btn-primary btn-sm float-left mr-1"
                           style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                           title="Sửa" data-placement="bottom"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('brand.destroy', [$brand->id]) }}">
                            @csrf
                            @method('delete')
                            <button class="btn btn-danger btn-sm dltBtn" data-id={{ $brand->id }}
                                                    style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                            data-placement="bottom" title="Xóa"><i
                                class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="flex justify-content-center w-full">
        {{$brands->links('vendor.pagination.bootstrap-5')}}
    </div>
</div>
