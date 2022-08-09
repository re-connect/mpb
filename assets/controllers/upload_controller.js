import { Controller } from '@hotwired/stimulus';
import * as Uppy from "uppy";
import axios from "axios";
import fr from "@uppy/locales/lib/fr_FR";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = [
    'container'
  ]
  static values = {
    url: String,
    callbackUrl: String,
  }

  connect() {
    const uppy = new Uppy.Core({ locale: fr })
      .use(Uppy.Webcam, {})
      .use(Uppy.Dashboard, {
        trigger: this.containerTarget,
        target: 'body',
        plugins: ['Webcam'],
        allowMultipleUploads: false,
        closeModalOnClickOutside: true,
        closeAfterFinish: true,
        proudlyDisplayPoweredByUppy: false,
      })
      .use(Uppy.XHRUpload, {
        endpoint: this.urlValue,
        formData: true,
        fieldName: 'file',
      })
      .on('complete', () => {
        if (this.callbackUrlValue) {
          axios.get(this.callbackUrlValue).then((response) => {
            this.containerTarget.parentNode.innerHTML = response.data;
          });
        }
      });
  }
}
