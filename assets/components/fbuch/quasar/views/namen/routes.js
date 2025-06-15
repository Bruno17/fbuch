import view from './view.js'
import memberform from './memberform.js'
import skills from './skills.js'

const routes = [
  {
    path: '/',
    name: 'view',
    component: view
  },    
  {
    path: '/member-create',
    name:'memberform_create',
    component: memberform
  },
  {
    path: '/member-update/:id',
    name:'memberform_update',
    component: memberform
  },
  {
    path: '/skills/:id',
    name:'skills',
    component: skills
  }          
]

export default routes