import './common.js';

Craft.Triggers.Triggers = Garnish.Base.extend({
    init: function () {
        this.initDelete();
    },

    initDelete: function () {
        $('.tableview .delete').click(function(){
            let line = $(this).parent().parent();
            if (confirm(Craft.t('triggers', 'Are you sure you want to delete this trigger ?'))) {
                $.ajax({
                    url: Craft.getActionUrl('triggers/cp-triggers/delete'),
                    data: {id: $(this).data('id')},
                    dataType: 'json'
                }).fail(function (data) {
                    Craft.Triggers.handleError(data);
                }).done(function (data) {
                    if (data.message) {
                        Craft.cp.displayNotice(data.message);
                    }
                    line.remove();
                })
            }
        });
    }
});