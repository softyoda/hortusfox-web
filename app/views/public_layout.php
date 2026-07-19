<!doctype html>
<html lang="{{ getLocale() }}">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ app('workspace') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bulma.css') }}"/>
        <script src="{{ asset('js/app.js', true) }}"></script>
        <style>
            .public-readonly-badge {
                display: inline-flex; align-items: center; gap: .4rem;
                background: #2d6a4f; color: #d8f3dc; padding: .25rem .75rem;
                border-radius: 4px; font-size: .8rem; margin-left: auto;
            }
            .public-log-load-more {
                background: none; border: none; color: var(--link-color, #3273dc);
                cursor: pointer; font-size: .9rem; padding: .5rem 0; text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div id="app">
            <nav class="navbar is-dark" role="navigation" aria-label="main navigation">
                <div class="navbar-brand">
                    <a class="navbar-item navbar-item-brand is-font-title" href="{{ url('/') }}">
                        <img src="{{ asset('logo.png') }}"/>&nbsp;{{ app('workspace') }}
                    </a>
                </div>
                <div class="navbar-menu">
                    <div class="navbar-end">
                        <div class="navbar-item">
                            <span class="public-readonly-badge">
                                <i class="fas fa-eye"></i> Read-only &mdash; <a href="{{ url('/auth') }}" style="color:#d8f3dc; text-decoration:underline;">Log in to edit</a>
                            </span>
                        </div>
                    </div>
                </div>
            </nav>

            @if (file_exists(public_path() . '/img/banner.jpg'))
            <div class="banner" style="background-image: url('{{ asset('img/banner.jpg') }}');"></div>
            @endif

            <div class="container">
                <div class="columns">
                    <div class="column is-1"></div>
                    <div class="column is-10">
                        <div class="content-inner">
                            {%content%}
                        </div>
                    </div>
                    <div class="column is-1"></div>
                </div>
            </div>
        </div>
    </body>
</html>
