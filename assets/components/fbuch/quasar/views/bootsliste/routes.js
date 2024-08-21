import view from './view.js'

const routes = [
  {
    path: '/',
    name: 'view',
    component: view
  },
  {
    path: '/:gattung_id',
    name: 'view_gattung',
    component: view
  }            
]

export default routes