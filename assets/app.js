global.$ = global.jQuery = require('jquery');
import {enableSelect2} from './js/select2'

$(document).ready(function () {
    enableSelect2();
});

import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
