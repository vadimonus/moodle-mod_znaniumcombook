import $ from 'jquery';
import Vue from 'vue';
import Vuex from 'vuex';
import moodleAjax from 'core/ajax';
import moodleStorage from 'core/localstorage';
import Notification from 'core/notification';

Vue.use(Vuex);

/**
 * Single ajax call to Moodle.
 */
async function ajax(method, args) {
    const request = {
        methodname: method,
        args: args,
    };

    try {
        return await moodleAjax.call([request])[0];
    } catch (e) {
        Notification.exception(e);
        throw e;
    }
}

export default new Vuex.Store({
    strict: process.env.NODE_ENV !== 'production',
    state: {
        strings: {},
        selectedBook: {
            id: null,
            description: '',
        },
        searching: false,
        queryString: false,
        searchError: false,
        pages: [],
        currentPageNum: null,
        pagesTotal: null,
    },
    getters: {
        currentPage: state => {
            if (state.pages[state.currentPageNum] !== undefined) {
                return state.pages[state.currentPageNum];
            }
            return [];
        },
    },
    mutations: {
        setStrings(state, strings) {
            state.strings = strings;
        },
        setSelectedBook(state, publication) {
            state.selectedBook.id = publication.id;
            state.selectedBook.description = publication.biblio_record;
        },
        setSearching(state, value) {
            state.searching = value;
        },
        setQueryString(state, value) {
            state.queryString = value;
        },
        setSearchError(state, value) {
            state.searchError = value;
        },
        unsetPages(state) {
            state.pages = [];
            state.pagesTotal = null;
            state.pagesLoaded = null;
        },
        setPage(state, payload) {
            Vue.set(state.pages, payload.page, payload.publications);
        },
        setCurrentPage(state, value) {
            state.currentPageNum = value;
        },
        setPagesTotal(state, value) {
            state.pagesTotal = value;
        },
    },
    actions: {
        async loadComponentStrings(context) {
            const lang = $('html').attr('lang').replace(/-/g, '_');
            const cacheKey = 'mod_znaniumcombook/strings/' + lang;
            const cachedStrings = moodleStorage.get(cacheKey);
            if (cachedStrings) {
                context.commit('setStrings', JSON.parse(cachedStrings));
            } else {
                const request = {
                    methodname: 'core_get_component_strings',
                    args: {
                        'component': 'mod_znaniumcombook',
                        lang,
                    },
                };
                const loadedStrings = await moodleAjax.call([request])[0];
                let strings = {};
                loadedStrings.forEach((s) => {
                    strings[s.stringid] = s.string;
                });
                context.commit('setStrings', strings);
                moodleStorage.set(cacheKey, JSON.stringify(strings));
            }
        },
        async search(context, queryString) {
            if (queryString == context.state.queryString) {
                return;
            }
            context.commit('unsetPages');
            context.commit('setQueryString', queryString);
            context.commit('setSearching', true);
            context.commit('setSearchError', false);
            try {
                const results = await ajax('mod_znaniumcombook_search', {
                    searchquery: queryString,
                });

                let currentPage = results._meta.currentPage;
                context.commit('setPage', {
                    page: currentPage,
                    publications: results.publications,
                });
                context.commit('setCurrentPage', currentPage);
                context.commit('setPagesTotal', results._meta.pageCount);
                context.commit('setSearching', false);
                context.dispatch('preloadPage', currentPage + 1);
            } catch (e) {
                context.commit('setSearching', false);
                context.commit('setSearchError', true);
            }
        },
        async loadPage(context, page) {
            if (page > context.state.pagesTotal) {
                page = context.state.pagesTotal;
            }
            if (page < 1) {
                page = 1;
            }
            if (context.state.pages[page] !== undefined) {
                context.commit('setCurrentPage', page);
                context.dispatch('preloadPage', page - 1);
                context.dispatch('preloadPage', page + 1);
                return;
            }
            context.commit('setSearching', true);
            context.commit('setSearchError', false);
            try {
                const results = await ajax('mod_znaniumcombook_search', {
                    searchquery: context.state.queryString,
                    page: page,
                });

                let currentPage = results._meta.currentPage;
                context.commit('setPage', {
                    page: currentPage,
                    publications: results.publications,
                });
                context.commit('setCurrentPage', currentPage);
                context.commit('setPagesTotal', results._meta.pageCount);
                context.commit('setSearching', false);
                context.dispatch('preloadPage', currentPage - 1);
                context.dispatch('preloadPage', currentPage + 1);
            } catch (e) {
                context.commit('setSearching', false);
                context.commit('setSearchError', true);
            }
        },
        async preloadPage(context, page) {
            if (page > context.state.pagesTotal) {
                page = context.state.pagesTotal;
            }
            if (page < 1) {
                page = 1;
            }
            if (context.state.pages[page] !== undefined) {
                return;
            }
            try {
                const results = await ajax('mod_znaniumcombook_search', {
                    searchquery: context.state.queryString,
                    page: page,
                });
                page = results._meta.currentPage;
                context.commit('setPage', {
                    page: page,
                    publications: results.publications,
                });
            } catch (e) {

            }
        },
    },
});
