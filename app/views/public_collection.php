<h1>{{ $location_data->get('name') }}</h1>

<div class="margin-vertical">
    <a class="is-default-link is-fixed-button-link" href="{{ url('/') }}">{{ __('app.back_to_dashboard') }}</a>
</div>

@if ($location_data->get('notes'))
<div class="location-notes" style="margin-bottom:1.5rem;">
    <div class="location-notes-title">{{ __('app.notes') }}</div>
    <div style="padding:.75rem 0; white-space:pre-wrap;">{!! UtilsModule::markdown($location_data->get('notes')) !!}</div>
</div>
<div class="is-dark-delimiter"><hr/></div>
@endif

<div class="plants">
    @if (count($plants) > 0)
        @foreach ($plants as $plant)
        <a href="{{ url('/plants/details/' . $plant->get('id')) }}">
            <div class="plant-card plant-filter-text-root">
                <div class="plant-card-image" style="background-image: url('{{ abs_photo($plant->get('photo')) }}');">
                    <div class="plant-card-overlay"></div>
                </div>
                <div class="plant-card-health-state">
                    @if ($plant->get('health_state') !== 'in_good_standing')
                        <i class="{{ PlantsModel::$plant_health_states[$plant->get('health_state')]['icon'] }} plant-state-{{ $plant->get('health_state') }}"></i>
                    @endif
                </div>
                <div class="plant-card-title plant-filter-text-target {{ ((strlen($plant->get('name')) > PlantsModel::PLANT_LONG_TEXT_THRESHOLD) ? 'plant-card-title-longtext' : '') }}">
                    {{ $plant->get('name') }}
                </div>
            </div>
        </a>
        @endforeach
    @else
        <div class="plants-empty">
            <div class="plants-empty-image">
                <img src="{{ asset('img/plantsempty.png') }}" alt="image"/>
            </div>
            <div class="plants-empty-text">{{ __('app.content_empty') }}</div>
        </div>
    @endif
</div>

@if ((is_countable($location_log_entries)) && (count($location_log_entries) > 0))
<div class="is-dark-delimiter"><hr/></div>
<div class="location-log">
    <div class="location-log-title">{{ __('app.location_log') }}</div>
    <table>
        <thead>
            <tr>
                <td>{{ __('app.location_log_content') }}</td>
                <td>{{ __('app.location_log_date') }}</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($location_log_entries as $entry)
            <tr>
                <td>{{ $entry->get('content') }}</td>
                <td>{{ date('Y-m-d', strtotime($entry->get('created_at'))) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
