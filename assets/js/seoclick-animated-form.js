jQuery(document).ready(function ($) {

    var currentAnimation = null,
        form = $(".seoclick-forms"),
        formFields = form.find(".form-field"),
        fio = form.find("[name='name']"),
        email = form.find("[name='email']"),
        phone = form.find("[name='phone']"),
        comment = form.find("[name='comment']"),
        linePath = '#line-path';

    //Обработчики фокуса, наведения мыши и нажатия клавиш
    fio.on("focus mouseenter keypress", {dashoffset: 0, dasharray: '70 400'}, animateLine);
    email.on("focus mouseenter keypress", {dashoffset: -103, dasharray: '70 400'}, animateLine);
    phone.on("focus mouseenter keypress", {dashoffset: -197, dasharray: '70 400'}, animateLine);
    comment.on("focus mouseenter keypress", {dashoffset: -197, dasharray: '225 400'}, animateLine);

    //Вешаем обработчики потери фокуса на поля ввода
    formFields.on("blur", {dashoffset: 0, dasharray: '70 400'}, animateLine);
    //Вешаем обработчики выхода курсора мыши за пределы формы
    form.mouseleave(function () {

        var trigger = false;

        //Проверка фокуса на полях ввода
        $.each(formFields, function (key, formField) {

            if ($(formField).is(":focus")) {

                $(formField).trigger("focus");
                trigger = true;
                return false;
            }
        });
        //Если фокуса нет возвращаем исходное положение линии
        if (!trigger) {
            fio.trigger("blur");
        }
    });

    //Функция анимирует передвижения линии
    function animateLine(event) {

        if (currentAnimation) {
            currentAnimation.pause();
        }

        currentAnimation = anime({
            targets: linePath,
            strokeDashoffset: event.data.dashoffset,
            strokeDasharray: event.data.dasharray,
            easing: 'easeInOutQuart'
        });
    }
});
