var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    mix.sass(
        'app.scss',
        'public/css/app.css',
        {includePaths: ['vendor/bower_components/foundation/scss']}
    )

    .styles(['foundation-icon-fonts/foundation-icons.css',
                'jQuery-contextMenu/src/jquery.contextMenu.css',
                'datatables/media/css/dataTables.foundation.min.css',
                'datatables/media/Buttons-1.0.3/css/buttons.foundation.min.css',
                '../../public/css/app.css'],
                'public/css/app.css',
                'vendor/bower_components')
    .scripts(
        ['vendor/modernizr.js', '../../../jquery/jquery-1.11.3.min.js',
        'foundation.min.js', '../../../bower_components/jQuery-contextMenu/src/jquery.contextMenu.js',
        '../../../bower_components/jQuery-contextMenu/src/jquery.ui.position.js',
        '../../../bower_components/jquery-form/jquery.form.js',
        '../../../bower_components/datatables/media/js/jquery.dataTables.min.js',
        '../../../bower_components/datatables/media/js/dataTables.foundation.min.js',
        '../../../bower_components/datatables/media/js/moment.js',
        '../../../bower_components/datatables/media/Buttons-1.0.3/js/dataTables.buttons.min.js',
        '../../../bower_components/datatables/media/Buttons-1.0.3/js/buttons.foundation.min.js',
        '../../../bower_components/jszip/dist/jszip.min.js',
        '../../../bower_components/datatables/media/Buttons-1.0.3/js/buttons.html5.min.js',
        '../../../bower_components/datatables/media/Buttons-1.0.3/js/buttons.flash.min.js',
        '../../../bower_components/datatables/media/Buttons-1.0.3/js/buttons.print.min.js',
        '../../../../resources/assets/js/app.js'], // Source files
        'public/js/app.js',
        'vendor/bower_components/foundation/js/')

    .version(['public/css/app.css', 'public/js/app.js']);

});