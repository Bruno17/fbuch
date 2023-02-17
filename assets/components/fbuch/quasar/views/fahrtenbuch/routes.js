import fahrtenbuch from './fahrtenbuch.js'
//import weekevents from './weekevents.js'
//import eventform from './eventform.js'

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
  }   
]

export default routes