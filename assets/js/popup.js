jQuery(document).ready(function($){

    var formContainer = $(".seoclick-forms-popup-wrap"),//контейнер формы
    show_button = $(".seoclickFormsShowpopUpForm");//кнопка показа формы

    formContainer.find("form").wrap('<div class="modal-wrap"></div>').append('<span class="close-button">X</span>');
    $(".modal-wrap").append('<div class="close-background"></div>');
    //Обработчики скрытия форм
    $('.close-button').on("click", closeModal);
    $('.close-background').on("click", closeModal);
    //обработчик кнопок вызова форм
    show_button.on("click", function(){

        $(this).closest(".seoclick-forms-popup-wrap").find("form").toggleClass("active").closest(".modal-wrap").toggleClass("active").find(".close-background").toggleClass("active");
    });

    //функция закрытия формы
    function closeModal(){

        var modal_wrap = $(this).closest(".modal-wrap");

        modal_wrap.find("form").removeClass('active');
        setTimeout(function(){
            modal_wrap.removeClass('active');
            modal_wrap.find(".close-background").removeClass('active');
        },300);
    }
});