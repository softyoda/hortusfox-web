<div class="public-breadcrumb">
    @if ($location_token)
        <a href="{{ url('/public/share/token/' . $location_token) }}">&#8592; Back to collection</a>
    @else
        <a href="javascript:history.back()">&#8592; Back</a>
    @endif
</div>

@if ($plant->get('health_state') !== 'in_good_standing')
<div class="health-warning">
    &#9888; {{ __('app.' . $plant->get('health_state')) }}
</div>
@endif

<div class="columns">
    <div class="column-main">
        <h2 style="color:#e0e0e0; margin-bottom:1.2rem;">{{ $plant->get('name') }}</h2>

        <table class="public-table">
            @if ($plant->get('scientific_name'))
            <tr>
                <td>Scientific name</td>
                <td>
                    @if ($plant->get('knowledge_link'))
                        <a href="{{ $plant->get('knowledge_link') }}" target="_blank" style="color:#a8d8a8;">{{ $plant->get('scientific_name') }}</a>
                    @else
                        {{ $plant->get('scientific_name') }}
                    @endif
                </td>
            </tr>
            @endif

            @if ($plant->get('location'))
            <tr>
                <td>Location</td>
                <td>{{ LocationsModel::getNameById($plant->get('location')) }}</td>
            </tr>
            @endif

            @if (plant_attr('last_watered') && $plant->get('last_watered'))
            <tr>
                <td>Last watered</td>
                <td>{{ date('Y-m-d', strtotime($plant->get('last_watered'))) }}</td>
            </tr>
            @endif

            @if (plant_attr('last_repotted') && $plant->get('last_repotted'))
            <tr>
                <td>Last repotted</td>
                <td>{{ date('Y-m-d', strtotime($plant->get('last_repotted'))) }}</td>
            </tr>
            @endif

            @if (plant_attr('last_fertilised') && $plant->get('last_fertilised'))
            <tr>
                <td>Last fertilised</td>
                <td>{{ date('Y-m-d', strtotime($plant->get('last_fertilised'))) }}</td>
            </tr>
            @endif

            @if (plant_attr('lifespan') && $plant->get('lifespan'))
            <tr>
                <td>Lifespan</td>
                <td>{{ __('app.' . $plant->get('lifespan')) }}</td>
            </tr>
            @endif

            @if (plant_attr('hardy') && !is_null($plant->get('hardy')))
            <tr>
                <td>Hardy</td>
                <td>{!! ($plant->get('hardy')) ? '<span style="color:#a8d8a8;">Yes</span>' : '<span style="color:#e07070;">No</span>' !!}</td>
            </tr>
            @endif

            @if (plant_attr('cutting_month') && $plant->get('cutting_month'))
            <tr>
                <td>Cutting month</td>
                <td>{{ UtilsModule::getMonthList()[$plant->get('cutting_month')] }}</td>
            </tr>
            @endif

            @if (plant_attr('date_of_purchase') && $plant->get('date_of_purchase'))
            <tr>
                <td>Date of purchase</td>
                <td>{{ date('Y-m-d', strtotime($plant->get('date_of_purchase'))) }}</td>
            </tr>
            @endif

            @if (plant_attr('humidity') && $plant->get('humidity'))
            <tr>
                <td>Humidity</td>
                <td>{{ $plant->get('humidity') }}%</td>
            </tr>
            @endif

            @if (plant_attr('light_level') && $plant->get('light_level'))
            <tr>
                <td>Light level</td>
                <td>{{ __('app.' . $plant->get('light_level')) }}</td>
            </tr>
            @endif

            @if (plant_attr('health_state'))
            <tr>
                <td>Health</td>
                <td>{{ __('app.' . $plant->get('health_state')) }}</td>
            </tr>
            @endif

            @foreach ($custom_attributes as $attr)
                @if (!is_null($attr->content))
                <tr>
                    <td>{{ $attr->label }}</td>
                    <td>
                        @if (is_bool($attr->content))
                            {!! $attr->content ? '<span style="color:#a8d8a8;">Yes</span>' : '<span style="color:#e07070;">No</span>' !!}
                        @elseif ((is_string($attr->content)) && ((strpos($attr->content, 'http://') === 0) || (strpos($attr->content, 'https://') === 0)))
                            <a href="{{ $attr->content }}" target="_blank" style="color:#a8d8a8;">{{ $attr->content }}</a>
                        @else
                            {{ $attr->content }}
                        @endif
                    </td>
                </tr>
                @endif
            @endforeach
        </table>

        @if (count($tags) > 0)
        <div class="public-section-title">Tags</div>
        <div>
            @foreach ($tags as $tag)
                @if (strlen($tag) > 0)
                <span class="public-tag">{{ $tag }}</span>
                @endif
            @endforeach
        </div>
        @endif

        @if ($plant->get('notes'))
        <div class="public-section-title">Notes</div>
        <div class="public-notes">{!! UtilsModule::markdown($plant->get('notes')) !!}</div>
        @endif
    </div>

    <div class="column-photo">
        @if ($plant->get('photo') !== 'placeholder.jpg')
        <a href="{{ str_replace('_thumb', '', abs_photo($plant->get('photo'))) }}" target="_blank">
            <img class="public-plant-photo" src="{{ abs_photo($plant->get('photo')) }}" alt="{{ $plant->get('name') }}"/>
        </a>
        @endif
    </div>
</div>

@if (count($photos) > 0)
<div class="public-section-title">Gallery</div>
<div class="public-gallery">
    @foreach ($photos as $photo)
    <div class="public-gallery-item">
        <a href="{{ abs_photo($photo->get('original')) }}" target="_blank">
            <img src="{{ abs_photo($photo->get('thumb')) }}" alt="{{ $photo->get('label') }}"/>
        </a>
        <div class="label">{{ $photo->get('label') }}</div>
    </div>
    @endforeach
</div>
@endif

<div class="public-section-title">Journal</div>

<?php $initial_log = []; ?>
@if ((is_countable($plant_log_entries)) && (count($plant_log_entries) > 0))
    <?php
        foreach ($plant_log_entries as $entry) {
            $initial_log[] = [
                'id' => $entry->get('id'),
                'content' => $entry->get('content'),
                'created_at' => date('Y-m-d', strtotime($entry->get('created_at'))),
                'updated_at' => date('Y-m-d', strtotime($entry->get('updated_at'))),
            ];
        }
    ?>
@endif

<div id="public-log-section">
    <table class="public-log-table" v-if="logEntries.length > 0">
        <thead>
            <tr>
                <th>Entry</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="entry in logEntries" :key="entry.id">
                <td>{{ "{{" }} entry.content {{ "}}" }}</td>
                <td style="white-space:nowrap; color:#888; font-size:.85rem;">{{ "{{" }} entry.created_at {{ "}}" }}</td>
            </tr>
        </tbody>
    </table>
    <p v-else style="color:#888;">No journal entries yet.</p>

    <button v-if="!noMoreLogs && logEntries.length >= 10" class="btn-load-more" @click="loadMoreLog()">Load more</button>
</div>

<script>
    (function() {
        const initialLog = <?php echo json_encode($initial_log); ?>;
        const plantToken = <?php echo json_encode($plant_token); ?>;
        app.initLog(initialLog, plantToken);

        // Mount the log section inside the existing Vue app
        document.getElementById('public-log-section').__vue_app__ = undefined;
    })();
</script>
