<template>
    <form v-on:submit.prevent="search">
        <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="queryString" @input="onInput"/>
            <button class="btn btn-secondary" type="submit" v-text="strings.search_btn"></button>
        </div>
    </form>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: 'SearchForm',
        data: () => ({
            queryString: '',
        }),
        computed: {
            ...mapState([
                'strings',
            ]),
        },
        methods: {
            onInput: function (event) {
                this.$emit('input', event.target.value);
            },
            search: function () {
                this.$store.dispatch('search', this.queryString);
            },
        },
    };
</script>
