import {Controller} from '@hotwired/stimulus';
import Uppy from '@uppy/core'
import axios from "axios";
import fr from "@uppy/locales/lib/fr_FR";
import XHRUpload from '@uppy/xhr-upload'
import Dashboard from '@uppy/dashboard'
import Webcam from '@uppy/webcam'

// And their styles (for UI plugins)
// With webpack and `style-loader`, you can import them like this:
import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'

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
        new Uppy({locale: fr})
            .use(Webcam, {})
            .use(Dashboard, {
                trigger: this.containerTarget,
                target: 'body',
                plugins: ['Webcam'],
                allowMultipleUploads: false,
                closeModalOnClickOutside: true,
                closeAfterFinish: true,
                proudlyDisplayPoweredByUppy: false,
            })
            .use(XHRUpload, {
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
