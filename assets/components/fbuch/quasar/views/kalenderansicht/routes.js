import monthcalendar from './monthcalendar.js'
import weekevents from './weekevents.js'
import eventform from './eventform.js'

const routes = [
  {
    path: '/:year/:month',
    name: 'monthcalendar',
    component: monthcalendar
  },
  {
    path: '/',
    name: 'monthcalendar_now',
    component: monthcalendar
  },  
  {
    path: '/events/:view/:year/:month/:day',
    name:'events',
    component: weekevents
  }, 
  {
    path: '/event-update/:id',
    name:'eventform_update',
    component: eventform
  },
  {
    path: '/event-create/:year/:month/:day',
    name: 'eventform_create',
    component: eventform
  },
  {
    path: '/event-create',
    name: 'eventform_create_now',
    component: eventform
  }       
]

export default routes