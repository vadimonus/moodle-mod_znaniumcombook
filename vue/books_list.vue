<template>
    <div class="books-list">
        <book-list-item v-for="publication in currentPage" :key="publication.id" :publication="publication"></book-list-item>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';
    import BookListItem from './books_list_item';

    export default {
        name: "BooksList",
        components: {
            BookListItem,
        },
        computed: {
            ...mapGetters([
                'currentPage',
            ]),
        },
    }
</script>
