global.$ = global.jQuery = require('jquery');

import '@popperjs/core';
import {Tooltip} from 'bootstrap';
import 'tom-select/dist/scss/tom-select.scss';

import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    .map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
