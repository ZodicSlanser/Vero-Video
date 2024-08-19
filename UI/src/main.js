import { createApp } from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faPlay, faPause, faVolumeUp, faVolumeMute, faExpand, faClosedCaptioning } from '@fortawesome/free-solid-svg-icons';
library.add(faPlay, faPause, faVolumeUp, faVolumeMute, faExpand, faClosedCaptioning, );

// Create and mount the Vue app
const app = createApp(App)
app.component('font-awesome-icon', FontAwesomeIcon)
app.mount('#app')



