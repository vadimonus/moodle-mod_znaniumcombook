<template>
    <nav>
        <ul class="pagination justify-content-center">
            <li
                    v-for="(type, page) in pages"
                    :key="page"
                    class="page-item"
                    :class="{
                        active: type=='current',
                        disabled: disabled(type),
                    }"
            >
                <span v-if="type=='current'" class="page-link">
                    {{page}}
                </span>
                <a v-if="type=='link'" class="page-link" href="#" @click="specified(page)">
                    {{page}}
                </a>
                <span v-if="type=='dots'" class="page-link disabled">
                    ...
                </span>
                <a v-if="type=='prev'" class="page-link" href="#" @click="prev">
                    &laquo; Назад
                </a>
                <span v-if="type=='prev_disabled'" class="page-link">
                    &laquo; Назад
                </span>
                <a v-if="type=='next'" class="page-link" href="#" @click="next">
                    Вперед &raquo;
                </a>
                <span v-if="type=='next_disabled'" class="page-link">
                    Вперед &raquo;
                </span>
            </li>
        </ul>
    </nav>
</template>

<script>
    import {mapState} from 'vuex';

    export default {
        name: 'Paginator',
        computed: {
            ...mapState([
                'currentPageNum',
                'pagesTotal',
            ]),
            pages() {
                if (!this.pagesTotal || !this.currentPageNum) {
                    return [];
                }
                let pages = [];
                let min = Math.max(2, Math.min(this.pagesTotal - 1, this.currentPageNum - 3));
                let max = Math.min(this.pagesTotal - 1, Math.max(2, this.currentPageNum + 3));
                if (this.currentPageNum > 1) {
                    pages[0] = 'prev';
                } else {
                    pages[0] = 'prev_disabled';

                }
                pages[1] = 'link';
                if (min > 3) {
                    pages[2] = 'dots';
                } else if (min == 3) {
                    pages[2] = 'link';
                }
                if (min <= max) {
                    for (let i = min; i <= max; i++) {
                        pages[i] = 'link';
                    }
                }
                if (max + 1 < this.pagesTotal - 1) {
                    pages[this.pagesTotal - 1] = 'dots';
                } else if (max + 1 == this.pagesTotal - 1) {
                    pages[this.pagesTotal - 1] = 'link';
                }
                pages[this.pagesTotal] = 'link';
                if (this.currentPageNum < this.pagesTotal) {
                    pages[this.pagesTotal + 1] = 'next';
                } else {
                    pages[this.pagesTotal + 1] = 'next_disabled';

                }
                pages[this.currentPageNum] = 'current';
                return pages;
            },
        },
        methods: {
            prev() {
                this.$store.dispatch('loadPage', this.currentPageNum - 1);
            },
            next() {
                this.$store.dispatch('loadPage', this.currentPageNum + 1);
            },
            specified(number) {
                this.$store.dispatch('loadPage', number);
            },
            disabled(type) {
                switch (type) {
                    case 'dots':
                    case 'prev_disabled':
                    case 'next_disabled':
                        return true;
                }
                return false;
            },
        },
    };
</script>
