@extends('layouts.app')

@section('title', 'Appointments Management - Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        Appointments Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="exportAppointments()">
                            <i class="bi bi-download me-2"></i>
                            Export
                        </button>
                        <button class="btn btn-light me-2" onclick="refreshAppointments()">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Refresh
                        </button>

                    </div>
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Quick Stats -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-primary text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="totalAppointments">-</h6>
                                    <small>Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="pendingAppointments">-</h6>
                                    <small>Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="confirmedAppointments">-</h6>
                                    <small>Confirmed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="completedAppointments">-</h6>
                                    <small>Completed</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="cancelledAppointments">-</h6>
                                    <small>Cancelled</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white text-center stats-card">
                                <div class="card-body py-2">
                                    <h6 class="mb-0" id="rejectedAppointments">-</h6>
                                    <small>Rejected</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="donation">Donation</option>
                                <option value="screening">Screening</option>
                                <option value="consultation">Consultation</option>
                                <option value="follow_up">Follow-up</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="dateFilter" placeholder="Filter by date">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search appointments...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-info w-100" onclick="applyFilters()">
                                <i class="bi bi-search me-2"></i>
                                Apply Filters
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="bi bi-x-circle me-2"></i>
                                Clear Filters
                            </button>
                        </div>
                    </div>

                    <!-- Appointments Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="appointmentsTableBody">
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="py-4">
                                            <i class="bi bi-arrow-clockwise text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">Loading appointments...</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4" id="paginationContainer">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Appointment Details Modal -->
<div class="modal fade" id="appointmentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="appointmentDetailsContent">
                <!-- Appointment details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="updateAppointmentStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Appointment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusUpdateForm">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" required>
                            <option value="">Select Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adminNotes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="submitStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Reschedule Appointment Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reschedule Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rescheduleDate" class="form-label">New Date</label>
                                <input type="date" class="form-control" id="rescheduleDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rescheduleTime" class="form-label">New Time</label>
                                <select class="form-select" id="rescheduleTime" required>
                                    <option value="">Select Time</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="09:30">09:30 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="10:30">10:30 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="11:30">11:30 AM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="13:30">01:30 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="14:30">02:30 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="15:30">03:30 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="16:30">04:30 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                    <option value="17:30">05:30 PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="rescheduleNotes" class="form-label">Reschedule Notes</label>
                        <textarea class="form-control" id="rescheduleNotes" rows="3" placeholder="Add notes about the reschedule..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="submitReschedule()">Reschedule Appointment</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAppointmentForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editType" class="form-label">Appointment Type</label>
                                <select class="form-select" id="editType" required>
                                    <option value="donation">Blood Donation</option>
                                    <option value="screening">Screening</option>
                                    <option value="consultation">Consultation</option>
                                    <option value="follow_up">Follow-up</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="editDate" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="editTime" class="form-label">Time</label>
                                <select class="form-select" id="editTime" required>
                                    <option value="">Select Time</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="09:30">09:30 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="10:30">10:30 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="11:30">11:30 AM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="13:30">01:30 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="14:30">02:30 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="15:30">03:30 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="16:30">04:30 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                    <option value="17:30">05:30 PM</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editBloodType" class="form-label">Blood Type (if applicable)</label>
                                <select class="form-select" id="editBloodType">
                                    <option value="">Not Applicable</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="editNotes" rows="3" placeholder="Appointment notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitEditAppointment()">Update Appointment</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.appointment-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.appointment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.stats-card {
    transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
    transform: scale(1.05);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-label {
    font-weight: 500;
    color: #495057;
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};
let currentAppointmentId = null;

function loadAppointments(page = 1) {
    currentPage = page;
    const params = new URLSearchParams({
        page: page,
        ...currentFilters
    });

    console.log('Loading appointments with params:', params.toString());
    console.log('Fetching from:', `/admin/appointments?${params}`);

    fetch(`/admin/appointments?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Appointments data received:', data);
            displayAppointments(data.appointments);
            displayPagination(data.pagination);
        })
        .catch(error => {
            console.error('Error loading appointments:', error);
            console.error('Error details:', error.message);
            showNotification('Error loading appointments: ' + error.message, 'error');
            
            // Show error in table
            document.getElementById('appointmentsTableBody').innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="py-4">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                            <p class="text-danger mt-2">Error loading appointments: ${error.message}</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="loadAppointments()">Retry</button>
                        </div>
                    </td>
                </tr>
            `;
        });
}

function displayAppointments(appointments) {
    const tbody = document.getElementById('appointmentsTableBody');
    tbody.innerHTML = '';

    appointments.forEach(appointment => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${appointment.id}</td>
            <td>${appointment.user ? appointment.user.name : 'N/A'}</td>
            <td>
                <span class="badge bg-${getTypeColor(appointment.appointment_type)}">
                    ${appointment.appointment_type}
                </span>
            </td>
            <td>${formatDate(appointment.appointment_date)}</td>
            <td>${formatTime(appointment.time_slot)}</td>
            <td>
                <span class="badge bg-${getStatusColor(appointment.status)}">
                    ${appointment.status}
                </span>
            </td>
            <td>${formatDate(appointment.created_at)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewAppointment(${appointment.id})" title="View Details">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="editAppointment(${appointment.id})" title="Edit Appointment">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="openStatusUpdate(${appointment.id})" title="Update Status">
                        <i class="bi bi-gear"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="rescheduleAppointment(${appointment.id})" title="Reschedule">
                        <i class="bi bi-calendar-plus"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function displayPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    if (!pagination) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination">';
    
    // Previous button
    if (pagination.prev_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadAppointments(${pagination.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadAppointments(${i})">${i}</a></li>`;
    }
    
    // Next button
    if (pagination.next_page_url) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadAppointments(${pagination.current_page + 1})">Next</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

function applyFilters() {
    currentFilters = {
        status: document.getElementById('statusFilter').value,
        type: document.getElementById('typeFilter').value,
        date: document.getElementById('dateFilter').value,
        search: document.getElementById('searchFilter').value
    };
    loadAppointments(1);
}

function clearFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('typeFilter').value = '';
    document.getElementById('dateFilter').value = '';
    document.getElementById('searchFilter').value = '';
    currentFilters = {};
    loadAppointments(1);
}

function viewAppointment(appointmentId) {
    console.log('Fetching appointment details for ID:', appointmentId);
    
    fetch(`/admin/appointment/${appointmentId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Appointment data received:', data);
            
            document.getElementById('appointmentDetailsContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Patient:</strong> ${data.user ? data.user.name : 'N/A'}</p>
                        <p><strong>Email:</strong> ${data.user ? data.user.email : 'N/A'}</p>
                        <p><strong>Type:</strong> <span class="badge bg-${getTypeColor(data.appointment_type)}">${data.appointment_type}</span></p>
                        <p><strong>Date:</strong> ${formatDate(data.appointment_date)}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Time:</strong> ${formatTime(data.time_slot)}</p>
                        <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(data.status)}">${data.status}</span></p>
                        <p><strong>Created:</strong> ${formatDate(data.created_at)}</p>
                        <p><strong>Blood Type:</strong> ${data.blood_type ? `<span class="badge bg-danger">${data.blood_type}</span>` : 'N/A'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <p><strong>Notes:</strong> ${data.notes || 'N/A'}</p>
                        ${data.admin_notes ? `<p><strong>Admin Notes:</strong> ${data.admin_notes}</p>` : ''}
                    </div>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('appointmentDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error loading appointment details:', error);
            console.error('Error details:', error.message);
            showNotification('Error loading appointment details: ' + error.message, 'error');
        });
}

function openStatusUpdate(appointmentId) {
    currentAppointmentId = appointmentId;
    document.getElementById('newStatus').value = '';
    document.getElementById('adminNotes').value = '';
    new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
}

function submitStatusUpdate() {
    const newStatus = document.getElementById('newStatus').value;
    const adminNotes = document.getElementById('adminNotes').value;

    console.log('Submitting status update:', {
        appointmentId: currentAppointmentId,
        newStatus: newStatus,
        adminNotes: adminNotes
    });

    if (!newStatus) {
        showNotification('Please select a new status', 'error');
        return;
    }

    fetch(`/admin/appointment/${currentAppointmentId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus,
            admin_notes: adminNotes
        })
    })
    .then(response => {
        console.log('Status update response status:', response.status);
        console.log('Status update response headers:', response.headers);
        
        if (!response.ok) {
            // Try to get the response text to see what's being returned
            return response.text().then(text => {
                console.error('Response text:', text);
                throw new Error(`HTTP error! status: ${response.status}. Response: ${text.substring(0, 200)}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Status update response data:', data);
        
        if (data.success) {
            showNotification('Appointment status updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('statusUpdateModal')).hide();
            loadAppointments(currentPage);
        } else {
            showNotification(data.message || 'Failed to update appointment status', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating appointment status:', error);
        console.error('Error details:', error.message);
        showNotification('Error updating appointment status: ' + error.message, 'error');
    });
}

function updateAppointmentStatus() {
    // This function is called from the modal button
    // It will open the status update modal
    if (currentAppointmentId) {
        openStatusUpdate(currentAppointmentId);
    } else {
        showNotification('No appointment selected for status update', 'error');
    }
}

function rescheduleAppointment(appointmentId) {
    currentAppointmentId = appointmentId;
    
    // Load current appointment data
    fetch(`/admin/appointment/${appointmentId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('rescheduleDate').min = today;
            document.getElementById('rescheduleDate').value = data.appointment_date;
            document.getElementById('rescheduleTime').value = data.time_slot;
            document.getElementById('rescheduleNotes').value = '';
            
            new bootstrap.Modal(document.getElementById('rescheduleModal')).show();
        })
        .catch(error => {
            console.error('Error loading appointment for reschedule:', error);
            showNotification('Error loading appointment details: ' + error.message, 'error');
        });
}

function submitReschedule() {
    const newDate = document.getElementById('rescheduleDate').value;
    const newTime = document.getElementById('rescheduleTime').value;
    const notes = document.getElementById('rescheduleNotes').value;

    if (!newDate || !newTime) {
        showNotification('Please select both date and time', 'error');
        return;
    }

    fetch(`/admin/appointment/${currentAppointmentId}/reschedule`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            appointment_date: newDate,
            time_slot: newTime,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Appointment rescheduled successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('rescheduleModal')).hide();
            loadAppointments(currentPage);
        } else {
            showNotification(data.message || 'Failed to reschedule appointment', 'error');
        }
    })
    .catch(error => {
        console.error('Error rescheduling appointment:', error);
        showNotification('Error rescheduling appointment', 'error');
    });
}

function editAppointment(appointmentId) {
    currentAppointmentId = appointmentId;
    
    // Load current appointment data
    fetch(`/admin/appointment/${appointmentId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('editType').value = data.appointment_type;
            document.getElementById('editDate').value = data.appointment_date;
            document.getElementById('editTime').value = data.time_slot;
            document.getElementById('editBloodType').value = data.blood_type || '';
            document.getElementById('editNotes').value = data.notes || '';
            
            new bootstrap.Modal(document.getElementById('editAppointmentModal')).show();
        })
        .catch(error => {
            console.error('Error loading appointment for edit:', error);
            showNotification('Error loading appointment details: ' + error.message, 'error');
        });
}

function submitEditAppointment() {
    const type = document.getElementById('editType').value;
    const date = document.getElementById('editDate').value;
    const time = document.getElementById('editTime').value;
    const bloodType = document.getElementById('editBloodType').value;
    const notes = document.getElementById('editNotes').value;

    if (!type || !date || !time) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    fetch(`/admin/appointment/${currentAppointmentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            appointment_type: type,
            appointment_date: date,
            time_slot: time,
            blood_type: bloodType,
            notes: notes
        })
    })
    .then(response => {
        console.log('Edit appointment response status:', response.status);
        console.log('Edit appointment response headers:', response.headers);
        
        if (!response.ok) {
            // Try to get the response text to see what's being returned
            return response.text().then(text => {
                console.error('Response text:', text);
                throw new Error(`HTTP error! status: ${response.status}. Response: ${text.substring(0, 200)}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Appointment updated successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editAppointmentModal')).hide();
            loadAppointments(currentPage);
        } else {
            showNotification(data.message || 'Failed to update appointment', 'error');
        }
    })
    .catch(error => {
        console.error('Error updating appointment:', error);
        showNotification('Error updating appointment', 'error');
    });
}

function exportAppointments() {
    const params = new URLSearchParams(currentFilters);
    window.open(`{{ route('admin.appointments.export') }}?${params}`, '_blank');
}

function refreshAppointments() {
    loadAppointments(currentPage);
}

function getTypeColor(type) {
    switch(type) {
        case 'donation': return 'success';
        case 'screening': return 'warning';
        case 'consultation': return 'info';
        default: return 'secondary';
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'pending': return 'warning';
        case 'confirmed': return 'info';
        case 'completed': return 'success';
        case 'cancelled': return 'secondary';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    // Handle different date formats
    let date;
    if (typeof dateString === 'string') {
        // If it's already a date string, parse it
        date = new Date(dateString);
    } else if (dateString.date) {
        // If it's a Carbon object from Laravel
        date = new Date(dateString.date);
    } else {
        date = new Date(dateString);
    }
    
    if (isNaN(date.getTime())) return 'Invalid Date';
    
    return date.toLocaleDateString();
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    
    // Handle time format (should be HH:MM format)
    if (typeof timeString === 'string' && timeString.includes(':')) {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }
    
    return timeString;
}

function showNotification(message, type) {
    // Use global notification functions if available, otherwise fallback to console
    if (window.showSuccess && window.showError && window.showWarning && window.showInfo) {
        switch(type) {
            case 'success':
                window.showSuccess(message);
                break;
            case 'error':
                window.showError(message);
                break;
            case 'warning':
                window.showWarning(message);
                break;
            default:
                window.showInfo(message);
        }
    } else {
        console.log(`${type}: ${message}`);
        // Fallback: create a simple alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.container-fluid') || document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
}

// Load appointments on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAppointments();
    loadAppointmentStats();
    
    // Add event listeners for filters
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('typeFilter').addEventListener('change', applyFilters);
    document.getElementById('dateFilter').addEventListener('change', applyFilters);
    document.getElementById('searchFilter').addEventListener('input', function() {
        // Debounce search input to avoid too many requests
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(applyFilters, 500);
    });
});

function loadAppointmentStats() {
    fetch('/admin/appointments/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalAppointments').textContent = data.total || 0;
            document.getElementById('pendingAppointments').textContent = data.pending || 0;
            document.getElementById('confirmedAppointments').textContent = data.confirmed || 0;
            document.getElementById('completedAppointments').textContent = data.completed || 0;
            document.getElementById('cancelledAppointments').textContent = data.cancelled || 0;
            document.getElementById('rejectedAppointments').textContent = data.rejected || 0;
        })
        .catch(error => {
            console.error('Error loading appointment stats:', error);
        });
}


</script>
@endpush
