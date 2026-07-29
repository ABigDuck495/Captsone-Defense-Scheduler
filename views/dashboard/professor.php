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
// ... rest of professor.php (HTML + JS) unchanged
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
            <h2 class="h5 mb-0">Availability for <?= $monthName ?></h2>
            <div>
                <button class="btn btn-sm btn-outline-secondary" id="prev-month">&lt;</button>
                <button class="btn btn-sm btn-outline-secondary" id="next-month">&gt;</button>
                <button class="btn btn-sm btn-outline-secondary" id="today-month">Today</button>
            </div>
        </div>
        <div id="calendar-container"></div>
        <p class="text-body-secondary small mt-2">Click a cell to toggle. If adding, you'll be asked to repeat weekly.</p>
    </div>

    <div class="tab-pane d-none" id="tab-requests">
        <span class="eyebrow">Defense panel requests</span>
        <h2 class="h5 mb-3">Requests needing your response</h2>
        <div id="requests-list"><div class="dash-empty">No pending requests.</div></div>
    </div>
</main>

<footer class="tt-footer" id="site-footer"></footer>
<script src="<?= $__tssBaseUrl ?>public/vendor/jquery/jquery.min.js"></script>
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

    function loadCalendar() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_availability.php',
            data: { professor_id: professorId, year: currentYear, month: currentMonth },
            dataType: 'json',
            success: function(res) {
                if (res.success) renderCalendar(res.slots);
                else $('#calendar-container').html('<p class="text-danger">Error loading availability.</p>');
            }
        });
    }

    function renderCalendar(slotsData) {
        const start = new window.Date(currentYear, currentMonth-1, 1);
        const end = new window.Date(currentYear, currentMonth, 0);
        const days = [];
        for (let d = new window.Date(start); d <= end; d.setDate(d.getDate()+1)) days.push(new window.Date(d));
        const timeSlots = [];
        for (let h=7; h<=17; h++) timeSlots.push(String(h).padStart(2,'0')+':00:00');

        let html = '<table class="calendar-table table table-bordered"><thead><tr><th>Time</th>';
        days.forEach(day => html += '<th>' + day.toLocaleDateString('en-US',{weekday:'short',day:'numeric'}) + '</th>');
        html += '</tr></thead><tbody>';
        timeSlots.forEach(time => {
            html += '<tr><td class="time-label">' + time.substr(0,5) + '</td>';
            days.forEach(day => {
                const dateStr = day.toISOString().slice(0,10);
                const key = dateStr + ' ' + time;
                const cls = slotsData[key] ? 'available' : 'unavailable';
                html += `<td class="slot ${cls}" data-date="${dateStr}" data-time="${time}"></td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        $('#calendar-container').html(html);

        // Click handler
        $('#calendar-container .slot').on('click', function() {
            const $this = $(this);
            const date = $this.data('date');
            const time = $this.data('time');
            const isAvailable = $this.hasClass('available');
            const action = isAvailable ? 'remove' : 'add';
            let repeat = false;
            if (!isAvailable) {
                repeat = confirm('Repeat this slot every week for the rest of the month?');
            }
            $.ajax({
                url: baseUrl + 'views/ajax/toggle_availability.php',
                method: 'POST',
                data: JSON.stringify({
                    professor_id: professorId,
                    date: date,
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

    $('#prev-month').click(function() {
        if (currentMonth === 1) { currentMonth = 12; currentYear--; } else currentMonth--;
        loadCalendar();
    });
    $('#next-month').click(function() {
        if (currentMonth === 12) { currentMonth = 1; currentYear++; } else currentMonth++;
        loadCalendar();
    });
    $('#today-month').click(function() {
        const today = new window.Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth()+1;
        loadCalendar();
    });

    function loadBooked() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_booked.php',
            data: { professor_id: professorId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.booked.length) {
                    let html = '<ul class="list-unstyled">';
                    res.booked.forEach(item => {
                        html += `<li><strong>${item.defense_date}</strong> ${item.start_time} - ${item.end_time} at ${item.venue || 'TBD'}</li>`;
                    });
                    html += '</ul>';
                    $('#booked-list').html(html);
                } else {
                    $('#booked-list').html('<div class="dash-empty">No booked defenses yet.</div>');
                }
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
    loadCalendar();
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>