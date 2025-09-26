<div class="container fixed-top">
    @if (session('success'))
        <div class="alert alert-success alert-dismissable fade show" style="top: 1rem; transition: top 0.75s ease;">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{session('success')}}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissable fade show" style="top: 1rem; transition: top 0.75s ease;">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{session('error')}}
        </div>
    @endif
</div>
