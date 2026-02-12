import { createMemoryHistory, createRouter } from "vue-router";

import { LandingPage } from "./pages/Landing";

const routes = [{
  path: '/',
  name: 'Landing page',
  component: LandingPage
}]

export const router = createRouter({
  history: createMemoryHistory(),
  routes
})
