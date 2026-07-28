<?php
// Shared calendar UI for student + professor dashboards
// Data is loaded by app.js — no backend logic needed here
$scheduleRole = $scheduleRole ?? 'student';
$isProfessor = $scheduleRole === 'professor';
?>
<div class="card p-3 p-md-4">
  <div class="d-flex justify-content-between align-items-start mb-2">
    <div>
      <span class="eyebrow"><?php echo $isProfessor ? 'Set your open times' : 'Step 2'; ?></span>
      <h2 class="h5 mb-0"><?php echo $isProfessor ? 'Click a date to add or remove a slot' : 'Pick a date & time'; ?></h2>
    </div>
    <span class="badge bg-forest"><?php echo $isProfessor ? 'Professor view' : 'Student view'; ?></span>
  </div>

  <div class="cal-nav mt-3">
    <button class="btn btn-sm btn-outline-primary" id="cal-prev">&larr; Prev</button>
    <div class="cal-month-label" id="cal-month-label"></div>
    <button class="btn btn-sm btn-outline-primary" id="cal-next">Next &rarr;</button>
  </div>

  <table class="table cal-table mb-0">
    <thead>
      <tr><th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th></tr>
    </thead>
    <tbody id="cal-grid"></tbody>
  </table>

  <div class="cal-legend mt-3">
    <?php if ($isProfessor): ?>
      <span><span class="slot-dot open"></span> Open</span>
      <span><span class="slot-dot pending"></span> Requested (pending your decision)</span>
      <span><span class="slot-dot booked"></span> Booked</span>
    <?php else: ?>
      <span><span class="slot-dot open"></span> Open (all 3 available)</span>
      <span><span class="slot-dot pending"></span> Request pending</span>
      <span><span class="slot-dot booked"></span> Already booked</span>
    <?php endif; ?>
  </div>

  <div class="day-detail d-none" id="day-detail"></div>

  <?php if ($isProfessor): ?>
    <p class="text-body-secondary small mt-3 mb-0">Slots you add here appear on students' calendars immediately.</p>
  <?php endif; ?>
</div>