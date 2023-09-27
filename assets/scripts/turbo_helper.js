import { Tooltip } from "bootstrap";
function enableTooltips() {
  [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    .map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
}

export default function enableTooltipsOnTurboRender() {
  enableTooltips();

  document.addEventListener('turbo:render', () => {
    enableTooltips()
  });
}
