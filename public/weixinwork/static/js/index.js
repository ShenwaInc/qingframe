function syntaxHighlight(json) {
    if (typeof json != 'string') {
        json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

function prepareStr(str) {
    try {
        return syntaxHighlight(JSON.stringify(JSON.parse(str.replace(/'/g, '"')), null, 2));
    } catch (e) {
        return str;
    }
}

var storage = (function () {
    var uid = new Date;
    var storage;
    var result;
    try {
        (storage = window.localStorage).setItem(uid, uid);
        result = storage.getItem(uid) == uid;
        storage.removeItem(uid);
        return result && storage;
    } catch (exception) {
    }
}());

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (!this.value) {
            return;
        }
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

$(document).ready(function () {

    if (storage) {
        $('#verify').val(storage.getItem('verify'));
        $('#token').val(storage.getItem('token'));
        $('#apiUrl').val(storage.getItem('apiUrl'));
    }

    $('[data-toggle="tooltip"]').tooltip({
        placement: 'bottom'
    });

    $(window).on("resize", function () {
        $("#sidebar").css("max-height", $(window).height() - 80);
    });

    $(window).trigger("resize");

    $(document).on("click", "#sidebar .list-group > .list-group-item", function () {
        $("#sidebar .list-group > .list-group-item").removeClass("current");
        $(this).addClass("current");
    });
    $(document).on("click", "#sidebar .child a", function () {
        var heading = $("#heading-" + $(this).data("id"));
        if (!heading.next().hasClass("in")) {
            $("a", heading).trigger("click");
        }
        $("html,body").animate({scrollTop: heading.offset().top - 70});
    });

    $('code[id^=response]').hide();

    $.each($('pre[id^=sample_response],pre[id^=sample_post_body]'), function () {
        if ($(this).html() == 'NA') {
            return;
        }
        var str = prepareStr($(this).html());
        $(this).html(str);
    });

    $("[data-toggle=popover]").popover({placement: 'right'});

    $('[data-toggle=popover]').on('shown.bs.popover', function () {
        var $sample = $(this).parent().find(".popover-content"),
            str = $(this).data('content');
        if (typeof str == "undefined" || str === "") {
            return;
        }
        var str = prepareStr(str);
        $sample.html('<pre>' + str + '</pre>');
    });

    $('body').on('click', '#save_data', function (e) {
        if (storage) {
            storage.setItem('verify', $('#verify').val());
            storage.setItem('token', $('#token').val());
            storage.setItem('apiUrl', $('#apiUrl').val());
        } else {
            alert('Your browser does not support local storage');
        }
    });

    $('body').on('click', '.send', function (e) {
        e.preventDefault();
        var form = $(this).closest('form');
        //added /g to get all the matched params instead of only first
        var matchedParamsInRoute = $(form).attr('action').match(/[^{]+(?=\})/g);
        var theId = $(this).attr('rel');
        //keep a copy of action attribute in order to modify the copy
        //instead of the initial attribute
        var url = $(form).attr('action');

        var formData = new FormData();

        $(form).find('input').each(function (i, input) {
            if ($(input).attr('type') == 'file') {
                formData.append($(input).attr('name'), $(input)[0].files[0]);
            } else {
                formData.append($(input).attr('name'), $(input).val())
            }
        });

        var index, key, value;

        if (matchedParamsInRoute) {
            var params = {};
            formData.forEach(function (value, key) {
                params[key] = value;
            });
            for (index = 0; index < matchedParamsInRoute.length; ++index) {
                try {
                    key = matchedParamsInRoute[index];
                    value = params[key];
                    if (typeof value == "undefined")
                        value = "";
                    url = url.replace("\{" + key + "\}", value);
                    formData.delete(key);
                } catch (err) {
                    console.log(err);
                }
            }
        }

        var headers = {};

        var verify = $('#verify').val();
        if (verify.length > 0) {
            headers['verify'] = verify;
        }

        var token = $('#token').val();
        var tokenStr = '';
        if (token.length > 0) {
            headers['token'] = token;
            headers['_token'] = token;
            formData.append("token", token);
            formData.append("_token", token);
            tokenStr += '&token='+token;
            tokenStr += '&_token='+token;
        }

        $("#sandbox" + theId + " .headers input[type=text]").each(function () {
            val = $(this).val();
            if (val.length > 0) {
                headers[$(this).prop('name')] = val;
            }
        });
        $("#sandbox" + theId + " .headers input[type=string]").each(function () {
            val = $(this).val();
            if (val.length > 0) {
                headers[$(this).prop('name')] = val;
            }
        });

        $.ajax({
            url: $('#apiUrl').val() + url,
            data: $(form).prop('method').toLowerCase() == 'get' ? $(form).serialize()+tokenStr : formData,
            type: $(form).prop('method') + '',
            dataType: 'json',
            contentType: false,
            processData: false,
            headers: headers,
            success: function (data, textStatus, xhr) {
                if (typeof data === 'object') {
                    var str = JSON.stringify(data, null, 2);
                    $('#response' + theId).html(syntaxHighlight(str));
                } else {
                    $('#response' + theId).html(data || '');
                }
                $('#response_headers' + theId).html('HTTP ' + xhr.status + ' ' + xhr.statusText + '<br/><br/>' + xhr.getAllResponseHeaders());
                $('#response' + theId).show();
            },
            error: function (xhr, textStatus, error) {
                try {
                    var str = JSON.stringify($.parseJSON(xhr.responseText), null, 2);
                } catch (e) {
                    var str = xhr.responseText;
                }
                $('#response_headers' + theId).html('HTTP ' + xhr.status + ' ' + xhr.statusText + '<br/><br/>' + xhr.getAllResponseHeaders());
                $('#response' + theId).html(syntaxHighlight(str));
                $('#response' + theId).show();
            }
        });
        return false;
    });
});