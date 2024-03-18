import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

function getRichOption(data, escape) {
  const addedColor = data.color ? ` style="color:${data.color}" ` : '';
  const addedIcon = data.icon ? `<i class="fa fa-${data.icon} me-2" ${addedColor}></i>` : '';

  return `<div ${addedColor}>${addedIcon}${escape(data.text)}</div>`;
}

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
  static targets = ['select']

  connect() {
    new TomSelect(this.selectTarget, {
      create: false,
      render: {
        option: getRichOption,
        item: getRichOption,
      },
      plugins: {
        remove_button:{
          title:'Remove this item',
        }
      }
    });
  }
}
