import './bootstrap';
import '../sass/ownapp.scss';
import flatpickr from "flatpickr";

document.addEventListener("DOMContentLoaded", () => {
  const el = document.querySelector(".js-birthdate");
  if (!el) return;

  flatpickr(el, {
    dateFormat: "Y-m-d",          // matches what Laravel expects
    altInput: true,              // nicer readable display
    altFormat: "F j, Y",         // e.g. May 1, 2026
    maxDate: "today",
    allowInput: true,
    disableMobile: true,         // forces Flatpickr even on mobile
  });
});