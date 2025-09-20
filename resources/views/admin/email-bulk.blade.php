@extends('layouts.app')

@section('title', 'Bulk Email Management - Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-envelope me-2"></i>
                        Bulk Email Management
                    </h4>
                    <div>
                        <button class="btn btn-light me-2" onclick="testEmail()">
                            <i class="bi bi-check-circle me-2"></i>
                            Test Email
                        </button>
                        <button class="btn btn-light" onclick="refreshEmailStats()">
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

                    <!-- Email Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Total Sent</h5>
                                    <p class="card-text" id="totalSent">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Successful</h5>
                                    <p class="card-text" id="successfulCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Pending</h5>
                                    <p class="card-text" id="pendingCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Failed</h5>
                                    <p class="card-text" id="failedCount">Loading...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bulk Email Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-send me-2"></i>
                                Send Bulk Email
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="bulkEmailForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emailType" class="form-label">Email Type</label>
                                            <select class="form-select" id="emailType" required>
                                                <option value="">Select Email Type</option>
                                                <option value="announcement">Announcement</option>
                                                <option value="reminder">Reminder</option>
                                                <option value="newsletter">Newsletter</option>
                                                <option value="custom">Custom Message</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="subject" class="form-label">Subject</label>
                                            <input type="text" class="form-control" id="subject" required placeholder="Enter email subject">
                                        </div>
                                        <div class="mb-3">
                                            <label for="recipientGroup" class="form-label">Recipient Group</label>
                                            <select class="form-select" id="recipientGroup" required>
                                                <option value="">Select Recipient Group</option>
                                                <option value="all">All Users</option>
                                                <option value="donors">Blood Donors Only</option>
                                                <option value="requesters">Blood Requesters Only</option>
                                                <option value="admins">Administrators Only</option>
                                                <option value="unverified">Unverified Users</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="message" class="form-label">Message</label>
                                            <textarea class="form-control" id="message" rows="8" required placeholder="Enter your email message..."></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="scheduledDate" class="form-label">Schedule Date (Optional)</label>
                                            <input type="datetime-local" class="form-control" id="scheduledDate">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sendImmediately" checked>
                                            <label class="form-check-label" for="sendImmediately">
                                                Send immediately
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                                        <i class="bi bi-send me-2"></i>
                                        Send Bulk Email
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2" onclick="previewEmail()">
                                        <i class="bi bi-eye me-2"></i>
                                        Preview
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>
                                        Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Email Templates -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-file-text me-2"></i>
                                Email Templates
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Announcement Template</h6>
                                            <p class="card-text small">Use this template for general announcements and updates.</p>
                                            <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate('announcement')">Load Template</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Reminder Template</h6>
                                            <p class="card-text small">Use this template for appointment and donation reminders.</p>
                                            <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate('reminder')">Load Template</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">Newsletter Template</h6>
                                            <p class="card-text small">Use this template for monthly newsletters and updates.</p>
                                            <button class="btn btn-sm btn-outline-primary" onclick="loadTemplate('newsletter')">Load Template</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Email History -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>
                                Recent Email History
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Subject</th>
                                            <th>Recipients</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emailHistoryTable">
                                        <!-- Email history will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Preview Modal -->
<div class="modal fade" id="emailPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>To:</strong> <span id="previewRecipients">Recipients</span>
                </div>
                <div class="mb-3">
                    <strong>Subject:</strong> <span id="previewSubject">Subject</span>
                </div>
                <div class="mb-3">
                    <strong>Message:</strong>
                    <div class="border p-3 mt-2" id="previewMessage">
                        <!-- Email message preview will be displayed here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="sendEmail()">Send Email</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let emailTemplates = {
    announcement: {
        subject: 'Important Announcement - Life Vault',
        message: 'Dear Life Vault Community,\n\nWe have an important announcement to share with you.\n\n[Your announcement content here]\n\nThank you for your continued support.\n\nBest regards,\nLife Vault Team'
    },
    reminder: {
        subject: 'Reminder - Your Upcoming Appointment',
        message: 'Dear [Name],\n\nThis is a friendly reminder about your upcoming appointment.\n\nAppointment Details:\n- Date: [Date]\n- Time: [Time]\n- Type: [Type]\n\nPlease ensure you arrive on time.\n\nBest regards,\nLife Vault Team'
    },
    newsletter: {
        subject: 'Life Vault Newsletter - [Month] [Year]',
        message: 'Dear Life Vault Community,\n\nWelcome to our monthly newsletter!\n\n[Your newsletter content here]\n\nStay connected with us for more updates.\n\nBest regards,\nLife Vault Team'
    }
};

function loadEmailStats() {
    fetch('/admin/email/stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalSent').textContent = data.total_sent || 0;
            document.getElementById('successfulCount').textContent = data.successful || 0;
            document.getElementById('pendingCount').textContent = data.pending || 0;
            document.getElementById('failedCount').textContent = data.failed || 0;
        })
        .catch(error => {
            console.error('Error loading email stats:', error);
        });
}

function loadEmailHistory() {
    fetch('/admin/email/history')
        .then(response => response.json())
        .then(data => {
            displayEmailHistory(data.emails);
        })
        .catch(error => {
            console.error('Error loading email history:', error);
        });
}

function displayEmailHistory(emails) {
    const tbody = document.getElementById('emailHistoryTable');
    tbody.innerHTML = '';

    emails.forEach(email => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${formatDate(email.created_at)}</td>
            <td>
                <span class="badge bg-${getEmailTypeColor(email.type)}">
                    ${email.type}
                </span>
            </td>
            <td>${email.subject}</td>
            <td>${email.recipient_count || 0}</td>
            <td>
                <span class="badge bg-${getEmailStatusColor(email.status)}">
                    ${email.status}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewEmailDetails(${email.id})" title="View Details">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="resendEmail(${email.id})" title="Resend">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function loadTemplate(templateType) {
    if (emailTemplates[templateType]) {
        const template = emailTemplates[templateType];
        document.getElementById('emailType').value = templateType;
        document.getElementById('subject').value = template.subject;
        document.getElementById('message').value = template.message;
        showNotification('Template loaded successfully', 'success');
    }
}

function previewEmail() {
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;
    const recipientGroup = document.getElementById('recipientGroup').value;

    if (!subject || !message || !recipientGroup) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    document.getElementById('previewRecipients').textContent = getRecipientGroupText(recipientGroup);
    document.getElementById('previewSubject').textContent = subject;
    document.getElementById('previewMessage').innerHTML = message.replace(/\n/g, '<br>');

    new bootstrap.Modal(document.getElementById('emailPreviewModal')).show();
}

function sendEmail() {
    const formData = {
        email_type: document.getElementById('emailType').value,
        subject: document.getElementById('subject').value,
        message: document.getElementById('message').value,
        recipient_group: document.getElementById('recipientGroup').value,
        scheduled_date: document.getElementById('scheduledDate').value,
        send_immediately: document.getElementById('sendImmediately').checked
    };

    if (!formData.subject || !formData.message || !formData.recipient_group) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    // Disable send button and show loading state
    const sendBtn = document.getElementById('sendEmailBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
    sendBtn.disabled = true;

    fetch('/admin/email/bulk', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Bulk email sent successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('emailPreviewModal')).hide();
            resetForm();
            loadEmailStats();
            loadEmailHistory();
        } else {
            showNotification('Failed to send bulk email', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending bulk email:', error);
        showNotification('Error sending bulk email', 'error');
    })
    .finally(() => {
        // Re-enable send button
        sendBtn.innerHTML = originalText;
        sendBtn.disabled = false;
    });
}

function testEmail() {
    // Implementation for testing email functionality
    showNotification('Test email feature coming soon', 'info');
}

function resetForm() {
    document.getElementById('bulkEmailForm').reset();
    document.getElementById('emailType').value = '';
    document.getElementById('subject').value = '';
    document.getElementById('message').value = '';
    document.getElementById('recipientGroup').value = '';
    document.getElementById('scheduledDate').value = '';
    document.getElementById('sendImmediately').checked = true;
}

function refreshEmailStats() {
    loadEmailStats();
    loadEmailHistory();
}

function getRecipientGroupText(group) {
    const groupTexts = {
        'all': 'All Users',
        'donors': 'Blood Donors Only',
        'requesters': 'Blood Requesters Only',
        'admins': 'Administrators Only',
        'unverified': 'Unverified Users'
    };
    return groupTexts[group] || group;
}

function getEmailTypeColor(type) {
    switch(type) {
        case 'announcement': return 'primary';
        case 'reminder': return 'warning';
        case 'newsletter': return 'info';
        case 'custom': return 'secondary';
        default: return 'secondary';
    }
}

function getEmailStatusColor(status) {
    switch(status) {
        case 'sent': return 'success';
        case 'pending': return 'warning';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
}

function showNotification(message, type) {
    // Implementation depends on your notification system
    console.log(`${type}: ${message}`);
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadEmailStats();
    loadEmailHistory();
});

// Form submission handler
document.getElementById('bulkEmailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    previewEmail();
});
</script>
@endpush
