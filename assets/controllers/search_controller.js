import {Controller} from '@hotwired/stimulus';
import axios from "axios";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['container', 'form']

    search() {
        axios({
            method: this.formTarget.method,
            url: this.formTarget.action,
            data: new FormData(this.formTarget),
        })
            .then((response) => {
                if (response && response.data) {
                    this.containerTarget.innerHTML = response.data.trim();
                }
            })
            .catch((reason) => {
                alert('There has been an error searching for bugs');
                console.log(reason);
            });
    }
}
