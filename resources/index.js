import Vue from 'vue';
import MovieSeach from '@c/movieSearch.vue';

window.app = new Vue({
    el: document.getElementById('app'),
    components: {
    	'movie-search': MovieSeach,
    },
});