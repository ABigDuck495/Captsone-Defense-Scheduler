<?php
require_once __DIR__ . '/../layout/header.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: ' . TSS_BASE_URL . 'views/auth/login.php');
    exit;
}

$studentId = $_SESSION['user_id'];

// Check if student already has a group (from session or database)
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

    <ul class="nav nav-tabs mb-4" id="student-nav">
        <li class="nav-item"><a class="nav-link active" href="#" data-target="#tab-book">Book Schedule</a></li>
        <li class="nav-item"><a class="nav-link" href="#" data-target="#tab-requests">My Requests</a></li>
    </ul>

    <div class="tab-pane" id="tab-book">
        <div class="card p-3 p-md-4 mb-4">
            <span class="eyebrow">Step 1</span>
            <h2 class="h5 mb-2">Select exactly 3 advisers</h2>
            <div class="prof-picker" id="prof-picker"></div>
            <div class="prof-counter" id="prof-counter">0 / 3 selected</div>
            <button class="btn btn-primary btn-sm mt-2" id="assign-panel-btn" disabled>Set Panel</button>
        </div>

        <div class="card p-3 p-md-4">
            <span class="eyebrow">Step 2</span>
            <h2 class="h5 mb-3">Available slots (all selected professors available)</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="prev-month">&lt;</button>
                    <button class="btn btn-sm btn-outline-secondary" id="next-month">&gt;</button>
                    <button class="btn btn-sm btn-outline-secondary" id="today-month">Today</button>
                </div>
                <span id="calendar-month-label"><?= $monthName ?></span>
            </div>
            <div id="calendar-container"><p class="text-muted">Select professors first.</p></div>
            <p class="text-body-secondary small mt-2">Click a green (available) slot to request a defense.</p>
        </div>
    </div>

    <div class="tab-pane d-none" id="tab-requests">
        <h2 class="h5 mb-3">My Requests</h2>
        <div id="requests-list"><div class="dash-empty">No requests made yet.</div></div>
    </div>
</main>

<footer class="tt-footer" id="site-footer"></footer>
<script src="<?= $__tssBaseUrl ?>public/vendor/jquery/jquery.min.js"></script>
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

    // Load professors
    function loadProfessors() {
        $.ajax({
            url: baseUrl + 'views/ajax/get_professors.php',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    let html = '';
                    res.professors.forEach(prof => {
                        html += `<div class="prof-option"><input type="checkbox" class="prof-checkbox" value="${prof.user_id}" id="prof_${prof.user_id}"><label for="prof_${prof.user_id}">${prof.full_name}</label></div>`;
                    });
                    $('#prof-picker').html(html);
                    $('.prof-checkbox').on('change', function() {
                        const id = parseInt($(this).val());
                        if ($(this).is(':checked')) {
                            if (selectedProfessors.length >= 3) {
                                alert('You can select at most 3 professors.');
                                $(this).prop('checked', false);
                                return;
                            }
                            selectedProfessors.push(id);
                        } else {
                            selectedProfessors = selectedProfessors.filter(p => p !== id);
                        }
                        $('#prof-counter').text(selectedProfessors.length + ' / 3 selected');
                        $('#assign-panel-btn').prop('disabled', selectedProfessors.length !== 3);
                        if (selectedProfessors.length === 3) {
                            assignPanel();
                        }
                    });
                }
            }
        });
    }

    function assignPanel() {
        if (selectedProfessors.length !== 3) return;
        const roles = ['adviser', 'chair', 'critic'];
        let requests = selectedProfessors.map((profId, index) => {
            return $.ajax({
                url: baseUrl + 'views/ajax/assign_panel.php',
                method: 'POST',
                data: JSON.stringify({ group_id: groupId, professor_id: profId, role: roles[index] }),
                contentType: 'application/json'
            });
        });
        $.when(...requests).done(function() {
            panelAssigned = true;
            loadCalendar();
            loadBooked();
        }).fail(function() {
            alert('Error assigning panel.');
        });
    }

    $('#assign-panel-btn').on('click', function() {
        if (selectedProfessors.length === 3) assignPanel();
    });

    function loadCalendar() {
        if (!panelAssigned) {
            $('#calendar-container').html('<p class="text-muted">Please select exactly 3 professors to see available slots.</p>');
            return;
        }
        $.ajax({
            url: baseUrl + 'views/ajax/get_overlaps.php',
            data: { group_id: groupId, year: currentYear, month: currentMonth },
            dataType: 'json',
            success: function(res) {
                if (res.success) renderStudentCalendar(res.slots);
                else $('#calendar-container').html('<p class="text-danger">Error loading availability.</p>');
            }
        });
    }

    function renderStudentCalendar(slotsData) {
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
                const available = slotsData[key] === 'available';
                const cls = available ? 'available clickable' : 'unavailable';
                html += `<td class="slot ${cls}" data-date="${dateStr}" data-time="${time}"></td>`;
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        $('#calendar-container').html(html);

        $('.slot.clickable').on('click', function() {
            const date = $(this).data('date');
            const time = $(this).data('time');
            const [h,m] = time.split(':').map(Number);
            const endTime = String(h+1).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':00';
            if (!confirm(`Request defense on ${date} at ${time.substr(0,5)} - ${endTime.substr(0,5)}?`)) return;
            $.ajax({
                url: baseUrl + 'views/ajax/request_schedule.php',
                method: 'POST',
                data: JSON.stringify({ group_id: groupId, date: date, start_time: time, end_time: endTime }),
                contentType: 'application/json',
                success: function(res) {
                    if (res.success) {
                        alert('Request submitted!');
                        loadRequests();
                    } else alert('Error: ' + (res.message || 'Failed.'));
                },
                error: function() { alert('Server error.'); }
            });
        });
    }

    $('#prev-month').click(function() {
        if (currentMonth === 1) { currentMonth = 12; currentYear--; } else currentMonth--;
        updateMonthLabel();
        loadCalendar();
    });
    $('#next-month').click(function() {
        if (currentMonth === 12) { currentMonth = 1; currentYear++; } else currentMonth++;
        updateMonthLabel();
        loadCalendar();
    });
    $('#today-month').click(function() {
        const today = new window.Date();
        currentYear = today.getFullYear();
        currentMonth = today.getMonth()+1;
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

    loadProfessors();
    loadBooked();
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>