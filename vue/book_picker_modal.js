// import Str from 'core/str';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Vue from 'vue';
import BookPickerModalBody from './book_picker_modal_body';

export default class BookPickerModal {

    constructor(parentVue) {
        this.parentVue = parentVue;
    }

    async show(onHideCallback) {
        this.modal = await ModalFactory.create({
            type: ModalFactory.types.CANCEL,
            title: 'Поиск публикации',
            body: '',
        });

        this.modal.setLarge();

        this.modal.getRoot().on(ModalEvents.hidden, function() {
            this.vue.$destroy();
            this.modal.setBody('');
            onHideCallback();
        }.bind(this));

        this.modal.getRoot().on(ModalEvents.shown, function() {
            const template = '<div id="book-picker-modal-body"><book-picker-modal-body></book-picker-modal-body></div>';
            this.modal.setBody(template);

            this.vue = new Vue({
                el: '#book-picker-modal-body',
                name: 'BookPickerModalWrapper',
                components: {
                    BookPickerModalBody,
                },
                parent: this.parentVue,
            });
        }.bind(this));

        this.modal.show();

    }

    hide() {
        this.modal.hide();
    }
}
