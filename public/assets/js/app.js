(function () {
  'use strict';

  // Mock data — replace with AJAX calls to backend
  var today = new Date();

  function iso(d) { return d.toISOString().slice(0, 10); }
  function pad2(n) { return (n < 10 ? '0' : '') + n; }

  var MOCK = {
    currentUser: null,

    users: [
      { username: 'prof1',    password: 'password', role: 'professor', id: 1, name: 'Dr. Alice Cruz',   redirect: '../dashboard/professor.php' },
      { username: 'prof2',    password: 'password', role: 'professor', id: 2, name: 'Dr. Ben Santos',    redirect: '../dashboard/professor.php' },
      { username: 'prof3',    password: 'password', role: 'professor', id: 3, name: 'Dr. Carla Reyes',   redirect: '../dashboard/professor.php' },
      { username: 'prof4',    password: 'password', role: 'professor', id: 4, name: 'Dr. Diego Torres',  redirect: '../dashboard/professor.php' },
      { username: 'student1', password: 'password', role: 'student',   id: 101, name: 'Group: Neural Nets Thesis', redirect: '../dashboard/student.php' }
    ],

    professors: [
      { id: 1, name: 'Dr. Alice Cruz',  dept: 'Computer Science' },
      { id: 2, name: 'Dr. Ben Santos',  dept: 'Information Technology' },
      { id: 3, name: 'Dr. Carla Reyes', dept: 'Computer Engineering' },
      { id: 4, name: 'Dr. Diego Torres', dept: 'Computer Science' }
    ],

    availability: [],
    requests: [],
    // Backend: load from defense_schedules table
    booked: [],
    nextAvailId: 1000,
    nextRequestId: 5000
  };

  // Seed sample data (remove when backend is ready)
  (function seed() {
    for (var p = 0; p < MOCK.professors.length; p++) {
      var profId = MOCK.professors[p].id;
      for (var day = 1; day <= 20; day += (2 + (profId % 3))) {
        var d = new Date(today.getFullYear(), today.getMonth(), today.getDate() + day);
        var hour = 9 + ((profId + day) % 6);
        MOCK.availability.push({
          id: MOCK.nextAvailId++,
          professor_id: profId,
          date: iso(d),
          start: pad2(hour) + ':00',
          end: pad2(hour + 1) + ':00',
          status: (day % 7 === 0) ? 'blocked' : 'available'
        });
      }
    }
    var sampleDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 6);
    MOCK.requests.push({
      id: MOCK.nextRequestId++,
      group_id: 'group1',
      thesis_title: 'Neural Nets Thesis',
      panel: [1, 2, 3],
      date: iso(sampleDate),
      start: '10:00',
      end: '11:00',
      venue: 'Room 301',
      status: 'pending',
      approvals: {
        1: { status: 'pending', remarks: null },
        2: { status: 'approved', remarks: null },
        3: { status: 'pending', remarks: null }
      }
    });

    // Sample booked defense (all 3 accepted)
    // Backend: load from defense_schedules
    MOCK.booked.push({
      id: 1,
      group_id: 'group1',
      thesis_title: 'Neural Nets Thesis',
      panel: [1, 2, 3],
      date: iso(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 14)),
      start: '09:00',
      end: '10:00',
      venue: 'Room 205',
      status: 'scheduled'
    });
  })();

  // Helpers
  var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  function showAlert(msg, type) {
    type = type || 'success';
    $('#form-alert').html(
      '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        msg +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
      '</div>'
    );
  }

  function badgeClassFor(status) {
    if (status === 'approved') return 'status-approved';
    if (status === 'rejected') return 'status-rejected';
    return 'status-pending';
  }

  function initTabs(navId) {
    $(navId + ' .nav-link').on('click', function (e) {
      e.preventDefault();
      var target = $(this).data('target');
      $(navId + ' .nav-link').removeClass('active');
      $(this).addClass('active');
      $('.tab-pane').addClass('d-none');
      $(target).removeClass('d-none');
    });
  }

  function buildMonthGrid(year, month) {
    var firstDay = new Date(year, month, 1);
    var startOffset = firstDay.getDay();
    var daysInMonth = new Date(year, month + 1, 0).getDate();
    var cells = [];
    for (var i = 0; i < startOffset; i++) cells.push(null);
    for (var d = 1; d <= daysInMonth; d++) cells.push(new Date(year, month, d));
    while (cells.length % 7 !== 0) cells.push(null);
    return cells;
  }

  // Login
  function initLoginPage() {
    if (!$('#login-form').length) return;

    $('#login-form').on('submit', function (e) {
      e.preventDefault();
      var email = $('#email').val().trim();
      var password = $('#password').val();
      var $btn = $('#login-btn');

      $btn.prop('disabled', true).text('Logging in...');

      $.ajax({
        url: '../../public/ajax/login.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ email: email, password: password }),
        dataType: 'json'
      }).done(function (response) {
        $btn.prop('disabled', false).text('Log in');

        if (!response.success) {
          showAlert(response.message || 'Invalid credentials.', 'danger');
          return;
        }

        sessionStorage.setItem('tss_user', JSON.stringify({
          role: response.role,
          email: email
        }));
        window.location.href = response.redirect || (response.role === 'professor' ? '../dashboard/professor.php' : '../dashboard/student.php');
      }).fail(function (xhr) {
        $btn.prop('disabled', false).text('Log in');
        var message = 'Invalid credentials.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        showAlert(message, 'danger');
      });
    });
  }

  // Shared calendar
  function initCalendar(opts) {
    if (!$('#cal-grid').length) return null;

    var calDate = new Date(today.getFullYear(), today.getMonth(), 1);
    var selectedDate = null;

    function render() {
      $('#cal-month-label').text(MONTHS[calDate.getMonth()] + ' ' + calDate.getFullYear());
      var cells = buildMonthGrid(calDate.getFullYear(), calDate.getMonth());
      var $body = $('#cal-grid').empty();

      for (var w = 0; w < cells.length / 7; w++) {
        var $row = $('<tr></tr>');
        for (var c = 0; c < 7; c++) {
          var d = cells[w * 7 + c];
          if (!d) { $row.append('<td class="cal-cell cal-empty"></td>'); continue; }

          var dIso = iso(d);
          var $td = $('<td class="cal-cell"></td>').attr('data-date', dIso);
          var clickable = opts.dayHasContent(dIso);

          if (clickable) $td.addClass('has-slots');
          var extra = opts.cellExtraClass ? opts.cellExtraClass(dIso) : '';
          if (extra) $td.addClass(extra);
          if (dIso === iso(today)) $td.addClass('is-today');
          if (dIso === selectedDate) $td.addClass('is-selected');

          $td.append('<div class="cal-daynum">' + d.getDate() + '</div>');

          var dots = opts.dotsFor(dIso) || [];
          if (dots.length) {
            var $dots = $('<div class="cal-dots"></div>');
            dots.forEach(function (cls) { $dots.append('<span class="slot-dot ' + cls + '"></span>'); });
            $td.append($dots);
          }
          $row.append($td);
        }
        $body.append($row);
      }
    }

    $('#cal-grid').off('click.cal').on('click.cal', 'td.has-slots', function () {
      selectedDate = $(this).data('date');
      render();
      opts.onSelectDay(selectedDate);
    });

    $('#cal-prev').off('click.cal').on('click.cal', function () { calDate.setMonth(calDate.getMonth() - 1); render(); });
    $('#cal-next').off('click.cal').on('click.cal', function () { calDate.setMonth(calDate.getMonth() + 1); render(); });

    render();

    return {
      refresh: render,
      clearSelection: function () { selectedDate = null; $('#day-detail').addClass('d-none').empty(); }
    };
  }

  function setDayDetail($content) {
    $('#day-detail').removeClass('d-none').empty().append($content);
  }

  // Backend: load from defense_schedules
  function renderBooked(filterFn) {
    var $list = $('#booked-list');
    if (!$list.length) return;
    var items = MOCK.booked.filter(filterFn || function () { return true; });
    $list.empty();
    if (!items.length) {
      $list.append('<div class="dash-empty">No booked defenses yet.</div>');
      return;
    }
    items.forEach(function (b) {
      var panelNames = b.panel.map(function (pid) {
        var p = MOCK.professors.filter(function (x) { return x.id === pid; })[0];
        return p ? p.name : 'Prof ' + pid;
      }).join(', ');
      var $card = $('<div class="request-card"></div>');
      $card.append(
        '<div class="request-top">' +
          '<div><strong>' + b.thesis_title + '</strong>' +
          '<div class="text-body-secondary small">' + b.date + ' \u00b7 ' + b.start + '-' + b.end + ' \u00b7 ' + b.venue + '</div>' +
          '<div class="text-body-secondary small">Panel: ' + panelNames + '</div></div>' +
          '<span class="badge status-approved">' + b.status + '</span>' +
        '</div>'
      );
      $list.append($card);
    });
  }

  // Professor dashboard
  function initProfessorPage() {
    if (!$('#prof-nav').length) return;

    initTabs('#prof-nav');

    var professorId = parseInt(($('body').data('professorId') + '').replace(/\D/g, ''), 10) || 1;
    var prof = MOCK.professors.filter(function (p) { return p.id === professorId; })[0] || MOCK.professors[0];
    $('#prof-name-label').text('Logged in as ' + prof.name + ' \u2014 ' + prof.dept);

    // Backend: load slots from professor_availability for this professor
    function slotsFor(dateIso) {
      return MOCK.availability.filter(function (a) { return a.professor_id === professorId && a.date === dateIso; });
    }

    function renderDayDetail(dateIso) {
      var slots = slotsFor(dateIso).sort(function (a, b) { return a.start.localeCompare(b.start); });
      var $wrap = $('<div></div>');
      $wrap.append('<div class="day-detail-title">' + new Date(dateIso + 'T00:00:00').toDateString() + '</div>');

      if (!slots.length) {
        $wrap.append('<p class="dash-empty mb-2">No slots yet for this day.</p>');
      } else {
        slots.forEach(function (s) {
          var statusLabel = s.status === 'available' ? 'Open' : (s.status === 'booked' ? 'Booked' : 'Blocked');
          var $row = $('<div class="slot-row"></div>');
          $row.append('<span>' + s.start + ' \u2013 ' + s.end + ' <span class="badge ' + (s.status === 'available' ? 'status-approved' : (s.status === 'booked' ? 'status-pending' : 'status-rejected')) + '">' + statusLabel + '</span></span>');
          if (s.status !== 'booked') {
            var $del = $('<button type="button" class="btn btn-sm btn-outline-primary">Remove</button>');
            $del.on('click', function (slotId) {
              return function () {
                // Backend: delete from professor_availability where id = slotId
                MOCK.availability = MOCK.availability.filter(function (a) { return a.id !== slotId; });
                calendar.refresh();
                renderDayDetail(dateIso);
              };
            }(s.id));
            $row.append($del);
          }
          $wrap.append($row);
        });
      }

      var $form = $(
        '<div class="reject-form">' +
          '<label class="form-label">Add a slot</label>' +
          '<div class="d-flex gap-2 align-items-end flex-wrap">' +
            '<div><label class="form-label small mb-1">Start time</label><input type="time" class="form-control form-control-sm" id="new-slot-start" value="09:00"></div>' +
            '<button type="button" class="btn btn-sm btn-primary" id="add-slot-btn">Add (1 hr)</button>' +
          '</div>' +
        '</div>'
      );
      $wrap.append($form);
      setDayDetail($wrap);

      $('#add-slot-btn').on('click', function () {
        var start = $('#new-slot-start').val();
        if (!start) return;
        var startH = parseInt(start.split(':')[0], 10);
        var startM = start.split(':')[1];
        var end = pad2(startH + 1) + ':' + startM;

        // Backend: insert into professor_availability
        MOCK.availability.push({
          id: MOCK.nextAvailId++,
          professor_id: professorId,
          date: dateIso,
          start: start,
          end: end,
          status: 'available'
        });
        calendar.refresh();
        renderDayDetail(dateIso);
      });
    }

    var calendar = initCalendar({
      dayHasContent: function (dateIso) { return slotsFor(dateIso).length > 0; },
      dotsFor: function (dateIso) {
        return slotsFor(dateIso).map(function (s) {
          return s.status === 'available' ? 'open' : (s.status === 'booked' ? 'pending' : 'booked');
        });
      },
      onSelectDay: renderDayDetail
    });

    // Requests tab
    // Backend: load schedule_requests where this professor is on the panel
    function myPendingRequests() {
      return MOCK.requests.filter(function (r) { return r.panel.indexOf(professorId) !== -1; });
    }

    function renderRequests() {
      var reqs = myPendingRequests();
      var $list = $('#requests-list').empty();

      if (!reqs.length) {
        $list.append('<div class="dash-empty">No requests need your response right now.</div>');
        return;
      }

      reqs.forEach(function (r) {
        var mine = r.approvals[professorId];
        var $card = $('<div class="request-card"></div>');
        $card.append(
          '<div class="request-top">' +
            '<div><strong>' + r.thesis_title + '</strong>' +
            '<div class="text-body-secondary small">' + r.date + ' \u00b7 ' + r.start + '-' + r.end + ' \u00b7 ' + r.venue + '</div></div>' +
            '<span class="badge ' + badgeClassFor(mine.status) + '">' + mine.status + '</span>' +
          '</div>'
        );

        if (mine.status === 'pending') {
          var $actions = $(
            '<div class="mt-2 d-flex gap-2">' +
              '<button type="button" class="btn btn-sm btn-primary approve-btn">Approve</button>' +
              '<button type="button" class="btn btn-sm btn-outline-primary reject-btn">Reject</button>' +
            '</div>'
          );
          $card.append($actions);

          $actions.find('.approve-btn').on('click', function (reqId) {
            return function () {
              // Backend: insert/update schedule_approvals status = approved
              // Backend: if all 3 approved → create defense_schedules row
              MOCK.requests.filter(function (x) { return x.id === reqId; })[0].approvals[professorId] = { status: 'approved', remarks: null };
              showAlert('Response recorded: approved.', 'success');
              renderRequests();
            };
          }(r.id));

          $actions.find('.reject-btn').on('click', function (reqId, cardEl) {
            return function () {
              var $form = $(
                '<div class="reject-form">' +
                  '<label class="reason-check"><input type="checkbox" value="schedule_conflict"> I have a scheduling conflict</label>' +
                  '<label class="reason-check"><input type="checkbox" value="not_enough_notice"> Not enough advance notice</label>' +
                  '<label class="reason-check"><input type="checkbox" value="incomplete_manuscript"> Manuscript not ready</label>' +
                  '<label class="reason-check"><input type="checkbox" value="other"> Other</label>' +
                  '<textarea class="form-control form-control-sm mt-2" placeholder="Optional: suggest an alternative date/time"></textarea>' +
                  '<button type="button" class="btn btn-sm btn-outline-primary mt-2 confirm-reject-btn">Confirm reject</button>' +
                '</div>'
              );
              $(cardEl).append($form);
              $(cardEl).find('.confirm-reject-btn').on('click', function () {
                var reasons = [];
                $form.find('input[type=checkbox]:checked').each(function () { reasons.push($(this).val()); });
                if (!reasons.length) {
                  showAlert('Select at least one reason before rejecting.', 'danger');
                  return;
                }
                var suggestion = $form.find('textarea').val().trim();
                // Backend: insert/update schedule_approvals status = rejected + remarks
                MOCK.requests.filter(function (x) { return x.id === reqId; })[0].approvals[professorId] = {
                  status: 'rejected',
                  remarks: reasons.join(', ') + (suggestion ? (' \u2014 ' + suggestion) : '')
                };
                showAlert('Response recorded: rejected.', 'success');
                renderRequests();
              });
            };
          }(r.id, $card[0]));
        } else if (mine.remarks) {
          $card.append('<div class="reject-detail"><div class="suggestion-box">' + mine.remarks + '</div></div>');
        }

        $list.append($card);
      });
    }

    renderRequests();

    // Backend: filter booked where this professor is on the panel
    renderBooked(function (b) { return b.panel.indexOf(professorId) !== -1; });
  }

  // Student dashboard
  function initStudentPage() {
    if (!$('#student-nav').length) return;

    initTabs('#student-nav');

    var groupId = ($('body').data('groupId') + '') || 'group1';
    var selectedProfs = [];

    // Backend: load professors from users where role = professor
    function renderProfPicker() {
      var $picker = $('#prof-picker').empty();
      MOCK.professors.forEach(function (p) {
        var isSelected = selectedProfs.indexOf(p.id) !== -1;
        var isDisabled = !isSelected && selectedProfs.length >= 3;
        var $chip = $('<div class="prof-chip"></div>').text(p.name).attr('data-id', p.id);
        if (isSelected) $chip.addClass('is-selected');
        if (isDisabled) $chip.addClass('is-disabled');
        $picker.append($chip);
      });
      var $counter = $('#prof-counter').text(selectedProfs.length + ' / 3 selected');
      $counter.toggleClass('is-complete', selectedProfs.length === 3);
    }

    // Backend: load availability for the 3 selected professors
    function commonSlotsFor(dateIso) {
      if (selectedProfs.length !== 3) return [];
      var byProf = selectedProfs.map(function (pid) {
        return MOCK.availability.filter(function (a) { return a.professor_id === pid && a.date === dateIso && a.status === 'available'; });
      });
      var starts = byProf[0].map(function (s) { return s.start; });
      return starts.filter(function (start) {
        return byProf[1].some(function (s) { return s.start === start; }) &&
               byProf[2].some(function (s) { return s.start === start; });
      });
    }

    function anyFree(dateIso) {
      if (!selectedProfs.length) return false;
      return selectedProfs.some(function (pid) {
        return MOCK.availability.some(function (a) { return a.professor_id === pid && a.date === dateIso && a.status === 'available'; });
      });
    }

    function renderDayDetail(dateIso) {
      var matches = commonSlotsFor(dateIso);
      var $wrap = $('<div></div>');
      $wrap.append('<div class="day-detail-title">' + new Date(dateIso + 'T00:00:00').toDateString() + '</div>');

      if (!matches.length) {
        $wrap.append('<p class="dash-empty mb-0">No shared open slot on this day.</p>');
      } else {
        matches.forEach(function (start) {
          var startH = parseInt(start.split(':')[0], 10);
          var end = pad2(startH + 1) + ':00';
          var $row = $('<div class="slot-row"></div>');
          $row.append('<span>' + start + ' \u2013 ' + end + '</span>');
          var $btn = $('<button type="button" class="btn btn-sm btn-primary">Request this slot</button>');
          $btn.on('click', function () {
            // Backend: insert into schedule_requests + create pending schedule_approvals for each of the 3
            var newReq = {
              id: MOCK.nextRequestId++,
              group_id: groupId,
              thesis_title: MOCK.currentUser ? MOCK.currentUser.name : 'My Thesis Group',
              panel: selectedProfs.slice(),
              date: dateIso,
              start: start,
              end: end,
              venue: 'TBD',
              status: 'pending',
              approvals: {}
            };
            selectedProfs.forEach(function (pid) { newReq.approvals[pid] = { status: 'pending', remarks: null }; });
            MOCK.requests.push(newReq);
            showAlert('Defense request sent for ' + dateIso + ' at ' + start + '.', 'success');
            calendar.clearSelection();
            renderStudentRequests();
          });
          $row.append($btn);
          $wrap.append($row);
        });
      }
      setDayDetail($wrap);
    }

    var calendar = initCalendar({
      dayHasContent: function (dateIso) { return selectedProfs.length === 3 && commonSlotsFor(dateIso).length > 0; },
      cellExtraClass: function (dateIso) {
        if (selectedProfs.length !== 3) return '';
        return commonSlotsFor(dateIso).length ? 'cal-match' : 'cal-no-match';
      },
      dotsFor: function (dateIso) {
        if (selectedProfs.length !== 3 || !anyFree(dateIso)) return [];
        return [commonSlotsFor(dateIso).length ? 'open' : 'pending'];
      },
      onSelectDay: renderDayDetail
    });

    $('#prof-picker').on('click', '.prof-chip', function () {
      var id = parseInt($(this).data('id'), 10);
      var idx = selectedProfs.indexOf(id);
      if (idx !== -1) {
        selectedProfs.splice(idx, 1);
      } else if (selectedProfs.length < 3) {
        selectedProfs.push(id);
      }
      renderProfPicker();
      renderAvailabilityReference();
      calendar.clearSelection();
      calendar.refresh();
    });

    // Backend: load availability per professor for the reference list
    function renderAvailabilityReference() {
      var $list = $('#prof-availability-list').empty();
      var profsToShow = selectedProfs.length ? selectedProfs : MOCK.professors.map(function (p) { return p.id; });

      profsToShow.forEach(function (pid) {
        var prof = MOCK.professors.filter(function (p) { return p.id === pid; })[0];
        var upcoming = MOCK.availability
          .filter(function (a) { return a.professor_id === pid && a.status === 'available' && a.date >= iso(today); })
          .sort(function (a, b) { return a.date.localeCompare(b.date); })
          .slice(0, 4);

        var $card = $('<div class="request-card"></div>');
        $card.append('<div class="request-top"><strong>' + prof.name + '</strong><span class="text-body-secondary small">' + prof.dept + '</span></div>');
        if (!upcoming.length) {
          $card.append('<p class="dash-empty mb-0">No upcoming open slots.</p>');
        } else {
          upcoming.forEach(function (a) {
            $card.append('<div class="slot-row"><span>' + a.date + '</span><span>' + a.start + ' \u2013 ' + a.end + '</span></div>');
          });
        }
        $list.append($card);
      });
    }

    // Backend: load schedule_requests + schedule_approvals for this group
    function renderStudentRequests() {
      var $list = $('#requests-list').empty();
      var myRequests = MOCK.requests.filter(function (r) { return r.group_id === groupId; });

      if (!myRequests.length) {
        $list.append('<div class="dash-empty">You have not requested a defense schedule yet.</div>');
        return;
      }

      myRequests.forEach(function (r) {
        var $card = $('<div class="request-card"></div>');
        $card.append(
          '<div class="request-top">' +
            '<div><strong>' + r.date + ' \u00b7 ' + r.start + '-' + r.end + '</strong>' +
            '<div class="text-body-secondary small">Venue: ' + r.venue + '</div></div>' +
            '<span class="badge ' + badgeClassFor(r.status) + '">' + r.status + '</span>' +
          '</div>'
        );
        r.panel.forEach(function (pid) {
          var prof = MOCK.professors.filter(function (p) { return p.id === pid; })[0];
          var a = r.approvals[pid];
          var $decision = $('<div class="decision-row"></div>');
          $decision.append('<span>' + prof.name + '</span><span class="badge ' + badgeClassFor(a.status) + '">' + a.status + '</span>');
          $card.append($decision);
          if (a.remarks) {
            $card.append('<div class="reject-detail"><span class="reason-tag">' + a.remarks + '</span></div>');
          }
        });
        $list.append($card);
      });
    }

    renderProfPicker();
    renderAvailabilityReference();
    renderStudentRequests();

    // Backend: filter booked where group_id matches
    renderBooked(function (b) { return b.group_id === groupId; });
  }

  // Boot
  $(function () {
    initLoginPage();
    initProfessorPage();
    initStudentPage();
  });
})();