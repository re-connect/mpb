global.$ = global.jQuery = require('jquery');
import {enableSelect2} from './js/select2'

$(document).ready(function () {
    enableSelect2();
});

// any CSS you import will output into a single css file (app.css in this case)
import 'bootswatch/dist/zephyr/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css'

// start the Stimulus application
import './bootstrap';
