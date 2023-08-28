import terminanmeldung from './terminanmeldung.js'
import eventcomments from './eventcomments.js'
import anmeldung from './anmeldung.js'
import einladungen from './einladungen.js'
import einladungsliste from './einladungsliste.js'

const routes = [
  {
    path: '/',
    name: 'terminanmeldung_noid',
    component: terminanmeldung
  },
  {
    path: '/:id',
    name: 'terminanmeldung',
    component: terminanmeldung,
    children: [
      {
        path: 'anmeldung',
        component: anmeldung,
      },
      {
        path: 'kommentare',
        component: eventcomments,
      },
      {
        path: 'einladungen',
        component: einladungen,
      }, 
      {
        path: 'einladungsliste',
        component: einladungsliste,
      },             
    ],
  },
  {
    path: '/comments/:id',
    name: 'eventcomments',
    component: eventcomments
  }              
]

export default routes