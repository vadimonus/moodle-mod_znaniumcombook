import $ from 'jquery';
import Vue from 'vue';
import store from './store';
import {mapState} from 'vuex';
import modal from './book_picker_modal';

export function init() {

    const bookIdSelector = '#id_book_id';
    const bookDescriptionSelector = '#id_book_description';
    const buttonSelector = '#id_book_select';

    let appElement = $(bookIdSelector).parent().get(0);

    new Vue({
        el: appElement,
        name: 'BookPickerInput',
        data: {},
        computed: {
            ...mapState([
                'strings',
                'selectedBook',
            ]),
            stringsLoaded: function () {
                return Object.keys(this.strings).length > 0;
            },
        },
        methods: {
            showModal: function () {
                $(buttonSelector).attr('disabled', true);
                this.modal = new modal(this);
                this.modal.show(function () {
                    $(buttonSelector).removeAttr('disabled');
                });
                this.modal;
            },
            hideModal: function () {
                if (this.modal) {
                    this.modal.hide();
                }
            },
        },
        beforeMount: function() {
            let id = $(bookIdSelector).val();
            let biblio_record = $(bookDescriptionSelector).val();
            this.$store.commit('setSelectedBook', {
                id,
                biblio_record
            });
        },
        mounted: function () {
            store.dispatch('loadComponentStrings');
        },
        watch: {
            'stringsLoaded': function () {
                $(buttonSelector).removeAttr('disabled');
            },
        },
        store,
    });
}