import { createApp } from 'vue'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

// Import the Font Awesome icons you will use
import { faPlay, faPause, faVolumeMute, faVolumeUp, faExpand } from '@fortawesome/free-solid-svg-icons'

// Add the icons to the library
library.add(faPlay, faPause, faVolumeMute, faVolumeUp, faExpand)

// Create and mount the Vue app
const app = createApp(App)
app.component('font-awesome-icon', FontAwesomeIcon)
app.mount('#app')



