<div class="plant-details-title">
    <h1>{{ $plant->get('name') }}</h1>
</div>

<div class="margin-vertical">
    @if (isset($public_location_url) && $public_location_url)
        <a class="is-default-link" href="{{ $public_location_url }}">{{ __('app.back_to_list') }}</a>
    @elseif ($location_token)
        <a class="is-default-link" href="{{ url('/public/share/token/' . $location_token) }}">{{ __('app.back_to_list') }}</a>
    @endif
</div>

@if ($plant->get('health_state') !== 'in_good_standing')
<div class="plant-warning">{{ __('app.plant_warning', ['reason' => __('app.' . $plant->get('health_state'))]) }}</div>
@endif

<div class="columns plant-column">
    <div class="column is-two-third">
        <table>
            <thead>
                <tr>
                    <td class="is-half-percent">{{ __('app.attribute') }}</td>
                    <td>{{ __('app.value') }}</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ __('app.name') }}</strong></td>
                    <td>{{ $plant->get('name') }}</td>
                </tr>

                <tr>
                    <td><strong>{{ __('app.scientific_name') }}</strong></td>
                    <td>
                        @if ($plant->get('scientific_name'))
                            @if ((is_string($plant->get('knowledge_link'))) && (strlen($plant->get('knowledge_link')) > 0))
                                <a class="is-default-link" href="{{ $plant->get('knowledge_link') }}" target="_blank">{{ $plant->get('scientific_name') }}</a>
                            @else
                                {{ $plant->get('scientific_name') }}
                            @endif
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>

                <tr>
                    <td><strong>{{ __('app.location') }}</strong></td>
                    <td>{{ LocationsModel::getNameById($plant->get('location')) }}</td>
                </tr>

                @if (plant_attr('last_watered'))
                <tr>
                    <td><strong>{{ __('app.last_watered') }}</strong></td>
                    <td>
                        @if ($plant->get('last_watered'))
                            {{ date('Y-m-d', strtotime($plant->get('last_watered'))) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('last_repotted'))
                <tr>
                    <td><strong>{{ __('app.last_repotted') }}</strong></td>
                    <td>
                        @if ($plant->get('last_repotted'))
                            {{ date('Y-m-d', strtotime($plant->get('last_repotted'))) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('last_fertilised'))
                <tr>
                    <td><strong>{{ __('app.last_fertilised') }}</strong></td>
                    <td>
                        @if ($plant->get('last_fertilised'))
                            {{ date('Y-m-d', strtotime($plant->get('last_fertilised'))) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('lifespan'))
                <tr>
                    <td><strong>{{ __('app.lifespan') }}</strong></td>
                    <td>
                        @if ($plant->get('lifespan'))
                            {{ __('app.' . $plant->get('lifespan')) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('hardy'))
                <tr>
                    <td><strong>{{ __('app.hardy') }}</strong></td>
                    <td>
                        @if (!is_null($plant->get('hardy')))
                            {!! ($plant->get('hardy')) ? '<span class="is-color-yes">' . __('app.yes') . '</span>' : '<span class="is-color-no">' . __('app.no') . '</span>' !!}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('cutting_month'))
                <tr>
                    <td><strong>{{ __('app.cutting_month') }}</strong></td>
                    <td>{!! ($plant->get('cutting_month')) ? UtilsModule::getMonthList()[$plant->get('cutting_month')] : '<span class="is-not-available">N/A</span>' !!}</td>
                </tr>
                @endif

                @if (plant_attr('date_of_purchase'))
                <tr>
                    <td><strong>{{ __('app.date_of_purchase') }}</strong></td>
                    <td>
                        @if ($plant->get('date_of_purchase'))
                            {{ date('Y-m-d', strtotime($plant->get('date_of_purchase'))) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('humidity'))
                <tr>
                    <td><strong>{{ __('app.humidity') }}</strong></td>
                    <td>
                        @if ($plant->get('humidity'))
                            {{ $plant->get('humidity') . '%' }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('light_level'))
                <tr>
                    <td><strong>{{ __('app.light_level') }}</strong></td>
                    <td>
                        @if ($plant->get('light_level'))
                            {{ __('app.' . $plant->get('light_level')) }}
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endif

                @if (plant_attr('health_state'))
                <tr>
                    <td><strong>{{ __('app.health_state') }}</strong></td>
                    <td><span class="plant-state-{{ $plant->get('health_state') }}">{!! ($plant->get('health_state') === 'in_good_standing') ? '<i class="far fa-check-circle is-color-yes"></i>&nbsp;' : '' !!}{{ __('app.' . $plant->get('health_state')) }}</span></td>
                </tr>
                @endif

                @foreach ($custom_attributes as $custom_attribute)
                <tr>
                    <td><strong>{{ $custom_attribute->label }}</strong></td>
                    <td>
                        @if (!is_null($custom_attribute->content))
                            @if (is_bool($custom_attribute->content))
                                {!! ($custom_attribute->content) ? '<span class="is-color-yes">' . __('app.yes') . '</span>' : '<span class="is-color-no">' . __('app.no') . '</span>' !!}
                            @else
                                @if ((is_string($custom_attribute->content)) && ((strpos($custom_attribute->content, 'http://') === 0) || (strpos($custom_attribute->content, 'https://') === 0)))
                                    <a class="is-default-link" href="{{ $custom_attribute->content }}" target="_blank">{{ $custom_attribute->content }}</a>
                                @else
                                    {{ $custom_attribute->content }}
                                @endif
                            @endif
                        @else
                            <span class="is-not-available">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="column is-one-third">
        <div class="plant-photo" style="background-image: url('{{ abs_photo($plant->get('photo')) }}');">
            <div class="plant-photo-overlay">
                <div class="plant-photo-view is-pointer" onclick="window.open('{{ str_replace('_thumb', '', abs_photo($plant->get('photo'))) }}', '_blank');"><i class="fas fa-expand fa-lg"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="columns plant-column">
    <div class="column is-full">
        <div class="plant-tags">
            <div class="plant-tags-content">
                @if (count($tags) > 0)
                    @foreach ($tags as $tag)
                        @if (strlen($tag) > 0)
                            <div class="plant-tags-item">{{ $tag }}</div>
                        @endif
                    @endforeach
                @else
                    <strong class="is-default-text-color">{{ __('app.no_tags_specified') }}</strong>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="columns plant-column">
    <div class="column is-full">
        <div class="plant-notes">
            <div class="plant-notes-content">
                @if (is_string($plant->get('notes')))
                    <pre>{!! UtilsModule::markdown($plant->get('notes')) !!}</pre>
                @else
                    <span class="is-not-available">{{ __('app.no_notes_specified') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="columns plant-column">
    <div class="column is-full">
        <div class="plant-gallery">
            <div class="plant-gallery-title">{{ __('app.photos') }}</div>
            <div class="plant-gallery-photos">
                @if (count($photos) > 0)
                    @foreach ($photos as $photo)
                        <div class="plant-gallery-item">
                            <div class="plant-gallery-item-header">
                                <div class="plant-gallery-item-header-label">{{ $photo->get('label') }}</div>
                            </div>
                            <div class="plant-gallery-item-photo">
                                <a href="{{ abs_photo($photo->get('original')) }}" target="_blank">
                                    <div class="plant-gallery-item-photo-overlay"></div>
                                    <img class="plant-gallery-item-photo-image" src="{{ abs_photo($photo->get('thumb')) }}" alt="photo"/>
                                </a>
                            </div>
                            <div class="plant-gallery-item-footer">
                                {{ (new Carbon($photo->get('created_at')))->diffForHumans() }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <strong>{{ __('app.no_photos_yet') }}</strong>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="columns plant-column">
    <div class="column is-full">
        <div class="plant-log">
            <div class="plant-log-title">{{ __('app.plant_log') }}</div>

            <a name="plant-log-anchor"></a>

            <?php
                $pub_log_has_more = false;
                $pub_log_last_id  = 0;
            ?>

            @if ((is_countable($plant_log_entries)) && (count($plant_log_entries) > 0))
            <div class="table-scroll-horizontally">
                <table id="public-plant-log-table">
                    <thead>
                        <tr>
                            <td>{{ __('app.plant_log_content') }}</td>
                            <td>{{ __('app.plant_log_date') }}</td>
                        </tr>
                    </thead>
                    <tbody id="public-plant-log-body">
                        @foreach ($plant_log_entries as $plant_log_entry)
                        <tr>
                            <td>{{ $plant_log_entry->get('content') }}</td>
                            <td>{{ date('Y-m-d', strtotime($plant_log_entry->get('created_at'))) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <?php
                $pub_log_last_id  = $plant_log_entries->get(count($plant_log_entries) - 1)?->get('id') ?? 0;
                $pub_log_has_more = $pub_log_last_id > 1;
            ?>
            @else
                <strong>{{ __('app.no_plant_log_entries_yet') }}</strong>
            @endif

            @if ($pub_log_has_more)
            <div class="plant-log-action" id="public-log-more-wrap">
                <button class="button is-light" id="public-log-more-btn" onclick="publicLoadMoreLog({{ (int)$plant->get('id') }}, {{ (int)$pub_log_last_id }});">{{ __('app.load_more') }}</button>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function publicLoadMoreLog(plantId, fromId) {
    fetch('/plants/log/fetch?plant=' + plantId + '&paginate=' + fromId)
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            if (resp.code !== 200 || !resp.data || resp.data.length === 0) {
                document.getElementById('public-log-more-wrap').style.display = 'none';
                return;
            }
            var tbody = document.getElementById('public-plant-log-body');
            resp.data.forEach(function(entry) {
                var tr = document.createElement('tr');
                var td1 = document.createElement('td');
                td1.textContent = entry.content;
                var td2 = document.createElement('td');
                td2.textContent = entry.created_at;
                tr.appendChild(td1);
                tr.appendChild(td2);
                tbody.appendChild(tr);
            });
            var lastId = resp.data[resp.data.length - 1].id;
            if (resp.data.length < 10 || lastId <= 1) {
                document.getElementById('public-log-more-wrap').style.display = 'none';
            } else {
                document.getElementById('public-log-more-btn').setAttribute('onclick', 'publicLoadMoreLog(' + plantId + ', ' + lastId + ')');
            }
        });
}
</script>
