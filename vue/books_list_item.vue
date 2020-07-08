<template>
    <div class="row align-items-center my-3">
        <div class="col-auto">
            <img :src="publication.cover" :alt="strings.cover_alt" class="border border-dark"/>
        </div>
        <div class="col" v-text="publication.biblio_record"></div>
        <div class="col-auto">
            <button
                    class="btn btn-primary"
                    @click="selectBook(publication.id)"
                    v-text="strings.select_book_btn"
            ></button>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: "BooksListItem",
        props: {
            publication: {
                type: Object,
                default: () => ({}),
            }
        },
        computed: {
            ...mapState([
                'strings',
            ]),
        },
        methods: {
            selectBook() {
                this.$store.commit('setSelectedBook', this.publication);
                this.$root.hideModal();
            },
        },
    }
</script>
