(()=>{"use strict";Craft.Triggers={handleError:function(r){r.hasOwnProperty("responseJSON")?r.responseJSON.hasOwnProperty("message")?Craft.cp.displayError(r.responseJSON.message):r.responseJSON.hasOwnProperty("error")&&Craft.cp.displayError(r.responseJSON.error):r.hasOwnProperty("statusText")&&Craft.cp.displayError(r.statusText)}},Craft.Triggers.Triggers=Garnish.Base.extend({init:function(){this.initDelete()},initDelete:function(){$(".tableview .delete").click((function(){var r=$(this).parent().parent();confirm(Craft.t("triggers","Are you sure you want to delete this trigger ?"))&&$.ajax({url:Craft.getActionUrl("triggers/cp-triggers/delete"),data:{id:$(this).data("id")},dataType:"json"}).fail((function(r){Craft.Triggers.handleError(r)})).done((function(e){e.message&&Craft.cp.displayNotice(e.message),r.remove()}))}))}})})();