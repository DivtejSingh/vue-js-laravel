import Vue from 'vue'
import Vuex from 'vuex'
import axios from "axios";

Vue.use(Vuex)
const store = new Vuex.Store({
    state:{
        userToken: '', // User token
        auserData: null, // User data
        userData: '', // User data
        userId: '', // User data
        isAuthenticated: false,
        userSearch:'',
        listingradius: 20,
        country_id:1,
        city_id:'',
        subcat_id:'',
        jobcat_id:'',
        dealtype:3,
        searchy:'',
    },
    mutations:{
        SET_AUSER_DATA(state,auserData){
            state.auserData = auserData;
            state.isAuthenticated = true;
        },
        SET_USER_TOKEN(state, token) {
            state.userToken = token;
            localStorage.setItem('userToken', token);
            state.isAuthenticated = true;
        },
        SET_USER_DATA(state, userData) {
            state.userData = userData;
            localStorage.setItem('userData', JSON.stringify(userData));
        },
        SET_USER_ID(state, userId) {
            state.userId = userId;
            localStorage.setItem('userId', JSON.stringify(userId));
        },
        SET_CITY_ID(state, city_id) {
            state.city_id = city_id;
            localStorage.setItem('city_id', JSON.stringify(city_id));
        },
        SET_SUBCAT_ID(state, subcat_id) {
            state.subcat_id = subcat_id;
            localStorage.setItem('subcat_id', JSON.stringify(subcat_id));
        },
        SET_JOBCAT_ID(state, jobcat_id) {
            state.jobcat_id = jobcat_id;
            localStorage.setItem('jobcat_id', JSON.stringify(jobcat_id));
        },
        SET_DEALTYPE_ID(state, dealtype) {
            state.dealtype = dealtype;
            localStorage.setItem('dealtype', JSON.stringify(dealtype));
        },
        SET_SEARCHY(state,searchy) {
            state.searchy = searchy;
            localStorage.setItem('searchy',JSON.stringify(searchy));
        },
        SET_USER_SEARCH(state, userSearch) {
            state.userSearch = userSearch;
            localStorage.setItem('userSearch',JSON.stringify(userSearch))
        },
        CLEAR_USER(state) {
            state.userToken = '';
            state.userData = '';
            localStorage.clear('userToken'); // Remove userToken from local storage
            localStorage.clear('userData'); // Remove userData from local storage
        },
        CLEAR_ID(state) {
            state.userId = '';
            localStorage.clear('userId'); // Remove userToken from local storage
        },
    },
    actions:{
        setlogin({commit},{auserData}){
            commit('SET_AUSER_DATA',auserData);
        },
        login({ commit }, { token, userData }) {
            commit('SET_USER_TOKEN', token);
            commit('SET_USER_DATA', userData);

        },
        setuserid({ commit }, { userId }){
            commit('SET_USER_ID', userId);
        },
        setcityid({commit},{city_id}){
            commit('SET_CITY_ID', city_id);
        },
        setsubcatid({commit},{subcat_id}){
            commit('SET_SUBCAT_ID', subcat_id);
        },
        setjobcatid({commit},{jobcat_id}){
            commit('SET_JOBCAT_ID', jobcat_id);
        },
        setdealtypeid({commit},{dealtype}){
            commit('SET_DEALTYPE_ID', dealtype);
        },
        setsearchy({commit},{searchy}){
            commit('SET_SEARCHY', searchy)
        },
        usersearch({commit}, {userSearch}){
            commit('SET_USER_SEARCH',userSearch);
        },
        logout({ commit }) {
            commit('CLEAR_USER');
        },
        removeid({ commit }) {
            commit('CLEAR_ID');
        },
    },
    getters:{}
})
const userToken = localStorage.getItem('userToken');
if (userToken) {
    store.commit('SET_USER_TOKEN', userToken);
}
const userSearch = JSON.parse(localStorage.getItem('userSearch'));
if(userSearch) {
    store.commit('SET_USER_SEARCH',userSearch);
}

let userHeader = document.head.querySelector('meta[name="user"]');
window.user = null
if (userHeader) {
    if (userHeader.content) {
        window.user = JSON.parse(userHeader.content);
    }
}

if(window.user){
    store.commit('SET_AUSER_DATA',window.user);
    store.commit('SET_USER_DATA',window.user);
}
// … for subcat_id
const rawSub = localStorage.getItem('subcat_id');
let subcat_id = null;
try {
  subcat_id = rawSub && rawSub !== 'undefined'
    ? JSON.parse(rawSub)
    : null;
} catch (e) {
  console.warn('bad subcat_id in localStorage:', rawSub);
  subcat_id = null;
}
if (subcat_id !== null) {
  store.commit('SET_SUBCAT_ID', subcat_id);
}


const city_id = JSON.parse(localStorage.getItem('city_id'));
if(city_id){
    store.commit('SET_CITY_ID',city_id);
}
const jobcat_id = JSON.parse(localStorage.getItem('jobcat_id'));
if(jobcat_id){
    store.commit('SET_JOBCAT_ID',jobcat_id);
}
const dealtype = JSON.parse(localStorage.getItem('dealtype'));
if(dealtype){
    store.commit('SET_DEALTYPE_ID',dealtype);
}
const setAuthHeader = () => {
    const token = localStorage.getItem('userToken');
    if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
};

// Call the function to set the header
setAuthHeader();

export default store;
