<div class="public-breadcrumb">
    <a class="back-link" href="{{ url('/') }}">&#8592; Back</a>
</div>

<h2 style="color:#e0e0e0; margin-bottom:1rem;">
    @if ($location_data->get('icon'))
        <img src="{{ abs_photo($location_data->get('icon')) }}" alt="icon" style="height:32px; vertical-align:middle; margin-right:.5rem; border-radius:4px;">
    @endif
    {{ $location_data->get('name') }}
</h2>

@if ($location_data->get('notes'))
<div class="public-section-title">Notes</div>
<div class="public-notes">{!! UtilsModule::markdown($location_data->get('notes')) !!}</div>
@endif

<div class="public-section-title">
    Plants &mdash; {{ count($plants) }}
</div>

@if (count($plants) > 0)
<div class="plant-grid">
    @foreach ($plants as $plant)
    <a class="plant-card" href="{{ url('/plants/details/' . $plant->get('id')) }}">
        <div class="plant-card-image" style="background-image: url('{{ abs_photo($plant->get('photo')) }}');"></div>
        <div class="plant-card-title">{{ $plant->get('name') }}</div>
        @if ($plant->get('scientific_name'))
        <div class="plant-card-scientific">{{ $plant->get('scientific_name') }}</div>
        @endif
    </a>
    @endforeach
</div>
@else
<p style="color:#888;">No plants in this collection yet.</p>
@endif

@if ((is_countable($location_log_entries)) && (count($location_log_entries) > 0))
<div class="public-section-title">Location Journal</div>
<table class="public-log-table">
    <thead>
        <tr>
            <th>Entry</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($location_log_entries as $entry)
        <tr>
            <td>{{ $entry->get('content') }}</td>
            <td style="white-space:nowrap; color:#888; font-size:.85rem;">{{ date('Y-m-d', strtotime($entry->get('created_at'))) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
