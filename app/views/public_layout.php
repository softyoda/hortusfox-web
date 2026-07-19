<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ app('workspace') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}"/>
        <script src="{{ asset('js/vue.min.js') }}"></script>
        <style>
            body { background: #1a1a2e; color: #e0e0e0; }
            .public-header { background: #16213e; padding: 1rem 2rem; display: flex; align-items: center; gap: 1rem; border-bottom: 2px solid #0f3460; }
            .public-header img { height: 40px; }
            .public-header h1 { margin: 0; font-size: 1.4rem; color: #e0e0e0; }
            .public-header .public-badge { margin-left: auto; background: #0f3460; color: #a8d8a8; padding: 0.3rem 0.8rem; border-radius: 4px; font-size: 0.85rem; }
            .public-container { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem; }
            .public-breadcrumb { font-size: 0.9rem; margin-bottom: 1.5rem; color: #888; }
            .public-breadcrumb a { color: #a8d8a8; text-decoration: none; }
            .public-breadcrumb a:hover { text-decoration: underline; }
            .plant-card { background: #16213e; border-radius: 8px; overflow: hidden; transition: transform .15s; display: block; color: inherit; text-decoration: none; }
            .plant-card:hover { transform: translateY(-3px); }
            .plant-card-image { height: 160px; background-size: cover; background-position: center; }
            .plant-card-title { padding: .6rem .8rem; font-weight: 600; font-size: .95rem; }
            .plant-card-scientific { padding: 0 .8rem .6rem; font-size: .8rem; color: #888; font-style: italic; }
            .plant-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
            .public-plant-photo { width: 100%; max-height: 380px; object-fit: contain; border-radius: 8px; background: #16213e; }
            .public-table { width: 100%; border-collapse: collapse; }
            .public-table td { padding: .5rem .8rem; border-bottom: 1px solid #0f3460; vertical-align: top; }
            .public-table td:first-child { font-weight: 600; width: 35%; color: #a8d8a8; }
            .public-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .8rem; margin-top: 1rem; }
            .public-gallery-item img { width: 100%; border-radius: 6px; }
            .public-gallery-item .label { font-size: .8rem; color: #888; margin-top: .3rem; }
            .public-section-title { font-size: 1.1rem; font-weight: 700; color: #a8d8a8; margin: 2rem 0 .8rem; border-bottom: 1px solid #0f3460; padding-bottom: .4rem; }
            .public-log-table { width: 100%; border-collapse: collapse; }
            .public-log-table td { padding: .5rem .8rem; border-bottom: 1px solid #0f3460; vertical-align: top; font-size: .9rem; }
            .public-log-table th { padding: .5rem .8rem; text-align: left; color: #a8d8a8; border-bottom: 2px solid #0f3460; font-size: .85rem; }
            .public-tag { display: inline-block; background: #0f3460; color: #a8d8a8; border-radius: 12px; padding: .2rem .7rem; font-size: .8rem; margin: .2rem; }
            .is-not-available { color: #555; }
            .btn-load-more { background: #0f3460; color: #a8d8a8; border: none; padding: .5rem 1.5rem; border-radius: 4px; cursor: pointer; margin-top: .8rem; }
            .btn-load-more:hover { background: #16213e; }
            .public-notes { background: #16213e; border-radius: 6px; padding: 1rem; margin-top: .5rem; white-space: pre-wrap; font-size: .95rem; }
            .health-warning { background: #4a2600; color: #ffa040; border-radius: 6px; padding: .6rem 1rem; margin-bottom: 1rem; }
            .back-link { color: #a8d8a8; text-decoration: none; font-size: .9rem; }
            .back-link:hover { text-decoration: underline; }
            .columns { display: flex; gap: 2rem; flex-wrap: wrap; }
            .column-main { flex: 1 1 55%; }
            .column-photo { flex: 1 1 30%; }
        </style>
    </head>
    <body>
        <div id="app">
            <div class="public-header">
                <img src="{{ asset('logo.png') }}" alt="Logo"/>
                <h1>{{ app('workspace') }}</h1>
                <span class="public-badge"><i class="fas fa-eye"></i> Read-only</span>
            </div>

            <div class="public-container">
                {%content%}
            </div>
        </div>

        <script>
            // Simple Vue app just to handle log "load more" on plant page
            const app = Vue.createApp({
                data() {
                    return {
                        logEntries: [],
                        lastLogId: null,
                        noMoreLogs: false,
                        plantToken: null,
                    };
                },
                methods: {
                    initLog(initialEntries, plantToken) {
                        this.plantToken = plantToken;
                        this.logEntries = initialEntries;
                        if (initialEntries.length > 0) {
                            this.lastLogId = initialEntries[initialEntries.length - 1].id;
                        }
                    },
                    loadMoreLog() {
                        if (!this.plantToken || !this.lastLogId) return;
                        fetch('/public/share/log/fetch?token=' + encodeURIComponent(this.plantToken) + '&paginate=' + this.lastLogId)
                            .then(r => r.json())
                            .then(resp => {
                                if (resp.code === 200 && resp.data.length > 0) {
                                    this.logEntries = this.logEntries.concat(resp.data);
                                    this.lastLogId = resp.data[resp.data.length - 1].id;
                                    if (resp.data.length < 10) this.noMoreLogs = true;
                                } else {
                                    this.noMoreLogs = true;
                                }
                            });
                    }
                }
            }).mount('#app');
        </script>
    </body>
</html>
