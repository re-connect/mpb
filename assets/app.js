global.$ = global.jQuery = require('jquery');

import '@popperjs/core';
import {Tooltip} from 'bootstrap';
import 'tom-select/dist/scss/tom-select.scss';

import './styles/app.scss';
import enableTooltips from './scripts/turbo_helper'

// start the Stimulus application
import './bootstrap';

enableTooltips();
