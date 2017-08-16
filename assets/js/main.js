var $ = require('jquery');
require('bootstrap-sass');


(function() {
    const buy_pass_form = $('#buy_pass_form');
    if(buy_pass_form.length > 0) {
        const price = $('#buy_pass_form_pass_price');
        buy_pass_form.find('input[type="radio"]').on('click', function(event) {
            const element = $(this);
            price.val(element.data('price'));
        });

        buy_pass_form.find('input[type="submit"]').on('click', function(event) {
            return confirm("Biztosan meg szeretné vásárolni a kiválasztott bérletet " + price.val() + " forintért?");
        });
    }
})();