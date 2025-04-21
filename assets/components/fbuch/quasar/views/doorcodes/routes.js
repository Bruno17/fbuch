import view from './view.js'
import codeform from './codeform.js'

const routes = [
  {
    path: '/',
    name: 'view',
    component: view
  },    
  {
    path: '/codemember-add/:id',
    name:'codeform_add',
    component: codeform
  },
  {
    path: '/codemember-update/:id',
    name:'codeform_update',
    component: codeform
  }    
]

export default routes