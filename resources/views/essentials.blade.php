<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Ordering System</title>
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}" />
  </head>
  <body>

    @yield('page')

  	<script src="{{ elixir('js/app.js') }}"></script>
  </body>
</html>