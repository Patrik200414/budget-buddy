import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue';
import RegistrationView from '../views/RegistrationView.vue';
import EmailVerifyView from '../views/EmailVerifyView.vue';
import PasswordForgotRequestView from '../views/PasswordForgotRequestView.vue';
import ForgetPasswordNewPasswordView from '../views/ForgetPasswordNewPasswordView.vue';
import Navbar from '@/components/Navbar.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: Navbar,
      children: [
        {
          path: '',
          component: HomeView
        }
      ]
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },
    {
      path: '/registration',
      name: 'registration',
      component: RegistrationView
    },
    {
      path: '/email/verify/:userId',
      name: 'emailVerify',
      component: EmailVerifyView
    },
    {
      path: '/password/forget/request',
      name: 'passwordForgetRequest',
      component: PasswordForgotRequestView
    },
    {
      path: '/password/new/:passwordResetVerifyToken',
      name: 'passwordForgetTokenVerify',
      component: ForgetPasswordNewPasswordView
    }
  ]
});

export default router
