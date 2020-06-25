<template>
    <div class="row align-items-center my-3">
        <div class="col-auto">
            <img :src="publication.cover" alt="Обложка" class="border border-dark"/>
        </div>
        <div class="col">
            {{ publication.biblio_record }}
        </div>
        <div class="col-auto">
            <button
                    class="btn btn-primary"
                    @click="selectBook(publication.id)"
            >
                Выбрать
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "BooksListItem",
        props: {
            publication: {
                type: Object,
                default: () => ({}),
            }
        },
        methods: {
            selectBook() {
                this.$store.commit('setSelectedBook', this.publication);
                this.$root.hideModal();
            },
        },
    }
</script>
