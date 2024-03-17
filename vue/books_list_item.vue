<!--
This file is part of Moodle - http://moodle.org/

Moodle is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Moodle is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
-->

<template>
    <div class="row align-items-center my-3">
        <div class="col-auto">
            <img :src="publication.cover" :alt="strings.cover_alt" class="border border-dark"/>
        </div>
        <div class="col" v-text="publication.biblio_record"></div>
        <div class="col-auto">
            <button
                    class="btn btn-primary"
                    @click="selectBook()"
                    v-text="strings.select_book_btn"
            ></button>
        </div>
    </div>
</template>

<script>
    /**
     * Book from znanium.com module
     *
     * @package mod_znaniumcombook
     * @copyright 2020 Vadim Dvorovenko
     * @copyright 2020 ООО «ЗНАНИУМ»
     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */

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
                this.$emit('book-selected');
            },
        },
    }
</script>
