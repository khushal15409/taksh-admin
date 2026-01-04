<h4 class="m-0 mr-1">
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
    {{ $zone_name ?? translate('messages.all') }}
</h4>
