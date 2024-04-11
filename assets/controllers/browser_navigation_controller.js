import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    back() {
        window.history.back();
    }
}
