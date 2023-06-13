import fahrtenbuch from './fahrtenbuch.js'
//import weekevents from './weekevents.js'
import entryform from './entryform.js'

const routes = [
  {
    path: '/',
    name: 'fahrtenbuch_today',
    component: fahrtenbuch
  },  
  {
    path: '/:year/:month/:day',
    name:'fahrtenbuch',
    component: fahrtenbuch
  },
  {
    path: '/entry-create/:gattungname',
    name:'entryform_create_gattung',
    component: entryform
  },    
  {
    path: '/entry-create',
    name:'entryform_create',
    component: entryform
  },
  {
    path: '/entry-createfromdate/:datenames_id',
    name:'entry_createfromdate',
    component: entryform
  },    
  {
    path: '/entry-update/:id',
    name:'entryform_update',
    component: entryform
  }           
]

export default routes