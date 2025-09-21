const API_BASE = '';

const state = {
  token: window.localStorage.getItem('bloodvault.token'),
  user: null,
  summary: null,
  requests: [],
  donations: [],
  appointments: [],
  inventory: []
};

const elements = {
  authSection: document.getElementById('auth-section'),
  appShell: document.getElementById('app-shell'),
  logoutButton: document.getElementById('logout-button'),
  statusBar: document.getElementById('status-bar'),
  userName: document.getElementById('user-name'),
  userRole: document.getElementById('user-role'),
  userEmail: document.getElementById('user-email'),
  summaryCards: document.getElementById('summary-cards'),
  recentRequestsTable: document.getElementById('recent-requests-table'),
  upcomingAppointmentsTable: document.getElementById('upcoming-appointments-table'),
  requestsTable: document.getElementById('requests-table'),
  donationsTable: document.getElementById('donations-table'),
  appointmentsTable: document.getElementById('appointments-table'),
  inventoryTable: document.getElementById('inventory-table'),
  profileDetails: document.getElementById('profile-details'),
  eligibilityPanel: document.getElementById('eligibility-panel'),
  emptyRowTemplate: document.getElementById('empty-row-template')
};

const forms = {
  login: document.getElementById('login-form'),
  register: document.getElementById('register-form'),
  request: document.getElementById('create-request-form'),
  donation: document.getElementById('create-donation-form'),
  appointment: document.getElementById('create-appointment-form')
};

const formErrors = {
  login: document.getElementById('login-error'),
  register: document.getElementById('register-error'),
  request: document.getElementById('request-error'),
  donation: document.getElementById('donation-error'),
  appointment: document.getElementById('appointment-error')
};

const buttons = {
  dashboardRefresh: document.getElementById('dashboard-refresh'),
  refreshRequests: document.getElementById('refresh-requests'),
  refreshDonations: document.getElementById('refresh-donations'),
  refreshAppointments: document.getElementById('refresh-appointments'),
  refreshInventory: document.getElementById('refresh-inventory'),
  refreshProfile: document.getElementById('refresh-profile'),
  refreshEligibility: document.getElementById('refresh-eligibility'),
  requestVerification: document.getElementById('request-verification')
};

const formCards = {
  request: document.getElementById('request-form-card'),
  donation: document.getElementById('donation-form-card'),
  appointment: document.getElementById('appointment-form-card')
};

let statusTimeoutId = null;

function setToken(token) {
  state.token = token;
  if (token) {
    window.localStorage.setItem('bloodvault.token', token);
  } else {
    window.localStorage.removeItem('bloodvault.token');
  }
}

function showStatus(message, variant = 'info', timeout = 4000) {
  if (!elements.statusBar) {
    return;
  }
  elements.statusBar.textContent = message;
  elements.statusBar.classList.remove('hidden', 'info', 'success', 'error');
  elements.statusBar.classList.add(variant);
  if (statusTimeoutId) {
    window.clearTimeout(statusTimeoutId);
  }
  statusTimeoutId = window.setTimeout(() => {
    elements.statusBar.classList.add('hidden');
  }, timeout);
}

async function apiFetch(path, options = {}) {
  const url = `${API_BASE}${path}`;
  const fetchOptions = { ...options };
  fetchOptions.headers = new Headers(options.headers || {});

  if (state.token) {
    fetchOptions.headers.set('Authorization', `Bearer ${state.token}`);
  }

  if (fetchOptions.body && !(fetchOptions.body instanceof FormData)) {
    fetchOptions.headers.set('Content-Type', 'application/json');
  }

  const response = await fetch(url, fetchOptions);
  const contentType = response.headers.get('content-type') || '';
  let payload = null;

  if (contentType.includes('application/json')) {
    payload = await response.json();
  } else {
    payload = await response.text();
  }

  if (!response.ok) {
    const message = payload && typeof payload === 'object' && payload.message
      ? payload.message
      : typeof payload === 'string' && payload
        ? payload
        : `Request failed with status ${response.status}`;
    const error = new Error(message);
    error.status = response.status;
    throw error;
  }

  return payload;
}

function formatDate(value) {
  if (!value) {
    return '—';
  }
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return String(value);
  }
  return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateTime(value) {
  if (!value) {
    return '—';
  }
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return String(value);
  }
  return date.toLocaleString(undefined, { dateStyle: 'medium', timeStyle: 'short' });
}

function formatStatus(value) {
  if (!value) {
    return '—';
  }
  return value
    .toString()
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase());
}

function formatRole(role) {
  return formatStatus(role || '');
}

function showAuthShell(authenticated) {
  if (authenticated) {
    elements.authSection?.classList.add('hidden');
    elements.appShell?.classList.remove('hidden');
    elements.logoutButton?.classList.remove('hidden');
  } else {
    elements.authSection?.classList.remove('hidden');
    elements.appShell?.classList.add('hidden');
    elements.logoutButton?.classList.add('hidden');
  }
}

function setActiveSection(name) {
  const targetId = `${name}-section`;
  document.querySelectorAll('.content-section').forEach((section) => {
    section.classList.toggle('active', section.id === targetId);
  });
  document.querySelectorAll('.nav-link').forEach((link) => {
    link.classList.toggle('active', link.dataset.section === name);
  });
}

function updateUserSummary() {
  if (!state.user) {
    elements.userName.textContent = '';
    elements.userRole.textContent = '';
    elements.userEmail.textContent = '';
    return;
  }
  elements.userName.textContent = state.user.name || state.user.email || `User #${state.user.id}`;
  elements.userRole.textContent = formatRole(state.user.usertype);
  elements.userEmail.textContent = state.user.email || '';
}

function updateFormPermissions() {
  if (!state.user) {
    return;
  }
  const role = state.user.usertype;
  const isAdmin = role === 'admin';
  const canRequest = isAdmin || role === 'requester';
  const canDonate = isAdmin || role === 'donor';
  const canAppointment = isAdmin || role === 'donor' || role === 'requester';

  formCards.request?.classList.toggle('hidden', !canRequest);
  formCards.donation?.classList.toggle('hidden', !canDonate);
  formCards.appointment?.classList.toggle('hidden', !canAppointment);
}

async function refreshUser() {
  if (!state.token) {
    throw new Error('Missing authentication token.');
  }
  try {
    const user = await apiFetch('/users/me');
    state.user = user;
    updateUserSummary();
    updateFormPermissions();
    return user;
  } catch (error) {
    if (error.status === 401) {
      setToken(null);
      showAuthShell(false);
    }
    throw error;
  }
}

async function loadSummary() {
  try {
    const summary = await apiFetch('/dashboard/summary');
    state.summary = summary;
    renderSummary(summary);
  } catch (error) {
    console.error('Failed to load summary', error);
    showStatus(error.message, 'error');
  }
}

function renderSummary(summary) {
  if (!summary) {
    elements.summaryCards.innerHTML = '';
    return;
  }
  const totals = summary.totals || {};
  const summaryDefinitions = [
    { key: 'requests', label: 'Total requests' },
    { key: 'pendingRequests', label: 'Pending requests' },
    { key: 'donations', label: 'Total donations' },
    { key: 'completedDonations', label: 'Completed donations' },
    { key: 'upcomingAppointments', label: 'Upcoming appointments' },
    { key: 'availableUnits', label: 'Available units' }
  ];

  elements.summaryCards.innerHTML = '';
  summaryDefinitions.forEach((item) => {
    const card = document.createElement('div');
    card.className = 'summary-card';
    const value = document.createElement('strong');
    value.textContent = typeof totals[item.key] === 'number' ? totals[item.key].toString() : '0';
    const label = document.createElement('span');
    label.textContent = item.label;
    card.append(value, label);
    elements.summaryCards.appendChild(card);
  });

  fillTable(elements.recentRequestsTable, summary.recentRequests || [], (request) => {
    const row = document.createElement('tr');
    row.append(
      createCell(formatDate(request.requestDate)),
      createCell(request.bloodType || '—'),
      createCell(request.unitsNeeded ?? '—'),
      createCell(formatStatus(request.status))
    );
    return row;
  });

  fillTable(elements.upcomingAppointmentsTable, summary.upcomingAppointments || [], (appointment) => {
    const row = document.createElement('tr');
    row.append(
      createCell(formatDateTime(appointment.appointmentDate)),
      createCell(appointment.appointmentType || '—'),
      createCell(formatStatus(appointment.status))
    );
    return row;
  });
}

async function loadRequests() {
  try {
    const requests = await apiFetch('/blood-requests');
    state.requests = Array.isArray(requests) ? requests : [];
    renderRequests();
  } catch (error) {
    console.error('Failed to load requests', error);
    showStatus(error.message, 'error');
  }
}

function renderRequests() {
  const isAdmin = state.user?.usertype === 'admin';
  fillTable(elements.requestsTable, state.requests, (request) => {
    const row = document.createElement('tr');

    row.append(
      createCell(formatDateTime(request.requestDate)),
      createCell(request.bloodType || '—'),
      createCell(request.unitsNeeded ?? '—'),
      createCell(formatStatus(request.urgency))
    );

    const statusCell = document.createElement('td');
    let statusSelect = null;
    if (isAdmin) {
      statusSelect = document.createElement('select');
      ['pending', 'approved', 'rejected', 'completed', 'cancelled'].forEach((status) => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = formatStatus(status);
        statusSelect.appendChild(option);
      });
      statusSelect.value = request.status || 'pending';
      statusCell.appendChild(statusSelect);
    } else {
      statusCell.textContent = formatStatus(request.status);
    }
    row.appendChild(statusCell);

    const availableCell = document.createElement('td');
    let availableCheckbox = null;
    if (isAdmin) {
      availableCheckbox = document.createElement('input');
      availableCheckbox.type = 'checkbox';
      availableCheckbox.checked = Boolean(request.bloodAvailable);
      availableCell.appendChild(availableCheckbox);
    } else {
      availableCell.textContent = request.bloodAvailable ? 'Yes' : 'No';
    }
    row.appendChild(availableCell);

    const allocatedCell = document.createElement('td');
    let allocatedInput = null;
    if (isAdmin) {
      allocatedInput = document.createElement('input');
      allocatedInput.type = 'number';
      allocatedInput.min = '0';
      allocatedInput.value = request.allocatedUnits ?? 0;
      allocatedInput.className = 'small-input';
      allocatedCell.appendChild(allocatedInput);
    } else {
      allocatedCell.textContent = request.allocatedUnits ?? 0;
    }
    row.appendChild(allocatedCell);

    const requesterCell = createCell(
      request.user?.name || request.user?.email || (request.user ? `User #${request.user.id}` : '—')
    );
    row.appendChild(requesterCell);

    const actionsCell = document.createElement('td');
    actionsCell.className = 'actions-cell';
    if (isAdmin) {
      const saveButton = document.createElement('button');
      saveButton.type = 'button';
      saveButton.className = 'button small primary';
      saveButton.textContent = 'Save';
      saveButton.addEventListener('click', async () => {
        saveButton.disabled = true;
        try {
          await updateRequestStatus(request.id, {
            status: statusSelect.value,
            bloodAvailable: Boolean(availableCheckbox.checked),
            allocatedUnits: Number.parseInt(allocatedInput.value, 10) || 0
          });
          showStatus('Request updated successfully.', 'success');
          await loadRequests();
        } catch (error) {
          showStatus(error.message, 'error');
        } finally {
          saveButton.disabled = false;
        }
      });
      actionsCell.appendChild(saveButton);
    } else {
      actionsCell.textContent = '—';
    }
    row.appendChild(actionsCell);

    return row;
  });
}

async function updateRequestStatus(id, payload) {
  return apiFetch(`/blood-requests/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify(payload)
  });
}

async function loadDonations() {
  try {
    const donations = await apiFetch('/blood-donations');
    state.donations = Array.isArray(donations) ? donations : [];
    renderDonations();
  } catch (error) {
    console.error('Failed to load donations', error);
    showStatus(error.message, 'error');
  }
}

function renderDonations() {
  const isAdmin = state.user?.usertype === 'admin';
  fillTable(elements.donationsTable, state.donations, (donation) => {
    const row = document.createElement('tr');
    row.append(
      createCell(formatDate(donation.donationDate)),
      createCell(donation.bloodType || '—'),
      createCell(donation.quantity ?? '—')
    );

    const statusCell = document.createElement('td');
    let statusSelect = null;
    if (isAdmin) {
      statusSelect = document.createElement('select');
      ['pending', 'approved', 'completed', 'rejected'].forEach((status) => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = formatStatus(status);
        statusSelect.appendChild(option);
      });
      statusSelect.value = donation.status || 'pending';
      statusCell.appendChild(statusSelect);
    } else {
      statusCell.textContent = formatStatus(donation.status);
    }
    row.appendChild(statusCell);

    const screeningCell = document.createElement('td');
    let screeningInput = null;
    if (isAdmin) {
      screeningInput = document.createElement('input');
      screeningInput.type = 'text';
      screeningInput.value = donation.screeningStatus || '';
      screeningCell.appendChild(screeningInput);
    } else {
      screeningCell.textContent = donation.screeningStatus || '—';
    }
    row.appendChild(screeningCell);

    row.appendChild(
      createCell(donation.user?.name || donation.donorName || donation.user?.email || '—')
    );

    const actionsCell = document.createElement('td');
    actionsCell.className = 'actions-cell';
    if (isAdmin) {
      const saveButton = document.createElement('button');
      saveButton.type = 'button';
      saveButton.className = 'button small primary';
      saveButton.textContent = 'Save';
      saveButton.addEventListener('click', async () => {
        saveButton.disabled = true;
        try {
          await updateDonationStatus(donation.id, {
            status: statusSelect.value,
            screeningStatus: screeningInput.value
          });
          showStatus('Donation updated successfully.', 'success');
          await loadDonations();
        } catch (error) {
          showStatus(error.message, 'error');
        } finally {
          saveButton.disabled = false;
        }
      });
      actionsCell.appendChild(saveButton);
    } else {
      actionsCell.textContent = '—';
    }
    row.appendChild(actionsCell);

    return row;
  });
}

async function updateDonationStatus(id, payload) {
  return apiFetch(`/blood-donations/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify(payload)
  });
}

async function loadAppointments() {
  try {
    const appointments = await apiFetch('/appointments');
    state.appointments = Array.isArray(appointments) ? appointments : [];
    renderAppointments();
  } catch (error) {
    console.error('Failed to load appointments', error);
    showStatus(error.message, 'error');
  }
}

function renderAppointments() {
  const isAdmin = state.user?.usertype === 'admin';
  fillTable(elements.appointmentsTable, state.appointments, (appointment) => {
    const row = document.createElement('tr');
    row.append(
      createCell(formatDateTime(appointment.appointmentDate)),
      createCell(appointment.appointmentType || '—')
    );

    const statusCell = document.createElement('td');
    let statusSelect = null;
    if (isAdmin) {
      statusSelect = document.createElement('select');
      ['pending', 'confirmed', 'completed', 'cancelled'].forEach((status) => {
        const option = document.createElement('option');
        option.value = status;
        option.textContent = formatStatus(status);
        statusSelect.appendChild(option);
      });
      statusSelect.value = appointment.status || 'pending';
      statusCell.appendChild(statusSelect);
    } else {
      statusCell.textContent = formatStatus(appointment.status);
    }
    row.appendChild(statusCell);

    row.appendChild(createCell(appointment.notes || '—'));
    row.appendChild(
      createCell(
        appointment.user?.name || appointment.user?.email || (appointment.user ? `User #${appointment.user.id}` : '—')
      )
    );

    const actionsCell = document.createElement('td');
    actionsCell.className = 'actions-cell';
    if (isAdmin) {
      const saveButton = document.createElement('button');
      saveButton.type = 'button';
      saveButton.className = 'button small primary';
      saveButton.textContent = 'Save';
      saveButton.addEventListener('click', async () => {
        saveButton.disabled = true;
        try {
          await updateAppointmentStatus(appointment.id, { status: statusSelect.value });
          showStatus('Appointment updated successfully.', 'success');
          await loadAppointments();
        } catch (error) {
          showStatus(error.message, 'error');
        } finally {
          saveButton.disabled = false;
        }
      });
      actionsCell.appendChild(saveButton);
    } else {
      actionsCell.textContent = '—';
    }
    row.appendChild(actionsCell);

    return row;
  });
}

async function updateAppointmentStatus(id, payload) {
  return apiFetch(`/appointments/${id}/status`, {
    method: 'PATCH',
    body: JSON.stringify(payload)
  });
}

async function loadInventory() {
  try {
    const inventory = await apiFetch('/inventory');
    state.inventory = Array.isArray(inventory) ? inventory : [];
    renderInventory();
  } catch (error) {
    console.error('Failed to load inventory', error);
    showStatus(error.message, 'error');
  }
}

function renderInventory() {
  const statusMap = {
    1: 'Approved',
    0: 'Pending',
    '-1': 'Denied'
  };
  fillTable(elements.inventoryTable, state.inventory, (item) => {
    const row = document.createElement('tr');
    row.append(
      createCell(item.bloodType || '—'),
      createCell(item.quantity ?? '—'),
      createCell(statusMap[item.status] || formatStatus(item.status)),
      createCell(formatDate(item.expirationDate)),
      createCell(item.donor?.name || item.donor?.email || (item.donor ? `User #${item.donor.id}` : '—'))
    );
    return row;
  });
}

async function loadProfile() {
  if (!state.user) {
    elements.profileDetails.innerHTML = '';
    return;
  }
  const user = state.user;
  const profileEntries = [
    ['Name', user.name || '—'],
    ['Email', user.email || '—'],
    ['Role', formatRole(user.usertype)],
    ['Blood type', user.bloodtype || '—'],
    ['City', user.city || '—'],
    ['Province', user.province || '—'],
    ['Contact', user.contact || '—'],
    ['Address', user.address || '—'],
    ['Email verified', user.emailVerifiedAt ? formatDateTime(user.emailVerifiedAt) : 'No']
  ];

  elements.profileDetails.innerHTML = '';
  profileEntries.forEach(([label, value]) => {
    const dt = document.createElement('dt');
    dt.textContent = label;
    const dd = document.createElement('dd');
    dd.textContent = value;
    elements.profileDetails.append(dt, dd);
  });
}

function fillTable(tableElement, items, createRow) {
  if (!tableElement) {
    return;
  }
  const tbody = tableElement.querySelector('tbody');
  tbody.innerHTML = '';
  if (!items || items.length === 0) {
    const fragment = elements.emptyRowTemplate.content.cloneNode(true);
    const cell = fragment.querySelector('td');
    const columnCount = tableElement.querySelectorAll('thead th').length || 1;
    cell.setAttribute('colspan', columnCount);
    tbody.appendChild(fragment);
    return;
  }
  items.forEach((item) => {
    const row = createRow(item);
    tbody.appendChild(row);
  });
}

function createCell(value) {
  const cell = document.createElement('td');
  cell.textContent = value ?? '—';
  return cell;
}

async function handleLogin(event) {
  event.preventDefault();
  if (!forms.login) {
    return;
  }
  formErrors.login.classList.add('hidden');
  const formData = new FormData(forms.login);
  const payload = {
    email: formData.get('email'),
    password: formData.get('password')
  };

  try {
    const data = await apiFetch('/auth/login', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    setToken(data.token);
    showStatus('Welcome back!', 'success');
    forms.login.reset();
    await onAuthenticated(data.user || null);
  } catch (error) {
    formErrors.login.textContent = error.message;
    formErrors.login.classList.remove('hidden');
  }
}

async function handleRegister(event) {
  event.preventDefault();
  if (!forms.register) {
    return;
  }
  formErrors.register.classList.add('hidden');
  const formData = new FormData(forms.register);
  const payload = {
    name: formData.get('name'),
    email: formData.get('email'),
    password: formData.get('password'),
    usertype: formData.get('usertype')
  };

  try {
    const data = await apiFetch('/auth/register', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    setToken(data.token);
    showStatus('Account created successfully.', 'success');
    forms.register.reset();
    await onAuthenticated();
  } catch (error) {
    formErrors.register.textContent = error.message;
    formErrors.register.classList.remove('hidden');
  }
}

async function handleCreateRequest(event) {
  event.preventDefault();
  if (!forms.request) {
    return;
  }
  formErrors.request.classList.add('hidden');
  const formData = new FormData(forms.request);
  const payload = {
    bloodType: formData.get('bloodType'),
    unitsNeeded: Number.parseInt(formData.get('unitsNeeded'), 10) || 0,
    urgency: formData.get('urgency'),
    reason: formData.get('reason'),
    hospital: formData.get('hospital'),
    contactPerson: formData.get('contactPerson'),
    contactNumber: formData.get('contactNumber'),
    requestDate: formData.get('requestDate') || undefined
  };

  try {
    await apiFetch('/blood-requests', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    forms.request.reset();
    showStatus('Request submitted successfully.', 'success');
    await loadRequests();
    await loadSummary();
  } catch (error) {
    formErrors.request.textContent = error.message;
    formErrors.request.classList.remove('hidden');
  }
}

async function handleCreateDonation(event) {
  event.preventDefault();
  if (!forms.donation) {
    return;
  }
  formErrors.donation.classList.add('hidden');
  const formData = new FormData(forms.donation);
  const payload = {
    donorName: formData.get('donorName'),
    donorEmail: formData.get('donorEmail'),
    bloodType: formData.get('bloodType'),
    donationDate: formData.get('donationDate') || undefined,
    quantity: Number.parseInt(formData.get('quantity'), 10) || 1,
    screeningStatus: formData.get('screeningStatus'),
    notes: formData.get('notes')
  };

  try {
    await apiFetch('/blood-donations', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    forms.donation.reset();
    showStatus('Donation recorded successfully.', 'success');
    await loadDonations();
    await loadSummary();
  } catch (error) {
    formErrors.donation.textContent = error.message;
    formErrors.donation.classList.remove('hidden');
  }
}

async function handleCreateAppointment(event) {
  event.preventDefault();
  if (!forms.appointment) {
    return;
  }
  formErrors.appointment.classList.add('hidden');
  const formData = new FormData(forms.appointment);
  const payload = {
    appointmentType: formData.get('appointmentType'),
    bloodType: formData.get('bloodType'),
    appointmentDate: formData.get('appointmentDate'),
    timeSlot: formData.get('timeSlot'),
    notes: formData.get('notes')
  };

  try {
    await apiFetch('/appointments', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
    forms.appointment.reset();
    showStatus('Appointment scheduled successfully.', 'success');
    await loadAppointments();
    await loadSummary();
  } catch (error) {
    formErrors.appointment.textContent = error.message;
    formErrors.appointment.classList.remove('hidden');
  }
}

async function refreshEligibility() {
  if (!state.user) {
    return;
  }
  try {
    const eligibility = await apiFetch(`/users/${state.user.id}/donation-eligibility`);
    elements.eligibilityPanel.classList.remove('hidden');
    const { canDonate, nextEligibleDate, remainingCooldown } = eligibility;
    const parts = [];
    parts.push(canDonate ? 'You are eligible to donate.' : 'You are currently not eligible to donate.');
    if (nextEligibleDate) {
      parts.push(`Next eligible date: ${formatDate(nextEligibleDate)}.`);
    }
    if (typeof remainingCooldown === 'number' && remainingCooldown > 0) {
      parts.push(`Days remaining in cooldown: ${remainingCooldown}.`);
    }
    elements.eligibilityPanel.textContent = parts.join(' ');
  } catch (error) {
    showStatus(error.message, 'error');
  }
}

async function requestVerificationEmail() {
  if (!state.user) {
    return;
  }
  try {
    await apiFetch(`/users/${state.user.id}/request-email-verification`, { method: 'POST' });
    showStatus('Verification email requested.', 'success');
  } catch (error) {
    showStatus(error.message, 'error');
  }
}

async function onAuthenticated(initialUser = null) {
  if (initialUser) {
    state.user = initialUser;
  }
  try {
    await refreshUser();
    showAuthShell(true);
    setActiveSection('dashboard');
    await Promise.all([
      loadSummary(),
      loadRequests(),
      loadDonations(),
      loadAppointments(),
      loadInventory()
    ]);
    await loadProfile();
  } catch (error) {
    console.error('Failed to complete authentication flow', error);
    showStatus(error.message, 'error');
  }
}

function logout() {
  setToken(null);
  state.user = null;
  state.summary = null;
  state.requests = [];
  state.donations = [];
  state.appointments = [];
  state.inventory = [];
  showAuthShell(false);
  elements.summaryCards.innerHTML = '';
  fillTable(elements.recentRequestsTable, [], () => document.createElement('tr'));
  fillTable(elements.upcomingAppointmentsTable, [], () => document.createElement('tr'));
  fillTable(elements.requestsTable, [], () => document.createElement('tr'));
  fillTable(elements.donationsTable, [], () => document.createElement('tr'));
  fillTable(elements.appointmentsTable, [], () => document.createElement('tr'));
  fillTable(elements.inventoryTable, [], () => document.createElement('tr'));
  elements.profileDetails.innerHTML = '';
  elements.eligibilityPanel.classList.add('hidden');
  showStatus('You have been logged out.', 'info');
}

function initTabs() {
  const tabButtons = document.querySelectorAll('.tab-button');
  tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const target = button.dataset.tab;
      tabButtons.forEach((btn) => {
        const isActive = btn === button;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-selected', String(isActive));
        const tabContent = document.getElementById(`${btn.dataset.tab}-tab`);
        tabContent?.classList.toggle('active', isActive);
        tabContent?.setAttribute('aria-hidden', String(!isActive));
      });
    });
  });
}

function initNavigation() {
  document.querySelectorAll('.nav-link').forEach((link) => {
    link.addEventListener('click', () => {
      setActiveSection(link.dataset.section);
    });
  });
}

function initForms() {
  forms.login?.addEventListener('submit', handleLogin);
  forms.register?.addEventListener('submit', handleRegister);
  forms.request?.addEventListener('submit', handleCreateRequest);
  forms.donation?.addEventListener('submit', handleCreateDonation);
  forms.appointment?.addEventListener('submit', handleCreateAppointment);
}

function initButtons() {
  elements.logoutButton?.addEventListener('click', logout);
  buttons.dashboardRefresh?.addEventListener('click', loadSummary);
  buttons.refreshRequests?.addEventListener('click', loadRequests);
  buttons.refreshDonations?.addEventListener('click', loadDonations);
  buttons.refreshAppointments?.addEventListener('click', loadAppointments);
  buttons.refreshInventory?.addEventListener('click', loadInventory);
  buttons.refreshProfile?.addEventListener('click', async () => {
    try {
      await refreshUser();
      await loadProfile();
    } catch (error) {
      showStatus(error.message, 'error');
    }
  });
  buttons.refreshEligibility?.addEventListener('click', refreshEligibility);
  buttons.requestVerification?.addEventListener('click', requestVerificationEmail);
}

function init() {
  initTabs();
  initNavigation();
  initForms();
  initButtons();

  if (state.token) {
    onAuthenticated().catch((error) => {
      console.error('Failed to restore session', error);
      logout();
    });
  } else {
    showAuthShell(false);
  }
}

init();
