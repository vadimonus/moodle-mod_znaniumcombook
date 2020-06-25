<template>
    <form v-on:submit.prevent="search">
        <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="queryString" @input="onInput"/>
            <button class="btn btn-secondary" type="submit">
                Поиск
            </button>
        </div>
    </form>
</template>

<script>
    export default {
        name: "SearchForm",
        data: () => ({
            queryString: '',
        }),
        methods: {
            onInput: function (event) {
                this.$emit('input', event.target.value);
            },
            search: function () {
                this.$store.dispatch('search', this.queryString);
            },
        },
    }
</script>
