// ─── CONFIG ───────────────────────────────────────────────────────────────────
// All task CRUD goes through Laravel's web routes via fetch()
// The route names map to TaskController methods
const ROUTES = {
    index:   '/',                // GET  → TaskController::index()
    store:   '/tasks',           // POST → TaskController::store()
    update:  (id) => `/tasks/${id}`,   // PUT  → TaskController::update()
    destroy: (id) => `/tasks/${id}`,   // DELETE → TaskController::destroy()
    toggle:  (id) => `/tasks/${id}/toggle`, // PATCH → TaskController::toggleStatus()
};

// ─── STATE ────────────────────────────────────────────────────────────────────
const state = {
    tasks: [],          // full list of tasks currently displayed
    editingTaskId: null // null = adding mode, number = editing mode
};

// ─── ELEMENT REFERENCES ───────────────────────────────────────────────────────
const el = {
    taskList:        () => document.getElementById('task-list-content'),
    taskMessage:     () => document.getElementById('task-message'),
    formMessage:     () => document.getElementById('form-message'),
    form:            () => document.getElementById('task-form-element'),
    formHeading:     () => document.getElementById('task-form-heading'),
    submitBtn:       () => document.getElementById('submit-task-btn'),
    cancelBtn:       () => document.getElementById('cancel-edit-btn'),
    taskId:          () => document.getElementById('task-id'),
    title:           () => document.getElementById('title'),
    description:     () => document.getElementById('description'),
    priority:        () => document.getElementById('task-priority'),
    status:          () => document.getElementById('task-status'),
    dueDate:         () => document.getElementById('due-date'),
    search:          () => document.getElementById('search-task'),
    filterPriority:  () => document.getElementById('filter-priority'),
    filterStatus:    () => document.getElementById('filter-status'),
};

// ─── CSRF TOKEN ───────────────────────────────────────────────────────────────
// Laravel requires this token on every POST/PUT/PATCH/DELETE request
// It reads from the <meta name="csrf-token"> tag in your Blade layout
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// ─── BOOT ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    bindEvents();
    loadTasks(); // load tasks immediately when the page opens
});

// ─── EVENT BINDING ────────────────────────────────────────────────────────────
function bindEvents() {
    // Form submit — intercept and send via fetch() instead of page reload
    const form = el.form();
    if (form) form.addEventListener('submit', handleFormSubmit);

    // Cancel edit button
    const cancelBtn = el.cancelBtn();
    if (cancelBtn) cancelBtn.addEventListener('click', resetForm);

    // Task list — delegate clicks for edit/delete/toggle buttons
    const taskList = el.taskList();
    if (taskList) taskList.addEventListener('click', handleTaskAction);

    // Filters — debounced so typing doesn't fire on every keystroke
    const search = el.search();
    if (search) {
        let debounceTimer;
        search.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            // Wait 400ms after user stops typing before loading tasks
            debounceTimer = setTimeout(loadTasks, 400);
        });
    }

    // Priority and status dropdowns — fire immediately on change
    [el.filterPriority(), el.filterStatus()].forEach(select => {
        if (select) select.addEventListener('change', loadTasks);
    });
}

// ─── LOAD TASKS (replaces full page reload for filtering) ─────────────────────
// Sends a GET request to / with query params for filters
// Laravel's index() returns the full HTML page but we only extract the task data
// Actually — we call a JSON endpoint. We handle this by adding ?json=1
// which makes index() return JSON instead of a view when detected.
//
// SIMPLER APPROACH: We keep the Blade rendering but fetch tasks as JSON
// by hitting a dedicated JSON route. Since we only have web routes,
// we use fetch() with an Accept: application/json header — Laravel
// detects this and we handle it in the controller.
//
// EVEN SIMPLER (zero controller changes): We re-render tasks client-side
// using the same data shape the original SPA used, by sending POST to
// a lightweight endpoint. BUT — we already have all routes.
//
// ACTUAL APPROACH USED HERE:
// Send GET / with ?search=&priority=&status= AND Accept: application/json
// In TaskController::index(), detect JSON request and return json($tasks)
// This requires ONE small addition to TaskController — detailed below.

async function loadTasks() {
    const search   = el.search()?.value.trim()   || '';
    const priority = el.filterPriority()?.value  || '';
    const status   = el.filterStatus()?.value    || '';

    // Build query string
    const params = new URLSearchParams();
    if (search)   params.set('search',   search);
    if (priority) params.set('priority', priority);
    if (status)   params.set('status',   status);

    showMessage(el.taskList(), '<div class="empty-state">Loading tasks...</div>', true);

    try {
        // Send GET request with Accept: application/json header
        // TaskController::index() will return JSON when it detects this header
        const response = await fetch('/?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!response.ok) throw new Error('Server error: ' + response.status);

        const result = await response.json();
        state.tasks = Array.isArray(result) ? result : [];
        renderTasks();

        if (state.tasks.length === 0) {
            showMessage(el.taskMessage(), 'No tasks match the current filters.', false, 'info');
        } else {
            clearMessage(el.taskMessage());
        }

    } catch (err) {
        showMessage(el.taskList(), '<div class="empty-state error-state">Unable to load tasks.</div>', true);
        showMessage(el.taskMessage(), 'Could not load tasks. ' + err.message, false, 'error');
    }
}

// ─── RENDER TASKS (client-side, no page reload) ───────────────────────────────
function renderTasks() {
    const list = el.taskList();
    if (!list) return;

    if (state.tasks.length === 0) {
        list.innerHTML = '<div class="empty-state">No tasks found. Add one below to get started.</div>';
        return;
    }

    list.innerHTML = state.tasks.map(task => `
        <article class="task-card">
            <div class="task-card-top">
                <div>
                    <p class="id">Task ID: #${esc(task.id)}</p>
                    <h3 class="title">${esc(task.title)}</h3>
                </div>
                <span class="status-badge ${esc(task.status)}">${cap(task.status)}</span>
            </div>

            <p class="description">${esc(task.description || 'No description provided.')}</p>

            <div class="task-meta">
                <div class="meta-item">
                    <span class="meta-label">Priority</span>
                    <span class="priority-badge ${esc(task.priority)}">${cap(task.priority)}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Created At</span>
                    <span class="meta-value">${formatDate(task.created_at)}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Due Date</span>
                    <span class="meta-value">${task.due_date ? formatDate(task.due_date) : 'No due date'}</span>
                </div>
            </div>

            <div class="task-actions">
                <button class="btn btn-edit"      data-action="edit"          data-id="${esc(task.id)}">Edit</button>
                <button class="btn btn-secondary" data-action="toggle"        data-id="${esc(task.id)}">
                    ${task.status === 'completed' ? 'Mark Pending' : 'Mark Complete'}
                </button>
                <button class="btn btn-delete"    data-action="delete"        data-id="${esc(task.id)}">Delete</button>
            </div>
        </article>
    `).join('');
}

// ─── HANDLE TASK CARD BUTTON CLICKS ───────────────────────────────────────────
async function handleTaskAction(event) {
    const button = event.target.closest('button[data-action]');
    if (!button) return;

    const { action, id } = button.dataset;
    const task = state.tasks.find(t => String(t.id) === String(id));
    if (!task) { showMessage(el.taskMessage(), 'Task not found.', false, 'error'); return; }

    if (action === 'edit') {
        populateForm(task);
        return;
    }

    if (action === 'delete') {
        if (!confirm(`Delete "${task.title}"?`)) return;

        const ok = await sendRequest(ROUTES.destroy(id), 'DELETE');
        if (ok) {
            showMessage(el.taskMessage(), 'Task deleted successfully.', false, 'success');
            if (String(state.editingTaskId) === String(id)) resetForm();
            await loadTasks();
        }
        return;
    }

    if (action === 'toggle') {
        const ok = await sendRequest(ROUTES.toggle(id), 'PATCH');
        if (ok) {
            showMessage(el.taskMessage(), `Task marked as ${task.status === 'completed' ? 'Pending' : 'Completed'}.`, false, 'success');
            await loadTasks();
        }
    }
}

// ─── FORM SUBMIT — ADD OR EDIT ─────────────────────────────────────────────────
async function handleFormSubmit(event) {
    event.preventDefault(); // stop browser from reloading the page

    const validationError = validateForm();
    if (validationError) {
        showMessage(el.formMessage(), validationError, false, 'error');
        return;
    }

    const isEditing = Boolean(state.editingTaskId);
    const url    = isEditing ? ROUTES.update(state.editingTaskId) : ROUTES.store;
    const method = isEditing ? 'PUT' : 'POST';

    const body = {
        title:       el.title()?.value.trim(),
        description: el.description()?.value.trim(),
        priority:    el.priority()?.value,
        status:      el.status()?.value,
        due_date:    el.dueDate()?.value || '',
    };

    const ok = await sendRequest(url, method, body);
    if (ok) {
        showMessage(el.formMessage(), isEditing ? 'Task updated successfully.' : 'Task added successfully.', false, 'success');
        resetForm();
        await loadTasks();
    }
}

// ─── CORE FETCH HELPER ────────────────────────────────────────────────────────
// Sends any HTTP request to any Laravel route and handles errors centrally
async function sendRequest(url, method, body = null) {
    try {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN':     getCsrfToken(), // required by Laravel for all non-GET
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type':     'application/json',
            },
        };

        if (body) options.body = JSON.stringify(body);

        const response = await fetch(url, options);
        const data     = await response.json();

        // Laravel returns validation errors as 422 with an 'errors' object
        if (response.status === 422 && data.errors) {
            // Flatten all error messages into one string
            const messages = Object.values(data.errors).flat().join(' ');
            showMessage(el.formMessage(), messages, false, 'error');
            return false;
        }

        if (!response.ok) {
            showMessage(el.formMessage(), data.message || 'Operation failed.', false, 'error');
            return false;
        }

        return true;

    } catch (err) {
        showMessage(el.formMessage(), 'Network error. Please try again.', false, 'error');
        return false;
    }
}

// ─── FORM HELPERS ─────────────────────────────────────────────────────────────
function populateForm(task) {
    state.editingTaskId     = String(task.id);
    el.taskId().value       = task.id;
    el.title().value        = task.title       || '';
    el.description().value  = task.description || '';
    el.priority().value     = task.priority    || '';
    el.status().value       = task.status      || 'pending';
    el.dueDate().value      = task.due_date    || '';

    el.formHeading().textContent = 'Edit Task';
    el.submitBtn().textContent   = 'Save Changes';
    el.cancelBtn().classList.remove('hidden');
    showMessage(el.formMessage(), `Editing task #${task.id}.`, false, 'info');
    el.form().scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function resetForm() {
    state.editingTaskId = null;
    el.form().reset();
    el.taskId().value            = '';
    el.formHeading().textContent = 'Add Task';
    el.submitBtn().textContent   = 'Add Task';
    el.cancelBtn().classList.add('hidden');
    clearMessage(el.formMessage());
}

// ─── CLIENT-SIDE VALIDATION ───────────────────────────────────────────────────
function validateForm() {
    const title    = el.title()?.value.trim()   || '';
    const priority = el.priority()?.value       || '';
    const status   = el.status()?.value         || '';
    const dueDate  = el.dueDate()?.value        || '';

    if (title.length < 3) return 'Title must be at least 3 characters long.';

    // Duplicate title check (only among tasks that are NOT the one being edited)
    const duplicate = state.tasks.find(t =>
        String(t.id) !== String(state.editingTaskId) &&
        t.title.trim().toLowerCase() === title.toLowerCase()
    );
    if (duplicate) return 'A task with this title already exists.';

    if (!['low', 'medium', 'high'].includes(priority)) return 'Please choose a valid priority.';
    if (!['pending', 'completed'].includes(status))     return 'Please choose a valid status.';

    if (dueDate) {
        const today    = new Date(); today.setHours(0, 0, 0, 0);
        const selected = new Date(dueDate + 'T00:00:00');
        if (selected < today) return 'Due date cannot be in the past.';
    }

    return ''; // empty string = no error
}

// ─── UI UTILITIES ─────────────────────────────────────────────────────────────
function showMessage(element, message, isHtml = false, type = '') {
    if (!element) return;
    if (isHtml) { element.innerHTML  = message; }
    else        { element.textContent = message; }
    if (type) element.className = `feedback-message ${type}`;
}

function clearMessage(element) {
    if (!element) return;
    element.textContent = '';
    element.className   = 'feedback-message';
}

function esc(value) {
    return String(value)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function cap(value) {
    return value ? value.charAt(0).toUpperCase() + value.slice(1) : '';
}

function formatDate(value) {
    const date = new Date(value);
    if (isNaN(date.getTime())) return value;
    return date.toLocaleDateString('en-CA'); // YYYY-MM-DD format
}