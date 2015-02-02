/**
 * Created by martin on 5.12.14.
 */

$().ready(function(){
    $('.alert').click(function(){

        var alertText = $(this).data('alert');

        if(alertText == undefined) {
            alertText = 'Do you really want to proceed?'
        }

        return confirm(alertText);
    });
});
