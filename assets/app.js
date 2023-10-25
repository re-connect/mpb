import '@popperjs/core';
import './bootstrap.js';
import enableTooltips from './scripts/turbo_helper';

global.$ = global.jQuery = require('jquery');

enableTooltips();
