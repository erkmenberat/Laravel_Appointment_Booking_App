// Axios global verfügbar machen, damit Requests überall einheitlich sind.
import axios from 'axios';
window.axios = axios;

// Kennzeichnet Requests als AJAX/XHR für Backend-Middleware und Controller.
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
