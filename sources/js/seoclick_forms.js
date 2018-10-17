jQuery(document).ready(function ($) {

    let inputs,
        sitePattern = "^[\\w \\.]+[\\.]{1}[\\D]{2,4}$",
        phonePattern = "^[\\d \\+]{1,5}[\\( \\d \\) \\s]{1,10}[-?\\d \\s]+$",
        emailPattern = "^[\\w \\.]+[@]{1}[\\w]+[\\.]{1}[\\D]{2,4}$",
        forms = $(".form-validate"),
        default_site_tooltip_text = "Адрес должен быть в формате site.ru",
        default_email_tooltip_text = "Email должен быть в формате почта@mail.ru",
        default_phone_tooltip_text = "Телефон должен быть в формате +код страны(код оператора)номер телефона. Например +375(33)123-45-67";

    $.each(forms, function (index, form) {

        //получаем валиируемые поля формы
        inputs = $(form).find(".validate");

        //вызываем функцию установки обработчиков на поля формы
        setEvents(inputs);
        //вешам обработчик отправки формы
        $(form).on("submit", sendData);
    });

    /*
    * Функция проверяет атрибуты pattern и data-tooltip
    * и в случае их отсутсвия присваивает им значения
    * по умолчанию при их наличии и возвращает true
    * иначе false
    * */
    function checkInputPatterns(input, def_pattern, def_tooltip_text) {

        let pattern = input.attr("pattern"),
            tooltip = input.data("tooltip"),
            response = {pattern: false, tooltip: false};

        if (pattern === undefined) {
            if (def_pattern !== undefined) {
                input.attr("pattern", def_pattern);
                response.pattern = true;
            } else {
                response.pattern = false;
            }
        } else {
            response.pattern = true;
        }

        if (tooltip === undefined) {
            if (def_tooltip_text !== undefined) {
                input.data("tooltip", def_tooltip_text);
                response.tooltip = true;
            } else {
                response.tooltip = false;
            }
        } else {
            response.tooltip = true;
        }

        return response;
    }

    /*
    * Функция управления подсказками
    * */
    function tooltip(input, command) {

        switch (command) {
            case 'create':
                input.after("<div class='invalid-tooltip'><div>" + input.data("tooltip") + "</div></div>");
                input.next().hide();
                break;
            case 'show':
                input.next().show();
                input.next().animate({
                    opacity: 1
                }, 300, () => {
                    input.next().show();
                });
                break;
            case 'hide':
                input.next().animate({
                    opacity: 0
                }, 300, () => {
                    input.next().hide();
                });
                break;
        }
    }

    //функция установки обработчиков полей формы
    function setEvents(inputs) {

        $.each(inputs, function (index, input) {

            let input_obj = $(input),
                data_type = input_obj.attr("data-validate"),
                validation;

            //устанавливвваем шаблоны для полей формы
            switch (data_type) {
                case "site":
                    validation = checkInputPatterns(input_obj, sitePattern, default_site_tooltip_text);
                    break;
                case "email":
                    validation = checkInputPatterns(input_obj, emailPattern, default_email_tooltip_text);
                    break;
                case "phone":
                    validation = checkInputPatterns(input_obj, phonePattern, default_phone_tooltip_text);
                    input_obj.on("paste", checkPasteText);
                    input_obj.on("keypress", checkPressedKey);
                    break;
                default:
                    validation = checkInputPatterns(input_obj);
                    if (!validation.tooltip && !validation.pattern) return true;
            }
            if (validation.tooltip) {
                tooltip(input_obj, 'create');
                input_obj.on("mouseenter", function () {
                    tooltip(input_obj, 'show');
                });
                input_obj.on("focusout mouseleave", function () {

                    tooltip(input_obj, 'hide');
                });
            }
            //устанавливаем обработчик на проверку вводимых данных
            if (validation.pattern) {
                input_obj.on("change keyup", function () {

                    let check = checkPattern($(this));

                    if (validation.tooltip) {
                        if (!check) {
                            tooltip(input_obj, 'show');
                        } else {
                            tooltip(input_obj, 'hide');
                        }
                    }
                });
                input_obj.on("focusout mouseleave", function () {

                    if (input_obj.val() === '') {
                        input_obj.removeClass('invalid');
                    }
                });
            }
        });
    }

    //функция проверки вставленные данные в поле формы по шаблону
    function checkPasteText(event) {
        let pattern,
            data = event.originalEvent.clipboardData.getData('text/plain');

        pattern = getPattern($(this));

        //проверка данных по шаблону
        if (!pattern.test(data)) {
            event.preventDefault();
        }
    }

    /*
    * функция проверки нажатой клавиши
    * Функция возвращет false на все клавиши,
    * кроме цифр, знаков + - ( ) и пробелов
    */
    function checkPressedKey(event) {

        let e = event;

        if (e.ctrlKey || e.altKey || e.metaKey) {
            return;
        }

        let chr = getChar(e);

        // с null надо осторожно в неравенствах, т.к. например null >= '0' => true!
        // на всякий случай лучше вынести проверку chr = null отдельно
        if (chr === null) {
            return;
        }

        if (chr < '0' || chr > '9') {
            if (chr !== "+" && chr !== "-" && chr !== "(" && chr !== ")" && chr !== " ") {
                return false;
            }
        }
    }

    //функция возвращает код символа
    function getChar(event) {

        if (event.code === "Space" || event.code === "Equal" || event.code === "Digit9" || event.code === "Digit0") {
            return 0;
        }
        if (event.which === null) { // IE
            if (event.keyCode < 32) return null; // спец. символ
            return String.fromCharCode(event.keyCode)
        }

        if (event.which !== 0 && event.charCode !== 0) { // все кроме IE
            if (event.which < 32) return null; // спец. символ
            return String.fromCharCode(event.which); // остальные
        }

        return null; // спец. символ
    }

    /*
    * Функция проверки введенных данных по шаблону
    * возвращает false если проверка не пройдена
    */
    function checkPattern(inputData) {

        let value = inputData.val(), pattern;

        pattern = new RegExp(inputData.attr("pattern"));

        //проверка данных по шаблону
        if (!pattern.test(value)) {
            inputData.addClass("invalid");
            return false;
        } else {
            inputData.removeClass("invalid");
            return true;
        }
    }

    //функция отправки данных на сервер
    function sendData(event) {

        event.preventDefault();

        let form = $(this), capchaResponse, request, data,
            formContainer = form.closest(".seoclick-forms"),
            messageBox = formContainer.find(".message-container");

        data = form.serializeArray();
        data = objectifyFormData(data);

        messageBox.addClass("active");
        if ($(formContainer).hasClass("seoclick-forms-popup-wrap")) {
            $(formContainer).find(".container").animate({
                scrollTop: 0
            }, 300);
        } else {
            $('html, body').animate({
                scrollTop: formContainer.offset().top - 200
            }, 300);
        }

        capchaResponse = form.find(".g-recaptcha-response").val();
        if (capchaResponse === "") {
            messageBox.html("Не пройдена проверка 'Я не робот'");
            return false;
        }

        messageBox.html("Отправка...");
        // Формируем параметры запроса
        request = {
            'option': 'com_ajax', // Используем AJAX интерфейс
            'module': 'seoclick_forms', // Название модуля без mod_
            'format': 'json', // Формат возвращаемых данных
            'g-recaptcha-response': capchaResponse, //ответ капчи
            'data': data // данные формы
        };

        $.ajax({
            type: 'POST',
            data: request,

            success: function (respond) {

                messageBox.html(respond.data);
            },
            error: function (jqXHR, textStatus) {
                messageBox.html('Ошибка AJAX запроса: ' + textStatus);
            }
        });

        grecaptcha.reset();
        return true;
    }

    /*
    * Функция преобразования сериализованного массива
    * функция принимает сериализованый двухмерный массив данных
    * производит его преобразования из двухмерного массива
    * в одномерный и возвращает полученный массив
    */
    function objectifyFormData(formArray) {//преобразование массива

        let returnArray = {};
        for (let i = 0; i < formArray.length; i++) {
            returnArray[formArray[i]['name']] = formArray[i]['value'];
        }
        return returnArray;
    }
});