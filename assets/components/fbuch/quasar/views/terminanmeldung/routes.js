import terminanmeldung from './terminanmeldung.js'

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
  }             
]

export default routes