<?php
require __DIR__ . '/../layout/header.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: ' . TSS_BASE_URL . 'views/auth/login.php');
    exit;
}

$studentId = $_SESSION['user_id'];

$groupId = $_SESSION['group_id'] ?? null;

if (!$groupId) {
    // Query database for existing group membership
    $stmt = $pdo->prepare("SELECT group_id FROM group_members WHERE student_id = ?");
    $stmt->execute([$studentId]);
    $row = $stmt->fetch();
    if ($row) {
        $groupId = (int)$row['group_id'];
        $_SESSION['group_id'] = $groupId;
    } else {
        // No group exists, create one
        $stmt = $pdo->prepare("INSERT INTO thesis_groups (thesis_title, status, created_by) VALUES (?, 'forming', ?)");
        $stmt->execute(['My Group', $studentId]);
        $groupId = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, student_id, is_leader) VALUES (?, ?, 1)");
        $stmt->execute([$groupId, $studentId]);
        
        $_SESSION['group_id'] = $groupId;
    }
}

$year = (int)($_GET['year'] ?? date('Y'));
$month = (int)($_GET['month'] ?? date('m'));
$monthName = date('F Y', strtotime("$year-$month-01"));

// Get student name
$stmt = $pdo->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();
$studentName = $student['full_name'] ?? 'Student';
?>
<link rel="stylesheet" href="../../public/assets/css/custom.css">
<script>
    const studentId = <?= json_encode($studentId) ?>;
    const groupId = <?= json_encode($groupId) ?>;
    const baseUrl = <?= json_encode($__tssBaseUrl) ?>;
    let currentYear = <?= $year ?>;
    let currentMonth = <?= $month ?>;
    let selectedProfessors = [];
    let panelAssigned = false;
</script>

<header class="dash-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <h1 class="mb-0">Thesis Scheduling System</h1>
            <span class="badge bg-forest">Student</span>
        </div>
        <a href="<?= $__tssBaseUrl ?>views/auth/logout.php" class="btn btn-sm btn-outline-light">Log out</a>
    </div>
</header>

<main class="container py-4">
    <p class="text-body-secondary small mb-3">Welcome, <?= htmlspecialchars($studentName) ?></p>
    <div id="form-alert"></div>

    <div class="card p-3 p-md-4 mb-4">
        <span class="eyebrow">Upcoming</span>
        <h2 class="h5 mb-3">My Booked Defenses</h2>
        <div id="booked-list"><div class="dash-empty">Loading...</div></div>
    </div>

    <div class="card p-3 p-md-4 mb-4">
        <span class="eyebrow">Upcoming</span>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">Calendar</h2>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary" id="mini-cal-prev" type="button">&lt;</button>
                <span id="mini-cal-label" class="fw-semibold small"></span>
                <button class="btn btn-sm btn-outline-secondary" id="mini-cal-next" type="button">&gt;</button>
                <button class="btn btn-sm btn-outline-secondary" id="mini-cal-today" type="button">Today</button>
            </div>
        </div>
        <div class="mini-calendar-grid mini-calendar-head">
            <div class="mini-cal-dow">Su</div><div class="mini-cal-dow">Mo</div><div class="mini-cal-dow">Tu</div>
            <div class="mini-cal-dow">We</div><div class="mini-cal-dow">Th</div><div class="mini-cal-dow">Fr</div>
            <div class="mini-cal-dow">Sa</div>
        </div>
        <div class="mini-calendar-grid" id="mini-calendar"><div class="dash-empty">Loading...</div></div>
        <div class="d-flex gap-3 small text-body-secondary mt-2">
            <span><span class="legend-dot" style="background-color: var(--forest-mid);"></span>Open slot (checked professors)</span>
            <span><span class="legend-dot bg-warning"></span>Pending request</span>
            <span><span class="legend-dot bg-success"></span>Booked defense</span>
        </div>
    </div>
    <style>
        .mini-calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
        .mini-calendar-head { margin-bottom: 4px; }
        .mini-cal-dow { text-align: center; font-size: .78rem; font-weight: 600; color: var(--bs-secondary-color, #6c757d); }
        .mini-cal-cell {
            aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
            border-radius: 6px; font-size: .82rem; background-color: var(--bs-tertiary-bg, #f8f9fa);
        }
        .mini-cal-empty { background-color: transparent; visibility: hidden; }
        .mini-cal-today { outline: 2px solid var(--bs-primary, #0d6efd); outline-offset: -2px; font-weight: 700; }
        .mini-cal-booked { background-color: var(--bs-success, #198754); color: #fff; font-weight: 600; }
        .mini-cal-pending { background-color: var(--bs-warning, #ffc107); color: #000; font-weight: 600; }
        .mini-cal-open { background-color: var(--forest-mid); color: #fff; font-weight: 600; }
        .legend-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
        .legend-dot.bg-success { background-color: var(--bs-success, #198754); }
        .legend-dot.bg-warning { background-color: var(--bs-warning, #ffc107); }
    </style>

    <ul class="nav nav-tabs mb-4" id="student-nav">
        <li class="nav-item"><a class="nav-link active" href="#" data-target="#tab-book">Book Schedule</a></li>
        <li class="nav-item"><a class="nav-link" href="#" data-target="#tab-requests">My Requests</a></li>
    </ul>

    <div class="tab-pane" id="tab-book">
        1:53 PM
<div class="tab-pane" id="tab-book">
        <div class="card p-3 p-md-4 mb-4">
            <span class="eyebrow">Step 1</span>
            <h2 class="h5 mb-2">Select your panel</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="select-adviser">Adviser</label>
                    <select class="form-select" id="select-adviser">
                        <option value="">Choose adviser...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="select-chair">Chair</label>
                    <select class="form-select" id="select-chair">
                        <option value="">Choose chair...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="select-critic">Critic</label>
                    <select class="form-select" id="select-critic">
                        <option value="">Choose critic...</option>
                    </select>
                </div>
            </div>
            <div class="prof-counter mt-2" id="panel-status">0 / 3 selected</div>
            <button class="btn btn-primary btn-sm mt-2" id="assign-panel-btn" disabled>Set Panel</button>
        </div>

        <div class="card p-3 p-md-4">
            <span class="eyebrow">Step 2</span>
            <h2 class="h5 mb-3">Available slots (based on selected professors)</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="prev-month">&lt;</button>
                    <button class="btn btn-sm btn-outline-secondary" id="next-month">&gt;</button>
                    <button class="btn btn-sm btn-outline-secondary" id="today-month">Today</button>
                </div>
                <span id="calendar-month-label"><?= $monthName ?></span>
            </div>
            <div class="avail-cal-head">
                <div class="avail-cal-dow">Su</div><div class="avail-cal-dow">Mo</div><div class="avail-cal-dow">Tu</div>
                <div class="avail-cal-dow">We</div><div class="avail-cal-dow">Th</div><div class="avail-cal-dow">Fr</div>
                <div class="avail-cal-dow">Sa</div>
            </div>
            <div id="calendar-container"><p class="text-muted">Select professors first.</p></div>
            <div class="cal-legend mt-2">
                <span><span class="slot-dot open"></span>All selected professors free</span>
            </div>
            <div id="day-detail-container"></div>
            <p class="text-body-secondary small mt-2">Check professors above to preview when they're free together. Click a day with an open dot to see the hours. Select exactly 3 and click "Set Panel" before you can submit a request.</p>
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
                .slot-row .panel-names { font-size: .78rem; color: var(--ink-soft); display: block; margin-top: 2px; }
            </style>
        </div>
    </div>

    <div class="tab-pane d-none" id="tab-requests">
        <h2 class="h5 mb-3">My Requests</h2>
        <div id="requests-list"><div class="dash-empty">No requests made yet.</div></div>
    </div>
</main>

<footer class="tt-footer" id="site-footer"></footer>
<script src="<?= $__tssBaseUrl ?>public/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= $__tssBaseUrl ?>public/assets/js/app.js"></script>
<script>
$(function() {
    $('#student-nav a').on('click', function(e) {
        e.preventDefault();
        const target = $(this).data('target');
        $('#student-nav a').removeClass('active');
        $(this).addClass('active');
        $('.tab-pane').addClass('d-none');
        $(target).removeClass('d-none');
        if (target === '#tab-requests') loadRequests();
    });

    let profNameMap = {}; 
    const panelSelectionStorageKey = 'student_panel_selection';

    function savePanelSelection() {
        const values = ['#select-adviser', '#select-chair', '#select-critic']
            .map(selectId => $(selectId).val());
        try {
            window.localStorage.setItem(panelSelectionStorageKey, JSON.stringify(values));
        } catch (e) {
            console.warn('Unable to save panel selection', e);
        }
    }

    function restorePanelSelection() {
        try {
            const stored = window.localStorage.getItem(panelSelectionStorageKey);
            if (!stored) return false;
            const values = JSON.parse(stored);
            if (!Array.isArray(values)) return false;

            ['#select-adviser', '#select-chair', '#select-critic'].forEach((selectId, index) => {
                const value = values[index];
                $(selectId).val(value !== undefined && value !== null && value !== '' ? value : '');
            });
            return true;
        } catch (e) {
            console.warn('Unable to restore panel selection', e);
            return false;
        }
    }

    function loadProfessors() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_professors.php',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    const professors = res.professors || [];
                    professors.forEach(prof => {
                        profNameMap[prof.user_id] = prof.full_name;
                    });

                    function populateSelect(selectId, placeholder) {
                        const $select = $(selectId);
                        $select.empty();
                        $select.append(`<option value="">${placeholder}</option>`);
                        professors.forEach(prof => {
                            $select.append(`<option value="${prof.user_id}">${prof.full_name}</option>`);
                        });
                    }

                    populateSelect('#select-adviser', 'Choose adviser...');
                    populateSelect('#select-chair', 'Choose chair...');
                    populateSelect('#select-critic', 'Choose critic...');

                    const restored = restorePanelSelection();

                    function syncPanelSelection() {
                        selectedProfessors = ['#select-adviser', '#select-chair', '#select-critic']
                            .map(selectId => $(selectId).val())
                            .filter(value => value !== '' && value !== null)
                            .map(value => parseInt(value, 10));

                        $('#panel-status').text(selectedProfessors.length + ' / 3 selected');
                        $('#assign-panel-btn').prop('disabled', selectedProfessors.length !== 3).text('Set Panel');

                        if (panelAssigned) {
                            panelAssigned = false;
                            selectedBookDay = null;
                            $('#day-detail-container').empty();
                        }

                        savePanelSelection();
                        loadCalendar();
                        loadMiniCalendar();
                    }

                    $('#select-adviser, #select-chair, #select-critic')
                        .off('change.panelSelection')
                        .on('change.panelSelection', syncPanelSelection);

                    if (!restored) {
                        savePanelSelection();
                    }
                    syncPanelSelection();
                }
            }
        });
    }

    function assignPanel() {
        if (selectedProfessors.length !== 3) return;
        $('#assign-panel-btn').prop('disabled', true).text('Setting panel…');

        $.ajax({
            url: baseUrl + 'views/ajax/assign_panel.php',
            method: 'POST',
            data: JSON.stringify({
                group_id: groupId,
                adviser_id: selectedProfessors[0],
                chair_id: selectedProfessors[1],
                critic_id: selectedProfessors[2]
            }),
            contentType: 'application/json'
        }).done(function(res) {
            if (res.success) {
                panelAssigned = true;
                $('#assign-panel-btn').text('Panel Set ✓');
                loadCalendar();
                loadBooked();
            } else {
                alert('Error: ' + (res.message || 'Failed to assign panel.'));
                $('#assign-panel-btn').prop('disabled', false).text('Set Panel');
            }
        }).fail(function() {
            alert('Error assigning panel.');
            $('#assign-panel-btn').prop('disabled', false).text('Set Panel');
        });
    }

    $('#assign-panel-btn').on('click', function() {
        if (selectedProfessors.length === 3 && !panelAssigned) assignPanel();
    });

    let overlapMonthData = {};  
    let panelNames = [];        
    let selectedBookDay = null;

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

    function slotsForBookDate(dateStr) {
        return hourSlots().map(time => ({
            time,
            available: overlapMonthData[dateStr + ' ' + time] === 'available'
        }));
    }

    function loadCalendar() {
        if (panelAssigned) {
            $.ajax({
                url: baseUrl + 'views/ajax/get_overlaps.php',
                data: { group_id: groupId, year: currentYear, month: currentMonth },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        overlapMonthData = res.slots;
                        panelNames = res.panel || [];
                        renderStudentCalendar();
                    } else {
                        $('#calendar-container').html('<p class="text-danger">Error loading availability.</p>');
                    }
                }
            });
            return;
        }

        if (!selectedProfessors.length) {
            overlapMonthData = {};
            panelNames = [];
            $('#calendar-container').html('<p class="text-muted">Check one or more professors above to preview their availability.</p>');
            $('#day-detail-container').empty();
            return;
        }

        loadPreview();
    }

    function fetchAvailabilityIntersection(profIds, year, month) {
        if (!profIds.length) {
            return $.Deferred().resolve({}).promise();
        }
        const requests = profIds.map(profId =>
            $.ajax({
                url: baseUrl + 'views/ajax/get_availability.php',
                data: { professor_id: profId, year: year, month: month },
                dataType: 'json'
            }).then(res => (res && res.success) ? res.slots : {})
        );
        return $.when.apply($, requests).then(function() {
            const slotMaps = Array.prototype.slice.call(arguments);
            const merged = {};
            const first = slotMaps[0] || {};
            Object.keys(first).forEach(key => {
                if (slotMaps.every(map => map && map[key] === 'available')) {
                    merged[key] = 'available';
                }
            });
            return merged;
        });
    }

    function loadPreview() {
        fetchAvailabilityIntersection(selectedProfessors, currentYear, currentMonth)
            .done(function(merged) {
                overlapMonthData = merged;
                panelNames = selectedProfessors.map(id => ({
                    full_name: profNameMap[id] || ('Professor #' + id)
                }));
                renderStudentCalendar();
            })
            .fail(function() {
                $('#calendar-container').html('<p class="text-danger">Error loading availability preview.</p>');
            });
    }

    function renderStudentCalendar() {
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
            const openCount = slotsForBookDate(dateStr).filter(s => s.available).length;

            let cls = 'cal-cell';
            if (openCount > 0) cls += ' has-slots';
            if (dateStr === todayStr) cls += ' is-today';
            if (dateStr === selectedBookDay) cls += ' is-selected';

            let dots = '';
            if (openCount > 0) dots += `<span class="slot-dot open" title="${openCount} open slot(s)"></span>`;

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
            selectedBookDay = $(this).data('date');
            renderStudentCalendar();
            renderBookDayDetail(selectedBookDay);
        });

        if (selectedBookDay) renderBookDayDetail(selectedBookDay);
    }

    function renderBookDayDetail(dateStr) {
        const slots = slotsForBookDate(dateStr).filter(s => s.available);
        const label = new window.Date(dateStr + 'T00:00:00')
            .toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
        const namesLine = panelNames.map(p => p.role ? `${p.full_name} (${p.role})` : p.full_name).join(', ');

        let rows = '';
        if (!slots.length) {
            rows = '<p class="text-muted small mb-0">No hour on this day works for everyone currently selected.</p>';
        } else {
            slots.forEach(s => {
                const [h, m] = s.time.split(':').map(Number);
                const endTime = String(h + 1).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':00';
                const requestBtn = panelAssigned
                    ? `<button class="btn btn-sm btn-success request-slot" data-time="${s.time}" data-end="${endTime}">Request</button>`
                    : `<button class="btn btn-sm btn-outline-secondary" disabled title="Click &quot;Set Panel&quot; first">Request</button>`;
                rows += `<div class="slot-row">
                            <span>
                                ${s.time.substr(0,5)} - ${endTime.substr(0,5)}
                                <span class="panel-names">Free: ${namesLine}</span>
                            </span>
                            ${requestBtn}
                         </div>`;
            });
        }
        $('#day-detail-container').html(`<div class="day-detail"><div class="day-detail-title">${label}</div>${rows}</div>`);

        if (!panelAssigned) return;

        $('.request-slot').on('click', function() {
            const time = $(this).data('time');
            const endTime = $(this).data('end');
            if (!confirm(`Request defense on ${dateStr} at ${time.substr(0,5)} - ${endTime.substr(0,5)}?`)) return;
            $.ajax({
                url: baseUrl + 'views/ajax/request_schedule.php',
                method: 'POST',
                data: JSON.stringify({ group_id: groupId, date: dateStr, start_time: time, end_time: endTime }),
                contentType: 'application/json',
                success: function(res) {
                    if (res.success) {
                        alert('Request submitted!');
                        loadRequests();
                        loadMiniCalendar();
                    } else alert('Error: ' + (res.message || 'Failed.'));
                },
                error: function() { alert('Server error.'); }
            });
        });
    }

    $('#prev-month').click(function() {
        if (currentMonth === 1) { currentMonth = 12; currentYear--; } else currentMonth--;
        selectedBookDay = null;
        $('#day-detail-container').empty();
        updateMonthLabel();
        loadCalendar();
    });
    $('#next-month').click(function() {
        if (currentMonth === 12) { currentMonth = 1; currentYear++; } else currentMonth++;
        selectedBookDay = null;
        $('#day-detail-container').empty();
        updateMonthLabel();
        loadCalendar();
    });
    $('#today-month').click(function() {
        const today = new window.Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth()+1;
        selectedBookDay = null;
        $('#day-detail-container').empty();
        updateMonthLabel();
        loadCalendar();
    });
    function updateMonthLabel() {
        const date = new window.Date(currentYear, currentMonth-1, 1);
        $('#calendar-month-label').text(date.toLocaleString('en-US', { month: 'long', year: 'numeric' }));
    }

    function loadBooked() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_booked.php',
            data: { group_id: groupId },
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
            data: { group_id: groupId },
            dataType: 'json',
            success: function(res) {
                if (res.success && res.requests.length) {
                    let html = '';
                    res.requests.forEach(req => {
                        let statuses = '';
                        req.approvals.forEach(app => {
                            statuses += `<span class="badge ${app.status==='approved'?'bg-success':app.status==='rejected'?'bg-danger':'bg-warning'}">${app.professor_name}: ${app.status}</span> `;
                        });
                        html += `<div class="card mb-2 p-3">
                            <div><strong>${req.defense_date}</strong> ${req.start_time}-${req.end_time}</div>
                            <div>Status: ${req.status}</div>
                            <div>${statuses}</div>
                        </div>`;
                    });
                    $('#requests-list').html(html);
                } else {
                    $('#requests-list').html('<div class="dash-empty">No requests made yet.</div>');
                }
            }
        });
    }

    let miniCalYear, miniCalMonth;

    function initMiniCal() {
        const today = new window.Date();
        miniCalYear = today.getFullYear();
        miniCalMonth = today.getMonth() + 1;
        loadMiniCalendar();
    }

    function loadMiniCalendar() {
        let bookedByDate = {};
        let pendingByDate = {};
        let openDates = new Set();
        let pending = 3;
        function settle() {
            pending--;
            if (pending === 0) {
                const dateStatus = {};
                openDates.forEach(d => { dateStatus[d] = { type: 'open', label: 'Open slot' }; });
                Object.keys(pendingByDate).forEach(d => { dateStatus[d] = pendingByDate[d]; });
                Object.keys(bookedByDate).forEach(d => { dateStatus[d] = bookedByDate[d]; });
                renderMiniCalendar(dateStatus);
            }
        }

        $.ajax({ url: baseUrl + 'views/ajax/get_requests.php', data: { group_id: groupId }, dataType: 'json' })
            .done(function(res) {
                if (res && res.success) {
                    (res.requests || []).forEach(r => {
                        if (r.status === 'pending') {
                            pendingByDate[r.defense_date] = { type: 'pending', label: r.start_time + '–' + r.end_time + ' (pending)' };
                        }
                    });
                } else {
                    console.warn('get_requests.php responded without success:', res);
                }
            })
            .fail(function(xhr, status) {
                console.error('get_requests.php request failed:', status, xhr.status, xhr.responseText);
            })
            .always(settle);

        $.ajax({ url: baseUrl + 'views/ajax/get_booked.php', data: { group_id: groupId }, dataType: 'json' })
            .done(function(res) {
                if (res && res.success) {
                    (res.booked || []).forEach(b => {
                        if (b.status === 'cancelled' || b.status === 'rescheduled') return;
                        bookedByDate[b.defense_date] = { type: 'booked', label: b.start_time + '–' + b.end_time + (b.venue ? ' @ ' + b.venue : '') };
                    });
                } else {
                    console.warn('get_booked.php responded without success:', res);
                }
            })
            .fail(function(xhr, status) {
                console.error('get_booked.php request failed:', status, xhr.status, xhr.responseText);
            })
            .always(settle);

        fetchAvailabilityIntersection(selectedProfessors, miniCalYear, miniCalMonth)
            .done(function(slots) {
                Object.keys(slots).forEach(key => openDates.add(key.split(' ')[0]));
            })
            .fail(function() {
                console.error('Failed to load availability preview for mini calendar');
            })
            .always(settle);
    }

    function renderMiniCalendar(dateStatus) {
        const first = new window.Date(miniCalYear, miniCalMonth - 1, 1);
        const startWeekday = first.getDay();
        const daysInMonth = new window.Date(miniCalYear, miniCalMonth, 0).getDate();
        const today = new window.Date();
        const todayStr = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');

        $('#mini-cal-label').text(first.toLocaleString('en-US', { month: 'long', year: 'numeric' }));

        let html = '';
        for (let i = 0; i < startWeekday; i++) {
            html += '<div class="mini-cal-cell mini-cal-empty"></div>';
        }
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = miniCalYear + '-' + String(miniCalMonth).padStart(2,'0') + '-' + String(day).padStart(2,'0');
            const entry = dateStatus[dateStr];
            let cls = 'mini-cal-cell';
            let title = '';
            if (dateStr === todayStr) cls += ' mini-cal-today';
            if (entry && entry.type === 'booked') { cls += ' mini-cal-booked'; title = entry.label; }
            else if (entry && entry.type === 'pending') { cls += ' mini-cal-pending'; title = entry.label; }
            else if (entry && entry.type === 'open') { cls += ' mini-cal-open'; title = entry.label; }
            html += `<div class="${cls}"${title ? ` title="${title}"` : ''}>${day}</div>`;
        }
        const totalCells = startWeekday + daysInMonth;
        const trailing = (7 - (totalCells % 7)) % 7;
        for (let i = 0; i < trailing; i++) {
            html += '<div class="mini-cal-cell mini-cal-empty"></div>';
        }

        $('#mini-calendar').html(html);
    }

    $('#mini-cal-prev').on('click', function() {
        if (miniCalMonth === 1) { miniCalMonth = 12; miniCalYear--; } else miniCalMonth--;
        loadMiniCalendar();
    });
    $('#mini-cal-next').on('click', function() {
        if (miniCalMonth === 12) { miniCalMonth = 1; miniCalYear++; } else miniCalMonth++;
        loadMiniCalendar();
    });
    $('#mini-cal-today').on('click', function() {
        const t = new window.Date();
        miniCalYear = t.getFullYear();
        miniCalMonth = t.getMonth() + 1;
        loadMiniCalendar();
    });

    loadProfessors();
    loadBooked();
    loadCalendar();
    initMiniCal();
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>