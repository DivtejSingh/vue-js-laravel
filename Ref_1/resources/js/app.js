/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import CKEditor from '@ckeditor/ckeditor5-vue2';

window.Vue = require('vue').default;
import vuetify from "./vuetify";
import store from "./store";

Vue.use(BootstrapVue);
Vue.use(IconsPlugin)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('menu-bar',require('./components/MenuBar.vue').default);
Vue.component('home-page', require('./pages/HomePage.vue').default);
Vue.component('main-footer', require('./pages/MainFooter.vue').default);
Vue.component('contact-view', require('./pages/ContactView.vue').default);
Vue.component('user-login',require('./user/UserLogin.vue').default);
Vue.component('user-register',require('./user/UserRegister.vue').default);
Vue.component('business-login',require('./business/BusinessLogin.vue').default);
Vue.component('business-register',require('./business/BusinessRegister.vue').default);
Vue.component('reseller-login',require('./reseller/ResellerLogin.vue').default);
Vue.component('reseller-register',require('./reseller/ResellerRegister.vue').default);
Vue.component('search-page', require('./pages/SearchPage.vue').default);
Vue.component('dealdetail-page', require('./pages/DealdetailPage.vue').default);
Vue.component('saledetail-page', require('./pages/SaledetailPage.vue').default);
Vue.component('jobdetail-page', require('./pages/JobdetailPage.vue').default);
Vue.component('videodetail-page', require('./pages/VideodetailPage.vue').default);
Vue.component('business-view', require('./pages/BusinessView.vue').default);
Vue.component('all-categories', require('./pages/AllCategories.vue').default);
Vue.component('cat-search', require('./pages/CatSearch.vue').default);
Vue.component('jobcat-page', require('./pages/JobcatPage.vue').default);
Vue.component('allcityjobs-page', require('./pages/AllCityjobs.vue').default);

Vue.component('user-logout', require('./user/UserLogout.vue').default);
Vue.component('user-profile',require('./user/UserProfile.vue').default);
Vue.component('user-wishlist', require('./user/UserWishlist.vue').default);
Vue.component('user-reviews', require('./user/UserReviews.vue').default);
Vue.component('menu-login', require('./components/MenuLogin.vue').default);

//business dashboard components
Vue.component('business-profile', require('./business/BusinessProfile.vue').default);
Vue.component('business-wishlist', require('./business/BusinessWishlist.vue').default);
Vue.component('business-reviews', require('./business/BusinessReviews.vue').default);
Vue.component('business-cover', require('./business/BusinessCover.vue').default);
Vue.component('business-gallery', require('./business/BusinessGallery.vue').default);
Vue.component('reviewbybusiness', require('./business/ReviewByBusiness.vue').default);
Vue.component('business-billing', require('./business/BusinessBilling.vue').default);
Vue.component('business-plans', require('./components/BusinessPlans.vue').default);
Vue.component('business-hotdeals', require('./business/BusinessHotdeals.vue').default);

//reseller dashboard components
Vue.component('business-list', require('./reseller/BusinessList.vue').default);
Vue.component('reseller-wishlist', require('./reseller/ResellerWishlist.vue').default);
Vue.component('reseller-reviews', require('./reseller/ResellerReviews.vue').default);
Vue.component('reseller-payment', require('./reseller/ResellerPayment.vue').default);
Vue.component('reseller-profile', require('./reseller/ResellerProfile.vue').default);

Vue.component('rbusiness-profile', require('./reseller/RbusinessProfile.vue').default);
Vue.component('rbusiness-cover', require('./reseller/RbusinessCover.vue').default);
Vue.component('rbusiness-gallery', require('./reseller/RbusinessGallery.vue').default);
Vue.component('rbusiness-hotdeals', require('./reseller/RbusinessHotdeals.vue').default);
Vue.component('rsales-view', require('./reseller/RsalesView.vue').default);
Vue.component('rjobs-view', require('./reseller/RjobsView.vue').default);
Vue.component('rvideos-view', require('./reseller/RvideosView.vue').default);
Vue.component('rbusiness-reviews', require('./reseller/RbusinessReviews.vue').default);
Vue.component('edit-business', require('./reseller/EditBusiness.vue').default);
Vue.component('admin-reseller-edit', require('./admin/AdminResellerEdit.vue').default);
Vue.component('admin-reseller-businesslist', require('./admin/AdminResellerBusinessList.vue').default);
Vue.component('admin-reseller-reviews', require('./admin/AdminResellerReviews.vue').default);
Vue.component('admin-reseller-wishlist-edit', require('./admin/AdminResellerWishlistEdit.vue').default);
Vue.component('admin-reseller-payment', require('./admin/AdminResellerPayment.vue').default);


//admin dashboard components
Vue.component('admin-dashboard', require('./admin/AdminDashboard.vue').default);
Vue.component('public-users', require('./admin/PublicUsers.vue').default);
Vue.component('admin-subadmins', require('./admin/AdminSubadmins.vue').default);
Vue.component('admin-resellers', require('./admin/AdminResellers.vue').default);
Vue.component('admin-businesslist', require('./admin/AdminBusinesslist.vue').default);
Vue.component('admin-businesslist-edit', require('./admin/AdminBusinesslistEdit.vue').default);
Vue.component('admin-business-cover-edit', require('./admin/AdminBusinessCoverEdit.vue').default);
Vue.component('admin-business-gallery-edit', require('./admin/AdminBusinessGalleryEdit.vue').default);
Vue.component('admin-business-wishlist', require('./admin/AdminBusinessWishlist.vue').default);
Vue.component('admin-business-reviews', require('./admin/AdminBusinessReviews.vue').default);
Vue.component('admin-reviewbybusiness', require('./admin/AdminReviewByBusiness.vue').default);


Vue.component('admin-businessdeals', require('./admin/AdminBusinessdeals.vue').default);
Vue.component('admin-mcats', require('./admin/AdminMaincats.vue').default);
Vue.component('admin-subcats', require('./admin/AdminSubcats.vue').default);
Vue.component('admin-ads', require('./admin/AdminAds.vue').default);
Vue.component('admin-categoryslider', require('./admin/AdminCategoryslider.vue').default);
Vue.component('admin-citydeal', require('./admin/AdminCitydeal.vue').default);
Vue.component('admin-country', require('./admin/AdminCountry.vue').default);
Vue.component('admin-state', require('./admin/AdminState.vue').default);
Vue.component('admin-city', require('./admin/AdminCity.vue').default);
Vue.component('admin-jobcats',require('./admin/AdminJobcats.vue').default);
Vue.component('admin-jobtypes',require('./admin/AdminJobtypes.vue').default);
Vue.component('admin-jobqualifications',require('./admin/AdminJobqualifications.vue').default);
Vue.component('admin-reviews',require('./admin/AdminReviews.vue').default);
Vue.component('admin-dealreports',require('./admin/AdminDealreports.vue').default);
Vue.component('admin-profile',require('./admin/AdminProfile.vue').default);
Vue.component('admin-plans',require('./admin/AdminPlans.vue').default);
Vue.component('admin-billing',require('./admin/AdminBilling.vue').default);
Vue.component('admin-registrationimage',require('./admin/AdminRegistrationimage.vue').default);
Vue.component('admin-contactphone',require('./admin/AdminContactphone.vue').default);
Vue.component('admin-pages',require('./admin/AdminPages.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    vuetify,
    store
});

window.addEventListener('beforeunload', () => {
    store.commit('SET_USER_TOKEN', store.state.userToken);
    store.commit('SET_USER_DATA', store.state.userData);
    store.commit('SET_USER_SEARCH', store.state.userSearch);
});
