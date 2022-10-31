import { Controller } from '@hotwired/stimulus';
import * as Turbo from "@hotwired/turbo";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        offUrl: String,
        onUrl: String,
    }

    toggle(event) {
        Turbo.visit(!event.currentTarget.checked ? this.offUrlValue : this.onUrlValue);
    }
}
