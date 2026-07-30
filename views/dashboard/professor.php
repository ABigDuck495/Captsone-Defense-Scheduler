<?php
require_once __DIR__ . '/../layout/header.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'professor') {
    header('Location: ' . TSS_BASE_URL . 'views/auth/login.php');
    exit;
}

$professorId = $_SESSION['user_id'];
$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));
$monthName = date('F Y', strtotime("$year-$month-01"));

// $pdo = getDbConnection(); // now available from functions.php
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->execute([$professorId]);
$professor = $stmt->fetch();
$professorName = $professor['full_name'] ?? 'Professor';

?>
<link rel="stylesheet" href="../../public/assets/css/custom.css">
<script>
    const professorId = <?= json_encode($professorId) ?>;
    const baseUrl = <?= json_encode($__tssBaseUrl) ?>;
    let currentYear = <?= $year ?>;
    let currentMonth = <?= $month ?>;
</script>

<header class="dash-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h1 class="mb-0">Thesis Scheduling System</h1>
            <span class="badge bg-forest">Professor</span>
        </div>
        <a href="<?= $__tssBaseUrl ?>views/auth/logout.php" class="btn btn-sm btn-outline-light">Log out</a>
    </div>
</header>

<main class="container py-4">
    <p class="text-body-secondary small mb-3">Welcome, <?= htmlspecialchars($professorName) ?></p>
    <div id="form-alert"></div>

    <div class="card p-3 p-md-4 mb-4">
        <span class="eyebrow">Upcoming</span>
        <h2 class="h5 mb-3">My Booked Defenses</h2>
        <div id="booked-list"><div class="dash-empty">Loading...</div></div>
    </div>

    <ul class="nav nav-tabs mb-4" id="prof-nav">
        <li class="nav-item"><a class="nav-link active" href="#" data-target="#tab-availability">My Availability</a></li>
        <li class="nav-item"><a class="nav-link" href="#" data-target="#tab-requests">Requests</a></li>
    </ul>

    <div class="tab-pane" id="tab-availability">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Availability for <span id="availability-month-label"><?= $monthName ?></span></h2>
            <div>
                <button class="btn btn-sm btn-outline-secondary" id="prev-month">&lt;</button>
                <button class="btn btn-sm btn-outline-secondary" id="next-month">&gt;</button>
                <button class="btn btn-sm btn-outline-secondary" id="today-month">Today</button>
            </div>
        </div>
        <div class="avail-cal-head">
            <div class="avail-cal-dow">Su</div><div class="avail-cal-dow">Mo</div><div class="avail-cal-dow">Tu</div>
            <div class="avail-cal-dow">We</div><div class="avail-cal-dow">Th</div><div class="avail-cal-dow">Fr</div>
            <div class="avail-cal-dow">Sa</div>
        </div>
        <div id="calendar-container"><div class="dash-empty">Loading...</div></div>
        <div class="cal-legend mt-2">
            <span><span class="slot-dot open"></span>Open slot</span>
            <span><span class="slot-dot booked"></span>Booked defense</span>
        </div>
        <div id="day-detail-container"></div>
        <p class="text-body-secondary small mt-2">Click a day to view and toggle its hourly slots. Check "repeat weekly" before adding if you want it to recur.</p>
        <style>
            .avail-cal-head, .avail-cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
            .avail-cal-head { margin-bottom: 6px; }
            .avail-cal-dow { text-align: center; font-size: .72rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: var(--ink-soft); }
            .cal-cell:not(.cal-empty) {
                cursor: pointer;
                transition: box-shadow .12s ease, border-color .12s ease, background-color .12s ease;
            }
            .cal-cell:not(.cal-empty):hover {
                border-color: var(--forest-mid);
                box-shadow: 0 2px 10px rgba(31,61,43,.10);
                background-color: var(--forest-pale);
            }
        </style>
    </div>

    <div class="tab-pane d-none" id="tab-requests">
        <span class="eyebrow">Defense panel requests</span>
        <h2 class="h5 mb-3">Requests needing your response</h2>
        <div id="requests-list"><div class="dash-empty">No pending requests.</div></div>
    </div>
</main>

<footer class="tt-footer" id="site-footer"></footer>
<script src="<?= $__tssBaseUrl ?>public/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= $__tssBaseUrl ?>public/assets/js/app.js"></script>
<script>
$(function() {
    // Tab switching
    $('#prof-nav a').on('click', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        $('#prof-nav a').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').addClass('d-none');
        $(target).removeClass('d-none');
        if (target === '#tab-requests') loadRequests();
    });

    let availMonthData = {};   
    let bookedDatesSet = new Set();
    let selectedDay = null;

    function toLocalDateStr(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function hourSlots() {
        const hours = [];
        for (let h = 7; h <= 17; h++) hours.push(String(h).padStart(2, '0') + ':00:00');
        return hours;
    }

    function slotsForDate(dateStr) {
        return hourSlots().map(time => ({
            time,
            available: availMonthData[dateStr + ' ' + time] === 'available'
        }));
    }

    function loadCalendar() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_availability.php',
            data: { professor_id: professorId, year: currentYear, month: currentMonth },
            dataType: 'json',
            success: function(res) {
                if (res.success) { availMonthData = res.slots; renderCalendar(); }
                else $('#calendar-container').html('<p class="text-danger">Error loading availability.</p>');
            },
            error: function(xhr) {
                console.error('get_availability.php failed:', xhr.status, xhr.responseText);
                $('#calendar-container').html('<p class="text-danger">Error loading availability. (HTTP ' + xhr.status + ')</p>');
            }
        });
    }

    function renderCalendar() {
        const first = new window.Date(currentYear, currentMonth - 1, 1);
        const startWeekday = first.getDay();
        const daysInMonth = new window.Date(currentYear, currentMonth, 0).getDate();
        const todayStr = toLocalDateStr(new window.Date());

        let html = '<div class="avail-cal-grid">';
        for (let i = 0; i < startWeekday; i++) {
            html += '<div class="cal-cell cal-empty"></div>';
        }
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = currentYear + '-' + String(currentMonth).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            const openCount = slotsForDate(dateStr).filter(s => s.available).length;
            const isBooked = bookedDatesSet.has(dateStr);

            let cls = 'cal-cell';
            if (openCount > 0 || isBooked) cls += ' has-slots';
            if (dateStr === todayStr) cls += ' is-today';
            if (dateStr === selectedDay) cls += ' is-selected';

            let dots = '';
            if (openCount > 0) dots += `<span class="slot-dot open" title="${openCount} open slot(s)"></span>`;
            if (isBooked) dots += '<span class="slot-dot booked" title="Booked defense"></span>';

            html += `<div class="${cls}" data-date="${dateStr}">
                        <div class="cal-daynum">${day}</div>
                        <div class="cal-dots">${dots}</div>
                     </div>`;
        }
        const trailing = (7 - ((startWeekday + daysInMonth) % 7)) % 7;
        for (let i = 0; i < trailing; i++) {
            html += '<div class="cal-cell cal-empty"></div>';
        }
        html += '</div>';
        $('#calendar-container').html(html);

        $('#calendar-container .cal-cell[data-date]').on('click', function() {
            selectedDay = $(this).data('date');
            renderCalendar();
            renderDayDetail(selectedDay);
        });

        if (selectedDay) renderDayDetail(selectedDay);
    }

    function renderDayDetail(dateStr) {
        const slots = slotsForDate(dateStr);
        const label = new window.Date(dateStr + 'T00:00:00')
            .toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });

        let rows = '';
        slots.forEach(s => {
            rows += `<div class="slot-row">
                        <span>${s.time.substr(0,5)}</span>
                        <span>
                            <span class="badge ${s.available ? 'status-approved' : 'status-rejected'}">${s.available ? 'Available' : 'Unavailable'}</span>
                            <button class="btn btn-sm ${s.available ? 'btn-outline-danger' : 'btn-outline-success'} toggle-slot" data-time="${s.time}">
                                ${s.available ? 'Remove' : 'Add'}
                            </button>
                        </span>
                     </div>`;
        });
        $('#day-detail-container').html(`
            <div class="day-detail">
                <div class="day-detail-title">${label}</div>
                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:.85rem; cursor:pointer;">
                    <input type="checkbox" id="repeat-weekly-check">
                    <span>When adding, also repeat this time slot weekly for the rest of the month</span>
                </label>
                ${rows}
            </div>
        `);

        $('.toggle-slot').on('click', function() {
            const time = $(this).data('time');
            const isAvailable = availMonthData[dateStr + ' ' + time] === 'available';
            const action = isAvailable ? 'remove' : 'add';
            const repeat = !isAvailable && $('#repeat-weekly-check').is(':checked');
            $.ajax({
                url: baseUrl + 'views/ajax/toggle_availability.php',
                method: 'POST',
                data: JSON.stringify({
                    professor_id: professorId,
                    date: dateStr,
                    time: time,
                    action: action,
                    repeat: repeat ? 'weekly' : false
                }),
                contentType: 'application/json',
                success: function(res) {
                    if (res.success) loadCalendar();
                    else alert('Error: ' + (res.message || 'Failed.'));
                },
                error: function() { alert('Server error.'); }
            });
        });
    }

    function updateMonthLabel() {
        const date = new window.Date(currentYear, currentMonth-1, 1);
        $('#availability-month-label').text(date.toLocaleString('en-US', { month: 'long', year: 'numeric' }));
    }

    function clearSelectedDay() {
        selectedDay = null;
        $('#day-detail-container').empty();
    }

    $('#prev-month').click(function() {
        if (currentMonth === 1) { currentMonth = 12; currentYear--; } else currentMonth--;
        clearSelectedDay();
        updateMonthLabel();
        loadCalendar();
    });
    $('#next-month').click(function() {
        if (currentMonth === 12) { currentMonth = 1; currentYear++; } else currentMonth++;
        clearSelectedDay();
        updateMonthLabel();
        loadCalendar();
    });
    $('#today-month').click(function() {
        const today = new window.Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth()+1;
        clearSelectedDay();
        updateMonthLabel();
        loadCalendar();
    });
    loadCalendar();

    function loadBooked() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_booked.php',
            data: { professor_id: professorId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.booked.length) {
                    bookedDatesSet = new Set(
                        res.booked
                            .filter(b => b.status !== 'cancelled' && b.status !== 'rescheduled')
                            .map(b => b.defense_date)
                    );
                    let html = '<ul class="list-unstyled">';
                    res.booked.forEach(item => {
                        html += `<li><strong>${item.defense_date}</strong> ${item.start_time} - ${item.end_time} at ${item.venue || 'TBD'}</li>`;
                    });
                    html += '</ul>';
                    $('#booked-list').html(html);
                } else {
                    bookedDatesSet = new Set();
                    $('#booked-list').html('<div class="dash-empty">No booked defenses yet.</div>');
                }
                renderCalendar();
            }
        });
    }

    function loadRequests() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_requests.php',
            data: { professor_id: professorId, status: 'pending' },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.requests.length) {
                    let html = '';
                    res.requests.forEach(req => {
                        html += `<div class="card mb-2 p-3">
                            <div class="d-flex justify-content-between">
                                <div><strong>Group ${req.group_id}</strong> – ${req.defense_date} ${req.start_time}-${req.end_time}</div>
                                <div>
                                    <button class="btn btn-sm btn-success approve-request" data-request="${req.request_id}">Approve</button>
                                    <button class="btn btn-sm btn-danger reject-request" data-request="${req.request_id}">Reject</button>
                                </div>
                            </div>
                        </div>`;
                    });
                    $('#requests-list').html(html);
                    $('.approve-request, .reject-request').on('click', function() {
                        const requestId = $(this).data('request');
                        const decision = $(this).hasClass('approve-request') ? 'approved' : 'rejected';
                        const $btn = $(this);
                        $btn.prop('disabled', true).text('Processing...');
                        $.ajax({
                            url: baseUrl + 'views/ajax/approve_schedule.php',
                            method: 'POST',
                            data: JSON.stringify({ request_id: requestId, decision: decision }),
                            contentType: 'application/json',
                            success: function(res) {
                                if (res.success) {
                                    alert('Response recorded.');
                                    loadRequests();
                                } else {
                                    alert('Error: ' + (res.message || 'Failed.'));
                                }
                            },
                            error: function() { alert('Server error.'); },
                            complete: function() { $btn.prop('disabled', false).text(decision==='approved'?'Approve':'Reject'); }
                        });
                    });
                } else {
                    $('#requests-list').html('<div class="dash-empty">No pending requests.</div>');
                }
            }
        });
    }

    loadBooked();
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>