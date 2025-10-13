@extends('backend.layouts.master')

@section('main-content')
    <div class="card shadow mb-4">
        <x-alert-container/>

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Category Lists</h6>
            <a href="{{route('category.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip"
               data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Category</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if(count($categories)>0)
                    <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Is Parent</th>
                            <th>Parent Category</th>
                            <th>Photo</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>S.N.</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Is Parent</th>
                            <th>Parent Category</th>
                            <th>Photo</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                        <tbody>

                        @foreach($categories as $category)
                            @php
                                @endphp
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
                                                data-id={{$category->id}} style="height:30px;
                                                width:30px;border-radius:50%
                                        " data-toggle="tooltip" data-placement="bottom" title="Delete"><i
                                            class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div
                        class="d-flex flex-row justify-content-center">{{$categories->links('vendor.pagination.bootstrap-5')}}</div>
                @else
                    <h6 class="text-center">No Categories found!!! Please create Category</h6>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Page level custom scripts -->
    <script>

        $('#banner-dataTable').DataTable({
            "columnDefs": [
                {
                    "orderable": false,
                    "targets": [3, 4, 5]
                }
            ]
        });

        // Sweet alert

        function deleteData(id) {

        }
    </script>
    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.dltBtn').click(function (e) {
                var form = $(this).closest('form');
                var dataID = $(this).data('id');
                // alert(dataID);
                e.preventDefault();
                swal({
                    title: "Are you sure?",
                    text: "Khi xóa, bạn sẽ không thể khôi phục dữ liệu này!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Dữ liệu của bạn vẫn an toàn!");
                        }
                    });
            })
        })
    </script>
@endpush
