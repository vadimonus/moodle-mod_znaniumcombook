<template>
    <div>
        <search-form></search-form>
        <div v-if="searchError" class="text-center text-danger" v-text="strings.search_error"></div>
        <div v-else-if="searching" class="text-center"  v-text="strings.searching"></div>
        <div v-else-if="pagesTotal > 0">
            <paginator></paginator>
            <books-list></books-list>
            <paginator></paginator>
        </div>
        <div v-else-if="queryString.length > 0" class="text-center" v-text="strings.nothing_found"></div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';
    import SearchForm from './search_form';
    import BooksList from './books_list';
    import Paginator from './paginator';

    export default {
        name: 'BookPickerModal',
        components: {
            Paginator,
            BooksList,
            SearchForm,
        },
        computed: {
            ...mapState([
                'strings',
                'searching',
                'currentPageNum',
                'pagesTotal',
                'queryString',
                'searchError',
            ]),
        },
    };
</script>
