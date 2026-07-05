import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import Swal from 'sweetalert2';

import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.Swal = Swal;

Alpine.start();
