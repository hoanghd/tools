!function($) {
    $('#vc_properties-panel .flip-input-text').click(function(e){
        e.preventDefault();
        var $input = $(this).prev().find('.my_param_field'),
            text = $input.val();
        $input.val(text.split("").reverse().join(""));
    });
}(window.jQuery);

vc.atts.slzcore_market = {
    parse: function(param) {
        alert('parse');
    },
    extractValues: function(param, $el) {
        alert('extractValues');
    },
    parseOne: function(param) {
        alert('parseOne');
    },
    init: function(param, $field) {
        
    }
};