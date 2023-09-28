import './bootstrap.js';
import '@popperjs/core';
import 'tom-select/dist/scss/tom-select.scss';
import './styles/app.scss';
import enableTooltips from './scripts/turbo_helper';

global.$ = global.jQuery = require('jquery');

enableTooltips();
