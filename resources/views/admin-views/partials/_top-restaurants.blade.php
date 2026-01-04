<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        {{ translate('top selling stores') }}
    </h5>
    @php
        $params = $params ?? session('dash_params') ?? ['zone_id' => 'all'];
        $zone_id = $params['zone_id'] ?? 'all';
    @endphp
    @if ($zone_id != 'all' && class_exists('\App\Models\Zone'))
        @php
            try {
                $zone = \App\Models\Zone::where('id', $zone_id)->first();
                $zone_name = $zone ? $zone->name : translate('messages.all');
            } catch (\Exception $e) {
                $zone_name = translate('messages.all');
            }
        @endphp
    @else
        @php($zone_name = translate('messages.all'))
    @endif
    <a href="{{ route('admin.dashboard') }}" class="fz-12px font-medium text-006AE5">{{ translate('view_all') }}</a>
</div>

<div class="card-body __top-resturant-card">

    @if (count($top_restaurants) > 0)
        <div class="__top-resturant">
            @foreach ($top_restaurants as $key => $item)
                <a href="{{ route('admin.dashboard') }}">
                    <div class="position-relative overflow-hidden">
                        <img class="onerror-image"
                            data-onerror-image="{{ asset('public/assets/admin/img/100x100/1.png') }}"
                            src="{{ $item['logo_full_url'] ?? asset('public/assets/admin/img/100x100/1.png') }}"
                            title="{{ $item?->name }}">
                        <h5 class="info m-0">
                            {{ translate('order : ') }} {{ $item['order_count'] }}
                        </h5>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty--data">
            <img src="{{ asset('assets/admin/svg/illustrations/empty-state.svg') }}" alt="public">
            <h5>
                {{ translate('no_data_found') }}
            </h5>
        </div>

    @endif

</div>

<script src="{{ asset('assets/admin/js/view-pages/common.js') }}"></script>
