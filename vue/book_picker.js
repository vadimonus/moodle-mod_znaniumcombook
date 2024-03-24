// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Book from znanium.com module
 *
 * @package mod_znaniumcombook
 * @copyright 2020 Vadim Dvorovenko
 * @copyright 2020 ООО «ЗНАНИУМ»
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import {createApp} from 'vue';
import {createStore, mapState} from 'vuex';
import storeDefinition from './store';
import modal from './book_picker_modal';

export function init() {

    const bookIdSelector = '#id_book_id';
    const bookDescriptionSelector = '#id_book_description';
    const buttonSelector = '#id_book_select';

    const app = createApp({
        name: 'BookPickerInput',
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
            },
            hideModal: function () {
                if (this.modal) {
                    this.modal.hide();
                }
            },
        },
        mounted: function () {
            this.$store.dispatch('loadComponentStrings');
        },
        watch: {
            'stringsLoaded': function () {
                $(buttonSelector).removeAttr('disabled');
            },
        },
    });

    const store = createStore(storeDefinition);
    app.use(store);

    store.commit('setSelectedBook', {
        id: $(bookIdSelector).val(),
        biblio_record: $(bookDescriptionSelector).val(),
    });

    const commonParent = function (el1, el2, el3) {
        return $(el1).parents().has(el2).has(el3).first().get(0);
    };

    const appElement = commonParent(bookIdSelector, bookDescriptionSelector, buttonSelector);
    app.mount(appElement);
}