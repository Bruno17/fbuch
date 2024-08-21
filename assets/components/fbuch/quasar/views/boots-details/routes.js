import view from './view.js'
import schaeden from './schaeden.js'
import riggerungen from './riggerungen.js'
import einstellungen from './einstellungen.js'
import fahrten from './fahrten.js'
import schadenform from './schadenform.js'
import riggerungform from './riggerungform.js'
import einstellungform from './einstellungform.js'

const routes = [
  {
    path: '/schaden-create/:bootid',
    name:'schadenform_create',
    component: schadenform
  },
  {
    path: '/schaden-update/:id',
    name:'schadenform_update',
    component: schadenform
  }, 
  {
    path: '/einstellung-create/:bootid',
    name:'einstellungform_create',
    component: einstellungform
  },
  {
    path: '/einstellung-update/:id',
    name:'einstellungform_update',
    component: einstellungform
  },   
  {
    path: '/riggerung-create/:bootid',
    name:'riggerungform_create',
    component: riggerungform
  },
  {
    path: '/riggerung-update/:id',
    name:'riggerungform_update',
    component: riggerungform
  },     
  {
    path: '/:id',
    name: 'view',
    component: view,
    children: [
      {
        path: 'schaeden',
        component: schaeden,
      },
      {
        path: 'riggerungen',
        component: riggerungen,
      },
      {
        path: 'einstellungen',
        component: einstellungen,
      }, 
      {
        path: 'fahrten',
        component: fahrten,
      }           
    ]
  }        
]

export default routes