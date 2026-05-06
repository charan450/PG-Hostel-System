// ================================================
// PG HOSTEL — main.js
// Shared functions used across all pages
// ================================================

// ── FLOOR & ROOM STRUCTURE ──
const ALL_ROOM_IDS = [
  101, 102, 103, 104, 105,
  201, 202, 203, 204, 205,
  301, 302, 303, 304, 305,
];

const FLOORS = [
  { label: 'Ground Floor', rooms: [101,102,103,104,105] },
  { label: 'First Floor',  rooms: [201,202,203,204,205] },
  { label: 'Second Floor', rooms: [301,302,303,304,305] },
];

// ── CHECK IF LOGGED IN ──
// Call this at top of every page
function checkLogin() {
  if (localStorage.getItem('pg-logged-in') !== 'true') {
    window.location.href = 'index.html';
  }
}

// ── LOGOUT ──
function logout() {
  localStorage.removeItem('pg-logged-in');
  window.location.href = 'index.html';
}

// ── LOAD ROOMS FROM STORAGE ──
function loadRooms() {
  const data = localStorage.getItem('pg-rooms');
  if (data) return JSON.parse(data);

  // Default empty rooms if nothing saved yet
  const rooms = {};
  ALL_ROOM_IDS.forEach(id => {
    rooms[id] = {
      id:      id,
      name:    '',
      phone:   '',
      members: 0,
      rent:    0,
      status:  'vacant',
    };
  });
  return rooms;
}

// ── SAVE ROOMS TO STORAGE ──
function saveRooms(rooms) {
  localStorage.setItem('pg-rooms', JSON.stringify(rooms));
}

// ── GET FLOOR NAME FROM ROOM ID ──
function getFloorName(roomId) {
  if (roomId >= 101 && roomId <= 105) return 'Ground Floor';
  if (roomId >= 201 && roomId <= 205) return 'First Floor';
  return 'Second Floor';
}

// ── GET INITIALS FROM NAME ──
function getInitials(name) {
  if (!name || name.trim() === '') return '?';
  const parts = name.trim().split(' ');
  if (parts.length === 1) return parts[0][0].toUpperCase();
  return (parts[0][0] + parts[1][0]).toUpperCase();
}

// ── FORMAT RUPEES ──
function formatRupees(amount) {
  return '₹' + Number(amount).toLocaleString('en-IN');
}

// ── STATUS BADGE HTML ──
function badgeHTML(status) {
  const map = {
    occupied: ['paid',    'Paid'],
    unpaid:   ['unpaid',  'Unpaid'],
    overdue:  ['overdue', 'Overdue'],
    vacant:   ['vacant',  'Vacant'],
  };
  const [cls, label] = map[status] || ['unpaid', status];
  return `<span class="badge ${cls}">
            <span class="badge-dot"></span>${label}
          </span>`;
}

// ── SHOW TOAST NOTIFICATION ──
// Usage: showToast('Saved successfully!', 'success')
//        showToast('Something went wrong', 'error')
function showToast(message, type = 'success') {

  // Remove existing toast if any
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();

  const icon = type === 'success' ? '✅' : '❌';

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
  document.body.appendChild(toast);

  // Show
  setTimeout(() => toast.classList.add('show'), 10);

  // Hide after 3 seconds
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}

// ── CLOSE MODAL ON OVERLAY CLICK ──
// Call this after your modal is in the DOM
function setupModalClose(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  modal.addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.remove('open');
    }
  });
}

// ── GET MONTH NAME FROM VALUE ──
// e.g. '2026-04' → 'April 2026'
function getMonthName(value) {
  const [year, month] = value.split('-');
  const date = new Date(year, parseInt(month) - 1, 1);
  return date.toLocaleString('en-IN', { month: 'long', year: 'numeric' });
}

// ── PAYMENT SUMMARY ──
// Returns { expected, collected, pending, overdue }
function getPaymentSummary(rooms) {
  let expected  = 0;
  let collected = 0;
  let pending   = 0;
  let overdue   = 0;

  ALL_ROOM_IDS.forEach(id => {
    const r = rooms[id];
    if (!r || r.status === 'vacant') return;
    const rent = Number(r.rent) || 0;
    expected += rent;
    if (r.status === 'occupied') collected += rent;
    if (r.status === 'unpaid')   pending   += rent;
    if (r.status === 'overdue')  overdue   += rent;
  });

  return { expected, collected, pending, overdue };
}