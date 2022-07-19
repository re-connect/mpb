import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['element']

  // ...

  startLoading() {
    const previousContent = this.elementTarget.innerHTML;
    this.elementTarget.innerHTML = '<span class="spinner-border spinner-border-sm me-3" role="status" aria-hidden="true"></span>' + previousContent;
    this.elementTarget.disabled = true;

    setTimeout(() => {
      this.elementTarget.innerHTML = previousContent;
      this.elementTarget.disabled = false;
    }, 2000);
  }
}
