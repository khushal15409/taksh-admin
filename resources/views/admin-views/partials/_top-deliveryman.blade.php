<div class="card-header border-0 order-header-shadow">
    <h5 class="card-title d-flex justify-content-between">
        <span>{{ translate('messages.top_deliveryman') }}</span>
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
    <a href="{{ route('admin.dashboard') }}"
        class="fz-12px font-medium text-006AE5">{{ translate('view_all') }}</a>
</div>

<div class="card-body">

    @if (count($top_deliveryman) > 0)
        <div class="top--selling">
            @foreach ($top_deliveryman as $key => $item)
                <a class="grid--card" href="{{ route('admin.dashboard') }}">
                    <img class="onerror-image" data-onerror-image="{{ asset('public/assets/admin/img/admin.png') }}"
                        src="{{ $item['image_full_url'] ?? asset('public/assets/admin/img/admin.png') }}"
                        alt="{{ $item['f_name'] }}">
                    <div class="cont pt-2">
                        <h6 class="mb-1">{{ $item['f_name'] ?? 'Not exist' }}</h6>
                        <span>{{ $item['phone'] }}</span>
                    </div>
                    <div class="ml-auto">
                        <span class="badge badge-soft">{{ translate('messages.orders') }} :
                            {{ $item['orders_count'] }}</span>
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
