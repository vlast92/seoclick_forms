"use strict";

jQuery(document).ready(function ($) {

    var formContainer = $(".seoclick-forms-popup-wrap"),
        //контейнер формы
    show_button = $(".seoclickFormsShowpopUpForm"); //кнопка показа формы

    $("body").append(formContainer);
    formContainer.children().wrapAll('<div class="modal-wrap"></div>').append('<span class="close-button"><img src="/modules/mod_seoclick_forms/assets/close.svg" alt="close"/></span>');
    $(".modal-wrap").append('<div class="close-background"></div>');
    //Обработчики скрытия форм
    $('.close-button').on("click", closeModal);
    $('.close-background').on("click", closeModal);
    //обработчик кнопок вызова форм
    show_button.on("click", function () {

        var form = $('#' + $(this).data('form'));

        form.find(".modal-wrap > *:not('.close-background')").toggleClass("active").closest(".modal-wrap").toggleClass("active");
        $('body').css('overflow-y', 'hidden');
    });

    //функция закрытия формы
    function closeModal() {

        var modal_wrap = $(this).closest(".modal-wrap");

        modal_wrap.find("> *:not('.close-background')").removeClass('active');
        setTimeout(function () {
            modal_wrap.removeClass('active');
            modal_wrap.find(".close-background").removeClass('active');
            $('body').css('overflow-y', 'auto');
        }, 300);
    }
});
//# sourceMappingURL=popup.js.map