import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['select']

  connect() {
    new TomSelect(this.selectTarget, {});
  }
}
