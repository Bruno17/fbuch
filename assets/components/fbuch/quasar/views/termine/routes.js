import terminanmeldung from './terminanmeldung.js'
import eventcomments from './eventcomments.js'
import anmeldung from './anmeldung.js'
import einladungen from './einladungen.js'

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
        // UserProfile will be rendered inside User's <router-view>
        // when /user/:id/profile is matched
        path: 'anmeldung',
        component: anmeldung,
      },
      {
        // UserPosts will be rendered inside User's <router-view>
        // when /user/:id/posts is matched
        path: 'kommentare',
        component: eventcomments,
      },
      {
        // UserPosts will be rendered inside User's <router-view>
        // when /user/:id/posts is matched
        path: 'einladungen',
        component: einladungen,
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