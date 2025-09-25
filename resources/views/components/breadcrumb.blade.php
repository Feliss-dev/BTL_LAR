<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        @if (count($elements) > 0)
                            <li>
                                @foreach (array_slice($elements, 0, -1) as $element)
                                    @if (!empty($element->url))
                                        <a href="{{$element->url}}">{{$element->name}}</a>
                                    @else
                                        {{$element->name}}
                                    @endif

                                    <i class="ti-arrow-right"></i>
                                @endforeach

                                @php
                                    $last = $elements[count($elements) - 1];
                                @endphp

                                @if (!empty($last->url))
                                    <a href="{{$last->url}}">{{$element->name}}</a>
                                @else
                                    {{$element->name}}
                                @endif
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
