
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') },
      { path: '/client', component: () => import('pages/Client.vue') },
      { path: '/staff', component: () => import('pages/StaffManagement.vue') },
      { path: '/project', component: () => import('pages/Project.vue') },
      { path: '/portfolio', component: () => import('pages/Portfolio.vue') },
      { path: '/vsla', component: () => import('pages/VSLAPanel.vue') },
      { path: '/vsladash', component: () => import('pages/VSLADashboard.vue') },
      { path: '/cycle', component: () => import('pages/Cycle.vue') },
      { path: '/vslahome', component: () => import('pages/VSLAHome.vue') },
      { path: '/member', component: () => import('pages/Member.vue') },
      { path: '/account', component: () => import('pages/Account.vue') },
      { path: '/saving', component: () => import('pages/Saving.vue') },
      { path: '/social', component: () => import('pages/SocialFund.vue') },
      { path: '/loan', component: () => import('pages/Loan.vue') },
      { path: '/request', component: () => import('pages/Request.vue') },
      { path: '/meeting', component: () => import('pages/Meeting.vue') },
      { path: '/donation', component: () => import('pages/Donation.vue') },
      { path: '/spending', component: () => import('pages/Spending.vue') },
      { path: '/share', component: () => import('pages/Shareout.vue') },
      { path: '/penalty', component: () => import('pages/Penalty.vue') },
      { path: '/report', component: () => import('pages/Report.vue') },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
