<template>
    <div>
        <search-form></search-form>
        <div v-if="searchError" class="text-center text-danger">
            Произошла ошибка
        </div>
        <div v-else-if="searching" class="text-center">
            ... идет поиск ...
        </div>
        <div v-else-if="pagesTotal > 0">
            <paginator></paginator>
            <books-list></books-list>
            <paginator></paginator>
        </div>
        <div v-else-if="queryString.length > 0" class="text-center">
            По вашему запросу ничего не найдено
        </div>
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
                'searching',
                'currentPageNum',
                'pagesTotal',
                'queryString',
                'searchError',
            ]),
        },
    };
</script>
