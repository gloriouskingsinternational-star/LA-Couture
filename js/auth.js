// L.A. Couture Authentication & Data Management System
// localStorage-based system for admin and client management

const AUTH_KEYS = {
  CURRENT_USER: 'la_couture_current_user',
  ADMINS: 'la_couture_admins',
  CLIENTS: 'la_couture_clients',
  ENQUIRIES: 'la_couture_enquiries',
  CONTACT_FORMS: 'la_couture_contact_forms',
  MESSAGES: 'la_couture_messages'
};

const ADMIN_CREDENTIALS = {
  email: 'admin@lacouture.com',
  password: 'admin123'
};

class AuthSystem {
  // Initialize localStorage with default data
  static init() {
    if (!localStorage.getItem(AUTH_KEYS.ADMINS)) {
      localStorage.setItem(AUTH_KEYS.ADMINS, JSON.stringify([ADMIN_CREDENTIALS]));
    }
    if (!localStorage.getItem(AUTH_KEYS.CLIENTS)) {
      localStorage.setItem(AUTH_KEYS.CLIENTS, JSON.stringify([]));
    }
    if (!localStorage.getItem(AUTH_KEYS.ENQUIRIES)) {
      localStorage.setItem(AUTH_KEYS.ENQUIRIES, JSON.stringify([]));
    }
    if (!localStorage.getItem(AUTH_KEYS.CONTACT_FORMS)) {
      localStorage.setItem(AUTH_KEYS.CONTACT_FORMS, JSON.stringify([]));
    }
    if (!localStorage.getItem(AUTH_KEYS.MESSAGES)) {
      localStorage.setItem(AUTH_KEYS.MESSAGES, JSON.stringify([]));
    }
  }

  // Admin Login
  static adminLogin(email, password) {
    const admins = JSON.parse(localStorage.getItem(AUTH_KEYS.ADMINS) || '[]');
    const admin = admins.find(a => a.email === email && a.password === password);
    
    if (admin) {
      const user = { ...admin, role: 'admin', loginTime: new Date().toISOString() };
      localStorage.setItem(AUTH_KEYS.CURRENT_USER, JSON.stringify(user));
      return { success: true, user };
    }
    return { success: false, error: 'Invalid credentials' };
  }

  // Client Registration
  static clientRegister(clientData) {
    const clients = JSON.parse(localStorage.getItem(AUTH_KEYS.CLIENTS) || '[]');
    
    // Check if email already exists
    if (clients.find(c => c.email === clientData.email)) {
      return { success: false, error: 'Email already registered' };
    }

    const newClient = {
      id: 'client_' + Date.now(),
      ...clientData,
      status: 'pending', // pending approval
      registeredAt: new Date().toISOString(),
      registeredBy: 'self-registration'
    };

    clients.push(newClient);
    localStorage.setItem(AUTH_KEYS.CLIENTS, JSON.stringify(clients));
    
    return { success: true, client: newClient, message: 'Registration pending admin approval' };
  }

  // Client Login
  static clientLogin(email, password) {
    const clients = JSON.parse(localStorage.getItem(AUTH_KEYS.CLIENTS) || '[]');
    const client = clients.find(c => c.email === email && c.password === password);
    
    if (!client) {
      return { success: false, error: 'Invalid credentials' };
    }

    if (client.status !== 'approved') {
      return { success: false, error: 'Your account is pending admin approval' };
    }

    const user = { ...client, role: 'client', loginTime: new Date().toISOString() };
    localStorage.setItem(AUTH_KEYS.CURRENT_USER, JSON.stringify(user));
    return { success: true, user };
  }

  // Logout
  static logout() {
    localStorage.removeItem(AUTH_KEYS.CURRENT_USER);
    window.location.href = 'index.html';
  }

  // Get current logged-in user
  static getCurrentUser() {
    const user = localStorage.getItem(AUTH_KEYS.CURRENT_USER);
    return user ? JSON.parse(user) : null;
  }

  // Check if user is authenticated
  static isAuthenticated() {
    return this.getCurrentUser() !== null;
  }

  // Add enquiry
  static addEnquiry(enquiryData) {
    const enquiries = JSON.parse(localStorage.getItem(AUTH_KEYS.ENQUIRIES) || '[]');
    const newEnquiry = {
      id: 'enq_' + Date.now(),
      ...enquiryData,
      status: 'open', // open, in-progress, closed
      createdAt: new Date().toISOString(),
      messages: []
    };
    enquiries.push(newEnquiry);
    localStorage.setItem(AUTH_KEYS.ENQUIRIES, JSON.stringify(enquiries));
    return newEnquiry;
  }

  // Add contact form submission
  static addContactForm(formData) {
    const forms = JSON.parse(localStorage.getItem(AUTH_KEYS.CONTACT_FORMS) || '[]');
    const newForm = {
      id: 'form_' + Date.now(),
      ...formData,
      status: 'received', // received, reviewed, responded
      createdAt: new Date().toISOString()
    };
    forms.push(newForm);
    localStorage.setItem(AUTH_KEYS.CONTACT_FORMS, JSON.stringify(forms));
    return newForm;
  }

  // Send message
  static sendMessage(enquiryId, message, sender) {
    const enquiries = JSON.parse(localStorage.getItem(AUTH_KEYS.ENQUIRIES) || '[]');
    const enquiry = enquiries.find(e => e.id === enquiryId);
    
    if (!enquiry) return null;

    const newMessage = {
      id: 'msg_' + Date.now(),
      enquiryId,
      sender,
      text: message,
      timestamp: new Date().toISOString(),
      read: false
    };

    enquiry.messages.push(newMessage);
    localStorage.setItem(AUTH_KEYS.ENQUIRIES, JSON.stringify(enquiries));
    return newMessage;
  }

  // Get all clients (admin only)
  static getAllClients() {
    return JSON.parse(localStorage.getItem(AUTH_KEYS.CLIENTS) || '[]');
  }

  // Get all enquiries (admin only)
  static getAllEnquiries() {
    return JSON.parse(localStorage.getItem(AUTH_KEYS.ENQUIRIES) || '[]');
  }

  // Get all contact forms (admin only)
  static getAllContactForms() {
    return JSON.parse(localStorage.getItem(AUTH_KEYS.CONTACT_FORMS) || '[]');
  }

  // Get client enquiries
  static getClientEnquiries(clientId) {
    const enquiries = JSON.parse(localStorage.getItem(AUTH_KEYS.ENQUIRIES) || '[]');
    return enquiries.filter(e => e.clientId === clientId);
  }

  // Approve client registration
  static approveClient(clientId) {
    const clients = JSON.parse(localStorage.getItem(AUTH_KEYS.CLIENTS) || '[]');
    const client = clients.find(c => c.id === clientId);
    
    if (client) {
      client.status = 'approved';
      client.approvedAt = new Date().toISOString();
      localStorage.setItem(AUTH_KEYS.CLIENTS, JSON.stringify(clients));
      return client;
    }
    return null;
  }

  // Reject client registration
  static rejectClient(clientId, reason) {
    const clients = JSON.parse(localStorage.getItem(AUTH_KEYS.CLIENTS) || '[]');
    const client = clients.find(c => c.id === clientId);
    
    if (client) {
      client.status = 'rejected';
      client.rejectionReason = reason;
      client.rejectedAt = new Date().toISOString();
      localStorage.setItem(AUTH_KEYS.CLIENTS, JSON.stringify(clients));
      return client;
    }
    return null;
  }

  // Update enquiry status
  static updateEnquiryStatus(enquiryId, newStatus) {
    const enquiries = JSON.parse(localStorage.getItem(AUTH_KEYS.ENQUIRIES) || '[]');
    const enquiry = enquiries.find(e => e.id === enquiryId);
    
    if (enquiry) {
      enquiry.status = newStatus;
      enquiry.statusUpdatedAt = new Date().toISOString();
      localStorage.setItem(AUTH_KEYS.ENQUIRIES, JSON.stringify(enquiries));
      return enquiry;
    }
    return null;
  }

  // Format date for display
  static formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
  }
}

// Initialize system on page load
document.addEventListener('DOMContentLoaded', () => {
  AuthSystem.init();
});
