import $ from 'jquery';
import Vue from 'vue';
import store from './store';
import {mapState} from 'vuex';
import modal from './book_picker_modal';

export function init(appSelector, buttonSelector, nameSelector, descriptionSelector) {

    new Vue({
        el: appSelector,
        name: 'BookPickerInput',
        data: {},
        computed: {
            ...mapState([
                'selectedBook',
            ]),
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
        mounted: function () {
            $(buttonSelector).removeAttr('disabled');
        },
        watch: {
            'selectedBook.id': function () {
                $(nameSelector).val(this.selectedBook.description);
            },
        },
        store,
    });
}