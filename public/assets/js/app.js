// Thesis Scheduling System - shared front-end helpers
window.TSS = window.TSS || {};

/**
 * Reusable hour x day grid calendar.
 *
 * Renders a table with one row per hour slot (default 7:00-17:00) and one
 * column per day of the given month, then wires up prev/next/today
 * navigation and per-cell click handling.
 *
 * This was originally duplicated between the student "request a slot"
 * calendar and the professor "set my availability" calendar. It now lives
 * here once, and each page supplies the bits that differ (which endpoint to
 * fetch from, how a slot maps to a CSS class, and what a click should do).
 *
 * Usage:
 *   const cal = TSS.createHourGridCalendar({
 *     container: '#calendar-container',
 *     monthLabelSelector: '#calendar-month-label', // optional
 *     year: currentYear,
 *     month: currentMonth,
 *     prevBtn: '#prev-month',
 *     nextBtn: '#next-month',
 *     todayBtn: '#today-month',
 *     fetchUrl: baseUrl + 'views/ajax/get_availability.php',
 *     fetchParams: function () { return { professor_id: professorId }; },
 *     cellClass: function (available) { return available ? 'available' : 'unavailable'; },
 *     onCellClick: function (cell) {
 *       // cell = { date, time, isAvailable, $cell, reload }
 *     }
 *   });
 *   cal.init();
 */
TSS.createHourGridCalendar = function (options) {
    const opts = Object.assign({
        startHour: 7,
        endHour: 17,
        monthLabelSelector: null
    }, options);

    const state = {
        year: opts.year,
        month: opts.month
    };

    function updateMonthLabel() {
        if (!opts.monthLabelSelector) return;
        const date = new window.Date(state.year, state.month - 1, 1);
        $(opts.monthLabelSelector).text(date.toLocaleString('en-US', { month: 'long', year: 'numeric' }));
    }

    function load() {
        const params = Object.assign(
            { year: state.year, month: state.month },
            opts.fetchParams ? opts.fetchParams() : {}
        );
        $.ajax({
            url: opts.fetchUrl,
            data: params,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    render(res.slots || {});
                } else {
                    $(opts.container).html('<p class="text-danger">Error loading availability.</p>');
                }
            },
            error: function (xhr) {
                let msg = 'Server error.';
                try {
                    const body = JSON.parse(xhr.responseText);
                    if (body && body.message) msg = body.message;
                } catch (e) {
                    // Response wasn't JSON (likely a PHP error page) - fall through to generic message.
                }
                console.error('Calendar load failed:', xhr.status, xhr.responseText);
                $(opts.container).html('<p class="text-danger">' + msg + ' (HTTP ' + xhr.status + ')</p>');
            }
        });
    }

    function render(slotsData) {
        const start = new window.Date(state.year, state.month - 1, 1);
        const end = new window.Date(state.year, state.month, 0);
        const days = [];
        for (let d = new window.Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            days.push(new window.Date(d));
        }

        const timeSlots = [];
        for (let h = opts.startHour; h <= opts.endHour; h++) {
            timeSlots.push(String(h).padStart(2, '0') + ':00:00');
        }

        let html = '<table class="calendar-table table table-bordered"><thead><tr><th>Time</th>';
        days.forEach(day => {
            html += '<th>' + day.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric' }) + '</th>';
        });
        html += '</tr></thead><tbody>';

        timeSlots.forEach(time => {
            html += '<tr><td class="time-label">' + time.substr(0, 5) + '</td>';
            days.forEach(day => {
                const dateStr = day.toISOString().slice(0, 10);
                const key = dateStr + ' ' + time;
                const available = !!slotsData[key];
                const cls = opts.cellClass ? opts.cellClass(available) : (available ? 'available' : 'unavailable');
                html += `<td class="slot ${cls}" data-date="${dateStr}" data-time="${time}"></td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        $(opts.container).html(html);

        $(opts.container).find('.slot').on('click', function () {
            const $cell = $(this);
            const date = $cell.data('date');
            const time = $cell.data('time');
            const isAvailable = $cell.hasClass('available');
            if (opts.onCellClick) {
                opts.onCellClick({ date: date, time: time, isAvailable: isAvailable, $cell: $cell, reload: load });
            }
        });

        updateMonthLabel();
    }

    function prevMonth() {
        if (state.month === 1) { state.month = 12; state.year--; } else state.month--;
        load();
    }

    function nextMonth() {
        if (state.month === 12) { state.month = 1; state.year++; } else state.month++;
        load();
    }

    function goToday() {
        const now = new window.Date();
        state.year = now.getFullYear();
        state.month = now.getMonth() + 1;
        load();
    }

    function init() {
        if (opts.prevBtn) $(opts.prevBtn).on('click', prevMonth);
        if (opts.nextBtn) $(opts.nextBtn).on('click', nextMonth);
        if (opts.todayBtn) $(opts.todayBtn).on('click', goToday);
        updateMonthLabel();
        load();
    }

    return { init: init, load: load, state: state };
};