<div class="public-breadcrumb">
    <a href="javascript:history.back()">&#8592; Back</a>
</div>

<h2 style="color:#e0e0e0; margin-bottom:1.5rem;">
    @if ($location_data->get('icon'))
        <img src="{{ abs_photo($location_data->get('icon')) }}" alt="icon" style="height:32px; vertical-align:middle; margin-right:.5rem; border-radius:4px;">
    @endif
    {{ $location_data->get('name') }}
</h2>

@if ($location_data->get('notes'))
<div class="public-notes">{{ $location_data->get('notes') }}</div>
@endif

<div class="public-section-title">
    Plants &mdash; {{ count($plants) }}
</div>

@if (count($plants) > 0)
<div class="plant-grid">
    @foreach ($plants as $plant)
        <?php
            $plant_share = PublicShareModel::getByEntity('plant', $plant->get('id'));
        ?>
        @if ($plant_share)
        <a class="plant-card" href="{{ url('/public/share/token/' . $plant_share->get('token')) }}">
        @else
        <div class="plant-card">
        @endif
            <div class="plant-card-image" style="background-image: url('{{ abs_photo($plant->get('photo')) }}');"></div>
            <div class="plant-card-title">{{ $plant->get('name') }}</div>
            @if ($plant->get('scientific_name'))
            <div class="plant-card-scientific">{{ $plant->get('scientific_name') }}</div>
            @endif
        @if ($plant_share)
        </a>
        @else
        </div>
        @endif
    @endforeach
</div>
@else
<p style="color:#888;">No plants in this collection yet.</p>
@endif
