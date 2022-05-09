import Sortable from 'sortablejs';
import './common.js';
import './edittrigger.scss';

let TriggerCondition = Garnish.Base.extend({
    container: null,
    list: null,
    handle: null,
    modal: null,

    init: function (container, list, modal) {
        this.$container = container;
        this.modal = modal;
        this.list = list;
        this.handle = container.data('handle');
        this.initChangeOperator();
        this.initDelete();
        this.initEdit();
        this.initActive();
        if (this.handle == 'group') {
            $.each(this.$container.find('>.description .conditions-list .condition'), (i, item) => {
                new TriggerCondition($(item), list, modal);
            });
            list.initConditionSortable(this.$container.find('>.description .conditions-list')[0]);
        }
    },

    initActive: function () {
        this.$container.find('>.active .lightswitch').on('change', (e) => {
            if ($(e.target).find('input').val()) {
                $(e.target).closest('.condition').removeClass('inactive');
            } else {
                $(e.target).closest('.condition').addClass('inactive');
            }
        });
    },

    initChangeOperator: function () {
        this.$container.find('>.operator .js-change-operator').click((e) => {
            e.preventDefault();
            let link = $(e.target);
            let input = link.next();
            let val = input.val();
            if (val == 'and') {
                input.val('or');
                link.html(link.data('or'));
            } else {
                input.val('and');
                link.html(link.data('and'));
            }
        });
    },

    initDelete: function () {
        this.$container.find('>.js-delete').click((e) => {
            e.preventDefault();
            this.delete();
        });
    },

    initEdit: function () {
        this.$container.find('>.js-edit').click((e) => {
            e.preventDefault();
            this.edit();
        });
    },

    delete: function () {
        this.$container.remove();
        this.destroy();
    },

    edit: function () {
        let form = $('<form>');
        form.html(this.$container.find('>.condition-wrapper .condition-config'));
        this.modal.$container.find('.content').html(form);
        this.modal.$container.find('.js-save').off('click');
        this.modal.$container.find('.js-save').on('click', () => {
            this.validate();
        });
        this.modal.show();
    },

    validate: function () {
        let form = this.modal.$container.find('form');
        form.find('ul.errors').remove();
        let conditionData = form.serializeJSON().conditions ?? {};
        let data = {
            action: 'triggers/cp-triggers/validate-condition',
            handle: this.handle,
            namespace: this.list.buildNamespace(this.$container)
        };
        let matches = [...data.namespace.matchAll(/\[([^\[\]]+)\]/g)];
        for (let i in matches) {
            conditionData = conditionData[matches[i][1]] ?? {};
        }
        data.condition = conditionData;
        $.ajax({
            url: '/',
            data: data,
            method: 'post'
        }).fail((data) => {
            Craft.Triggers.handleError(data);
        }).done((data) => {
            form.find('.condition-config').html(data.html);
            Craft.appendHeadHtml(data.headHtml);
            Craft.appendFootHtml(data.footHtml);
            Craft.initUiElements(form.find('.condition-config'));
            this.$container.find('>.description').html(data.description);
            if (!data.errors) {
                this.$container.find('>.icon.settings.error').removeClass('error');
                this.$container.find('>.condition-wrapper').html(form.find('.condition-config'));
                this.modal.hide();
            }
        });
    }
});

let TriggerAction = Garnish.Base.extend({
    container: null,
    handle: null,
    modal: null,
    namespace: null,

    init: function (container, list, modal) {
        this.$container = container;
        this.handle = container.data('handle');
        this.modal = modal;
        this.namespace = container.data('namespace');
        this.initDelete();
        this.initEdit();
        this.initActive();
    },

    initActive: function () {
        this.$container.find('.active .lightswitch').on('change', (e) => {
            if ($(e.target).find('input').val()) {
                $(e.target).closest('.action').removeClass('inactive');
            } else {
                $(e.target).closest('.action').addClass('inactive');
            }
        });
    },

    initDelete: function () {
        this.$container.find('.js-delete').click((e) => {
            e.preventDefault();
            this.delete();
        });
    },

    initEdit: function () {
        this.$container.find('.js-edit').click((e) => {
            e.preventDefault();
            this.edit();
        });
    },

    delete: function () {
        this.$container.remove();
        this.destroy();
    },

    edit: function () {
        let form = $('<form>');
        form.html(this.$container.find('.action-config'));
        this.modal.$container.find('.content').html(form);
        this.modal.$container.find('.js-save').off('click');
        this.modal.$container.find('.js-save').on('click', () => {
            this.validate();
        });
        this.modal.show();
    },

    validate: function () {
        let form = this.modal.$container.find('form');
        form.find('ul.errors').remove();
        let actionData = form.serializeJSON().actions;
        let data = {
            action: 'triggers/cp-triggers/validate-action',
            handle: this.handle,
            namespace: this.$container.attr('data-namespace')
        };
        let match = data.namespace.match(/\[([^\[\]]+)\]/);
        data.actionData = actionData[match[1]];
        $.ajax({
            url: '/',
            data: data,
            method: 'post'
        }).fail((data) => {
            Craft.Triggers.handleError(data);
        }).done((data) => {
            form.find('.action-config').html(data.html);
            Craft.appendHeadHtml(data.headHtml);
            Craft.appendFootHtml(data.footHtml);
            Craft.initUiElements(form.find('.action-config'));
            this.$container.find('.description').html(data.description);
            if (!data.errors) {
                this.$container.find('>.icon.settings.error').removeClass('error');
                this.$container.find('.config-wrapper').html(form.find('.action-config'));
                this.modal.hide();
            }
        });
    }
});

Craft.Triggers.EditTrigger = Garnish.Base.extend({
    $conditionList: null,
    $actionList: null,
    configModal: null,

    init: function () {
        this.$conditionList = $('#conditions-list');
        this.$actionList = $('#actions-list');
        this.initActionModal();
        this.initChangeTrigger();
        this.initAddCondition();
        this.initAddAction();
        this.initConditionSortable(this.$conditionList[0]);
        this.initActionSortable(this.$actionList[0]);
        this.filterConditions();
        $.each(this.$conditionList.find('>.condition'), (i, item) => {
            new TriggerCondition($(item), this, this.configModal);
        });
        $.each(this.$actionList.find('>.action'), (i, item) => {
            new TriggerAction($(item), this, this.configModal);
        });
        this.rebuildNamespaces(this.$conditionList);
    },

    initActionModal: function () {
        this.configModal = new Garnish.Modal($('#config-modal'), {
            autoShow: false,
            hideOnEsc: false,
            hideOnShadeClick: false,
        });
    },

    initConditionSortable: function (elem) {
        new Sortable(elem, {
            group: {
                name: 'conditions',
                put: true,
                pull: true
            },
            handle: ".move",
            draggable: '.condition',
            onChange: this.onConditionSortableChange.bind(this),
            invertSwap: true,
            direction: 'vertical',
            fallbackOnBody: true
        });
    },

    initActionSortable: function (elem) {
        new Sortable(elem, {
            group: 'actions',
            handle: ".move",
            draggable: '.action',
            onChange: this.onActionSortableChange.bind(this),
            direction: 'vertical'
        });
    },

    initChangeTrigger: function () {
        $('select[name=handle]').change((e) => {
            this.loadTriggerConfig();
            this.toggleConditionsActions();
            this.filterConditions();
        });
    },

    initAddCondition: function () {
        $(document).on('click', '.js-add-condition', (e) => {
            e.preventDefault();
            let handle = $(e.target).data('handle');
            $.ajax({
                url: '/',
                data: {
                    action: 'triggers/cp-triggers/new-condition',
                    handle: handle
                },
                method: 'post'
            }).fail((data) => {
                Craft.Triggers.handleError(data);
            }).done((data) => {
                let html = $(data.html);
                this.$conditionList.append(html);
                Craft.appendHeadHtml(data.headHtml);
                Craft.appendFootHtml(data.footHtml);
                Craft.initUiElements(html);
                new TriggerCondition(html, this, this.configModal);
                this.rebuildOrders(this.$conditionList);
                this.rebuildNamespaces(this.$conditionList);
            });
        });
    },

    initAddAction: function () {
        $(document).on('click', '.js-add-action', (e) => {
            e.preventDefault();
            let handle = $(e.target).data('handle');
            $.ajax({
                url: '/',
                data: {
                    action: 'triggers/cp-triggers/new-action',
                    handle: handle
                },
                method: 'post'
            }).fail((data) => {
                Craft.Triggers.handleError(data);
            }).done((data) => {
                let html = $(data.html);
                this.$actionList.append(html);
                Craft.appendHeadHtml(data.headHtml);
                Craft.appendFootHtml(data.footHtml);
                Craft.initUiElements(html);
                new TriggerAction(html, this, this.configModal);
            });
        });
    },

    toggleConditionsActions: function () {
        let show = $('select[name=handle]').val();
        if (show) {
            $('.field.conditions-wrapper').show();
            $('.field.actions-wrapper').show();
        } else {
            $('.field.conditions-wrapper').hide();
            $('.field.actions-wrapper').hide();
        }
    },

    onConditionSortableChange: function (e) {
        this.rebuildOrders($(e.from));
        this.rebuildNamespaces($(e.from));
        if ($(e.from).data('namespace') != $(e.to).data('namespace')) {
            this.rebuildOrders($(e.to));
            this.rebuildNamespaces($(e.to));
        }
    },

    onActionSortableChange: function (e) {
        $.each(this.$actionList.find('>.action'), (i, item) => {
            $(item).find('>input.order').val(i);
        });
    },

    rebuildOrders: function (elem) {
        $.each(elem.find('>.condition'), (i, item) => {
            $(item).find('>input.order').val(i);
        });
    },

    rebuildNamespaces: function (elem) {
        let newName, name, namespace, match, isArray, attr;
        $.each(elem.find('>.condition'), (i, condition) => {
            namespace = this.buildNamespace($(condition));
            $.each($(condition).find('>input, >select, >textarea, >.active input, >.operator input, >.condition-wrapper input, >.condition-wrapper select, >.condition-wrapper textarea'), (i, item) => {
                attr = $(item).attr('name');
                match = [...attr.matchAll(/(\[[^\[]*\])/g)];
                if (!match.length) {
                    name = '[' + attr + ']';
                } else if (match[match.length - 1][0] == '[]') {
                    if (match.length > 1) {
                        name = match[match.length - 2][0] + '[]';
                    } else {
                        name = '[' + attr.substring(0, attr.length - 2) + '][]';
                    }
                } else {
                    name = match[match.length - 1][0];
                }
                newName = namespace + name;
                $(item).attr('name', newName);
            });
            this.rebuildNamespaces($(condition).find('>.description >.conditions-list'));
        });
    },

    buildNamespace: function (elem, namespace = '') {
        let newNamespace = elem.data('namespace');
        if (newNamespace) {
            if (Array.isArray(newNamespace)) {
                newNamespace = '[' + newNamespace.join('') + ']';
            }
            namespace = newNamespace + namespace;
        }
        if (elem.attr('id') == 'conditions-list') {
            return namespace;
        }
        let parent = elem.parent();
        return this.buildNamespace(parent, namespace);
    },

    loadTriggerConfig: function () {
        let handle = $('select[name=handle]').val();
        let instructionsElem = $('#trigger-select-instructions');
        let tipElem = $('#trigger-select-tip');
        if (!handle) {
            tipElem.remove();
            instructionsElem.remove();
            $('#trigger-config').remove();
            return;
        }
        $.ajax({
            url: '/',
            data: {
                action: 'triggers/cp-triggers/trigger-config',
                handle: handle
            },
            method: 'post'
        }).fail((data) => {
            Craft.Triggers.handleError(data);
        }).done((data) => {
            $('#trigger-config').remove();
            if (data.html) {
                $('#trigger-select-field').after(data.html);
                Craft.initUiElements();
                Craft.appendHeadHtml(data.headHtml);
                Craft.appendFootHtml(data.footHtml);
            }
            if (data.tip) {
                if (!tipElem.length) {
                    tipElem = $('<p id="trigger-select-tip" class="notice has-icon"><span class="icon" aria-hidden="true"></span><span></span></p>');
                    $('#trigger-select-field').append(tipElem);
                }
                tipElem.find('span').last().html(data.tip);
            } else if (tipElem.length) {
                tipElem.remove()
            }
            if (data.instructions) {
                if (!instructionsElem.length) {
                    instructionsElem = $('<div id="trigger-select-instructions" class="instructions"><p></p></div>');
                    instructionsElem.insertAfter($('#trigger-select-field').find('.heading'))
                }
                instructionsElem.find('p').html(data.instructions);
            } else if (instructionsElem.length) {
                instructionsElem.remove()
            }
        });
    },

    filterConditions: function () {
        let trigger = $('select[name=handle]').val();
        let items = $('#conditions-menubtn').data('menubtn').menu.$container.find('a');
        $.each(items, (i, item) => {
            let triggers;
            let isGlobal = $(item).data('global');
            let show = false;
            if (isGlobal) {
                show = true;
            } else if (trigger) {
                triggers = $(item).data('triggers').split(',');
                show = triggers.includes(trigger);
            }
            if (show) {
                $(item).parent().show();
            } else {
                $(item).parent().hide();
            }
        });
        items = $('#conditions-list').find('.condition');
        $.each(items, (i, item) => {
            let triggers;
            let isGlobal = $(item).data('global');
            if (!isGlobal && trigger) {
                triggers = $(item).data('triggers').split(',');
                if (!triggers.includes(trigger)) {
                    $(item).remove();
                }
            }
        });
    }
});