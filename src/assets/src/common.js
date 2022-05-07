import './common.scss';

Craft.Triggers = {
    handleError: function (data) {
        if (data.hasOwnProperty('responseJSON')) {
            if (data.responseJSON.hasOwnProperty('message')) {
                Craft.cp.displayError(data.responseJSON.message);
            } else if (data.responseJSON.hasOwnProperty('error')) {
                Craft.cp.displayError(data.responseJSON.error);
            }
        } else if (data.hasOwnProperty('statusText')) {
            Craft.cp.displayError(data.statusText);
        } 
    }
};