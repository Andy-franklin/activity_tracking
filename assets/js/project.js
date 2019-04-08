const $ = require('jquery');
var moment = require('moment');

moment().format();

$(function () {
        var projectId = $('h1').data('project-id');
        var nav = $('.js-calendar-nav');
        nav.on('click', '.js-next', function (e) {
            var weekCommencing = e.delegateTarget.getAttribute('data-weekCommencing');
            var currentDate = moment(parseDate(weekCommencing));
            currentDate.add(7, 'days');
            var newUrl = '/authed/project/'+ projectId + '?weekCommencing='+ moment(currentDate).format('DD-MM-YYYY');
            window.location.replace(newUrl)
        });


        nav.on('click', '.js-previous', function (e) {
            var weekCommencing = e.delegateTarget.getAttribute('data-weekCommencing');
            var currentDate = moment(parseDate(weekCommencing));
            console.log(currentDate);
            currentDate.add(-7, 'days');
            console.log(currentDate);
            var newUrl = '/authed/project/'+ projectId + '?weekCommencing='+ moment(currentDate).format('DD-MM-YYYY');
            window.location.replace(newUrl)
        });

        $('button.js-planned-hours').on('click', function (e) {
            var tableBody = $('tbody.js-planned-hours');
            var authorRows = tableBody.find('tr');

            var hourData = [];
            authorRows.each(function () {
                var authorId = $(this).data('author-id');
                var hourInput = $(this).find('input[type="number"]').val();
                if (hourInput === '') {
                    hourInput = 0;
                }

                hourData.push({
                    'authorId': authorId,
                    'hours': hourInput
                });
            });

            var requestData = {
                'projectId': tableBody.data('project'),
                'weekCommencing': $('.js-calendar-nav').data('weekcommencing'),
                'plannedHours': hourData
            };

            console.table(requestData);


            $.ajax({
                url: '/authed/api/plannedhours/create',
                method: 'POST',
                data: requestData,
                dataType: 'JSON',
                success: function (responseData) {
                    toastr.success('Planned hours updated successfully');
                },
                error: function (responseData) {
                    toastr.error('Sorry there was an error. Check the console for more information.');
                    console.log('error: ' + responseData)
                }
            });
        })
    });

function parseDate(input) {
    var parts = input.match(/(\d+)/g);
    return new Date(parts[2], parts[1]-1, parts[0]);
}
