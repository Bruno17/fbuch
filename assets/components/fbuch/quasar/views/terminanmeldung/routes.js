import terminanmeldung from './terminanmeldung.js'
import eventcomments from './eventcomments.js'

const routes = [
  {
    path: '/',
    name: 'terminanmeldung_noid',
    component: terminanmeldung
  },
  {
    path: '/:id',
    name: 'terminanmeldung',
    component: terminanmeldung
  },
  {
    path: '/comments/:id',
    name: 'eventcomments',
    component: eventcomments
  }              
]

export default routes