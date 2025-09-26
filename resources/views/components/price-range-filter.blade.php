<div class="single-widget range">
    <h3 class="title">{{$title}}</h3>
    <div class="price-filter">
        <div class="price-filter-inner">
            <div id="slider-range" data-min="{{$min}}" data-max="{{$max}}"></div>
            <div class="product_filter">
                <button type="submit" class="filter_button">Filter</button>
                <div class="label-input">
                    <span>Range:</span>
                    <input style="" type="text" id="amount" readonly/>
                    <input type="hidden" name="price_range" id="price_range" value="@if (!empty($_GET['price'])){{$_GET['price']}}@endif"/>
                </div>
            </div>
        </div>
    </div>
</div>
