/**
 * VisoApp — Bordio SaaS Frontend Controller
 * Handles AJAX, drag-and-drop, slide-over modal, assignees, notes, and client-side UI.
 */
const VisoApp = (function ($) {
    'use strict';

    // ========================
    //  CONFIG
    // ========================
    const API = '/api';
    const CSRF = () => $('meta[name="csrf-token"]').attr('content');

    const headers = () => ({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': CSRF(),
    });

    let activeTaskId = null;
    let activeNoteId = null;
    let dragTaskId = null;
    let allUsers = [];

    // ========================
    //  AJAX HELPERS
    // ========================
    function apiGet(url) {
        return $.ajax({ url: API + url, headers: headers() });
    }
    function apiPost(url, data) {
        return $.ajax({ url: API + url, method: 'POST', headers: headers(), data: JSON.stringify(data) });
    }
    function apiPut(url, data) {
        return $.ajax({ url: API + url, method: 'PUT', headers: headers(), data: JSON.stringify(data) });
    }
    function apiDelete(url) {
        return $.ajax({ url: API + url, method: 'DELETE', headers: headers() });
    }

    function reload() { window.location.reload(); }

    function toast(msg, type = 'success') {
        const bg = type === 'success' ? 'linear-gradient(135deg, #22c55e, #16a34a)' : 'linear-gradient(135deg, #ef4444, #dc2626)';
        const el = $(`<div class="position-fixed bottom-0 end-0 m-4 px-4 py-3 rounded-3 shadow-lg text-white fw-medium small d-flex align-items-center gap-2" style="z-index:9999;animation:visoSlideUp .3s ease;background:${bg}">
            <i class="icon-${type === 'success' ? 'check-circle' : 'alert-circle'}" style="font-size:16px"></i>
            ${msg}
        </div>`);
        $('body').append(el);
        setTimeout(() => el.fadeOut(300, () => el.remove()), 3000);
    }

    // ========================
    //  TASK CRUD
    // ========================
    function addTask(title, projectId) {
        const data = {
            title: title.trim(),
            status: 'Todo',
            priority: 'Normal',
            time_estimate: 30,
        };
        if (projectId) data.project_id = projectId;

        apiPost('/tasks', data).done(() => {
            toast('Task created');
            reload();
        }).fail(() => toast('Failed to create task', 'danger'));
    }

    function deleteTask(id) {
        if (!confirm('Delete this task?')) return;
        apiDelete('/tasks/' + id).done(() => {
            toast('Task deleted');
            closeTaskModal();
            reload();
        });
    }

    function duplicateTask(id) {
        apiPost('/tasks/' + id + '/duplicate', {}).done(() => {
            toast('Task duplicated');
            reload();
        });
    }

    function updateTaskField(field, value) {
        if (!activeTaskId) return;
        const data = {};
        data[field] = value;
        apiPut('/tasks/' + activeTaskId, data).done(() => {
            // Subtle feedback without toast for field updates
        });
    }

    function getActiveTaskId() {
        return activeTaskId;
    }

    // Quick Add from modal
    function quickAddTask() {
        const title = $('#quickTaskTitle').val();
        if (!title || !title.trim()) return;

        const data = {
            title: title.trim(),
            status: 'Todo',
            priority: $('#quickTaskPriority').val() || 'Normal',
            time_estimate: 30,
        };

        const projectId = $('#quickTaskProject').val();
        if (projectId) data.project_id = parseInt(projectId);

        const dueDate = $('#quickTaskDue').val();
        if (dueDate) data.due_date = dueDate;

        const assigneeIds = [];
        $('#quickTaskAssignee option:selected').each(function () {
            assigneeIds.push(parseInt($(this).val()));
        });
        if (assigneeIds.length) data.assignee_ids = assigneeIds;

        apiPost('/tasks', data).done(() => {
            toast('Task created');
            $('#quickAddTaskModal').modal('hide');
            reload();
        }).fail(() => toast('Failed to create task', 'danger'));
    }

    // Add task from project view modal
    function addProjectTask(projectId) {
        const title = $('#projectTaskTitle').val();
        if (!title || !title.trim()) return;

        const data = {
            title: title.trim(),
            project_id: projectId,
            status: 'Todo',
            priority: $('#projectTaskPriority').val() || 'Normal',
            time_estimate: 30,
        };

        const dueDate = $('#projectTaskDue').val();
        if (dueDate) data.due_date = dueDate;

        apiPost('/tasks', data).done(() => {
            toast('Task created');
            $('#addProjectTaskModal').modal('hide');
            reload();
        }).fail(() => toast('Failed to create task', 'danger'));
    }

    // ========================
    //  TASK MODAL (SLIDE-OVER)
    // ========================
    function openTaskModal(id) {
        activeTaskId = id;
        apiGet('/tasks/' + id).done(function (task) {
            // Header
            $('#taskModalProject').text(task.project ? task.project.name : 'Personal');
            $('#taskModalTitle').text(task.title);
            $('#taskDetailTitle').text(task.title);

            // Fields
            $('#taskStatusSelect').val(task.status);
            $('#taskPrioritySelect').val(task.priority);
            $('#taskRecurrenceSelect').val(task.recurrence || 'none');
            $('#taskDueDateInput').val(task.due_date ? task.due_date.split('T')[0] : '');
            $('#taskTimeEstimate').val(task.time_estimate || 30);

            // Description
            const descEl = document.getElementById('taskDescription');
            if (descEl) {
                descEl.innerHTML = task.description || '<p class="text-muted small mb-0">Click to add description...</p>';
            }

            // Assignees
            renderAssignees(task.assignees || []);

            // Subtasks
            renderSubtasks(task.subtasks || []);

            // Chat
            renderChat(task.chat_messages || []);

            // Show modal
            $('#taskBackdrop').addClass('show');
            $('#taskSlideOver').addClass('show');
            $('body').css('overflow', 'hidden');
        });
    }

    function closeTaskModal() {
        $('#taskBackdrop').removeClass('show');
        $('#taskSlideOver').removeClass('show');
        $('body').css('overflow', '');
        activeTaskId = null;
    }

    // ========================
    //  ASSIGNEES
    // ========================
    function renderAssignees(assignees) {
        const $list = $('#taskAssigneeList').empty();
        assignees.forEach(function (user) {
            const avatarUrl = user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=24&background=3b82f6&color=fff';
            $list.append(`
                <div class="viso-assignee-chip">
                    <img src="${avatarUrl}" class="rounded-circle" width="22" height="22">
                    <span>${user.name}</span>
                    <span class="remove-btn" onclick="VisoApp.removeAssignee(${user.id})" title="Remove">
                        <i class="icon-x" style="font-size:12px"></i>
                    </span>
                </div>
            `);
        });

        // Populate dropdown
        loadUsersForDropdown(assignees.map(a => a.id));
    }

    function loadUsersForDropdown(currentIds) {
        if (allUsers.length > 0) {
            populateAssigneeDropdown(currentIds);
            return;
        }
        apiGet('/users').done(function (users) {
            allUsers = users;
            populateAssigneeDropdown(currentIds);
        });
    }

    function populateAssigneeDropdown(currentIds) {
        const $dropdown = $('#assigneeDropdown').empty();
        allUsers.forEach(function (user) {
            const isAssigned = currentIds.includes(user.id);
            const avatarUrl = user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&size=24&background=3b82f6&color=fff';
            $dropdown.append(`
                <div class="d-flex align-items-center gap-2 p-2 rounded cursor-pointer hover-bg-light transition-all ${isAssigned ? 'opacity-50' : ''}"
                     onclick="${isAssigned ? '' : 'VisoApp.addAssignee(' + user.id + ')'}">
                    <img src="${avatarUrl}" class="rounded-circle" width="24" height="24">
                    <span class="small fw-medium">${user.name}</span>
                    ${isAssigned ? '<i class="icon-check text-success ms-auto" style="font-size:14px"></i>' : ''}
                </div>
            `);
        });
    }

    function addAssignee(userId) {
        if (!activeTaskId) return;
        // Get current assignees and add new one
        apiGet('/tasks/' + activeTaskId).done(function (task) {
            const ids = (task.assignees || []).map(a => a.id);
            if (!ids.includes(userId)) ids.push(userId);
            apiPost('/tasks/' + activeTaskId + '/assignees', { assignee_ids: ids }).done(function () {
                openTaskModal(activeTaskId);
                toast('Assignee added');
            });
        });
    }

    function removeAssignee(userId) {
        if (!activeTaskId) return;
        apiGet('/tasks/' + activeTaskId).done(function (task) {
            const ids = (task.assignees || []).map(a => a.id).filter(id => id !== userId);
            apiPost('/tasks/' + activeTaskId + '/assignees', { assignee_ids: ids }).done(function () {
                openTaskModal(activeTaskId);
            });
        });
    }

    // ========================
    //  SUBTASKS
    // ========================
    function renderSubtasks(subtasks) {
        const total = subtasks.length;
        const done = subtasks.filter(s => s.completed).length;
        const pct = total > 0 ? Math.round((done / total) * 100) : 0;

        $('#subtaskCount').text(done + '/' + total);
        $('#subtaskProgress').css('width', pct + '%');

        const $list = $('#subtaskList').empty();
        subtasks.forEach(function (s) {
            $list.append(`
                <div class="d-flex align-items-center gap-3 p-2 rounded hover-bg-light transition-all">
                    <input type="checkbox" class="form-check-input mt-0 cursor-pointer"
                           ${s.completed ? 'checked' : ''}
                           onchange="VisoApp.toggleSubtask(${s.id})">
                    <span class="small flex-grow-1 ${s.completed ? 'text-muted text-decoration-line-through' : 'text-dark'}">${s.title}</span>
                    <button onclick="VisoApp.deleteSubtask(${s.id})" class="btn btn-sm btn-link text-muted p-0" style="opacity:0.4">
                        <i class="icon-trash-2" style="font-size:12px"></i>
                    </button>
                </div>
            `);
        });
    }

    function addSubtask(title) {
        if (!title.trim() || !activeTaskId) return;
        apiPost('/tasks/' + activeTaskId + '/subtasks', { title: title.trim() }).done(function () {
            openTaskModal(activeTaskId);
        });
    }

    function toggleSubtask(subtaskId) {
        if (!activeTaskId) return;
        apiPut('/tasks/' + activeTaskId + '/subtasks/' + subtaskId, {}).done(function () {
            openTaskModal(activeTaskId);
        });
    }

    function deleteSubtask(subtaskId) {
        if (!activeTaskId) return;
        apiDelete('/tasks/' + activeTaskId + '/subtasks/' + subtaskId).done(function () {
            openTaskModal(activeTaskId);
        });
    }

    // ========================
    //  CHAT / COMMENTS
    // ========================
    function renderChat(messages) {
        const $chat = $('#chatMessages').empty();
        if (messages.length === 0) {
            $chat.append('<div class="text-center text-muted small py-5 fst-italic"><i class="icon-message-square d-block mb-2" style="font-size:24px;opacity:0.3"></i>No comments yet</div>');
        }
        messages.forEach(function (msg) {
            const avatar = msg.user ? msg.user.avatar : '';
            const name = msg.user ? msg.user.name : 'User';
            const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            $chat.append(`
                <div class="viso-fade-in">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="${avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&size=24&background=3b82f6&color=fff'}"
                             class="rounded-circle" width="24" height="24">
                        <span class="small fw-bold text-dark">${name}</span>
                        <span class="text-muted fs-10">${time}</span>
                    </div>
                    <div class="ms-4 small text-dark bg-light p-2 rounded-2">
                        ${msg.content}
                    </div>
                </div>
            `);
        });
        const chatEl = document.getElementById('chatMessages');
        if (chatEl) chatEl.scrollTop = chatEl.scrollHeight;
    }

    function sendChatMessage() {
        const content = $('#chatInput').val().trim();
        if (!content || !activeTaskId) return;
        apiPost('/tasks/' + activeTaskId + '/messages', { content }).done(function () {
            $('#chatInput').val('');
            openTaskModal(activeTaskId);
        });
    }

    // ========================
    //  DRAG & DROP
    // ========================
    function onDragStart(event, taskId) {
        dragTaskId = taskId;
        event.dataTransfer.setData('text/plain', taskId);
        event.dataTransfer.effectAllowed = 'move';
        // Add dragging class
        setTimeout(() => { event.target.classList.add('dragging'); }, 0);
    }

    function onKanbanDragStart(event, taskId) {
        onDragStart(event, taskId);
    }

    function onKanbanDrop(event, status) {
        event.preventDefault();
        event.currentTarget.classList.remove('drag-over');
        document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
        if (!dragTaskId) return;
        apiPut('/tasks/' + dragTaskId, { status: status }).done(() => {
            toast('Task moved to ' + status);
            reload();
        });
    }

    function onCalendarDrop(event, dateStr) {
        event.preventDefault();
        event.currentTarget.classList.remove('bg-primary', 'bg-opacity-10');
        document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
        if (!dragTaskId) return;
        apiPut('/tasks/' + dragTaskId, {
            due_date: dateStr,
            status: 'Scheduled'
        }).done(() => {
            toast('Task scheduled');
            reload();
        });
    }

    function promptAddTask(status) {
        const title = prompt('New task title:');
        if (!title || !title.trim()) return;
        apiPost('/tasks', { title: title.trim(), status: status, priority: 'Normal', time_estimate: 30 })
            .done(() => { toast('Task created'); reload(); });
    }

    // ========================
    //  NOTES
    // ========================
    function selectNote(id) {
        activeNoteId = id;
        $('.viso-note-item').removeClass('active').find('h6').removeClass('text-primary').addClass('text-dark');
        $(`.viso-note-item[data-note-id="${id}"]`).addClass('active').find('h6').removeClass('text-dark').addClass('text-primary');

        apiGet('/notes').done(function (notes) {
            const note = notes.find(n => n.id === id);
            if (!note) return;
            $('#noteTitleEdit').text(note.title);
            $('#noteContentEdit').html(note.content || '<p>Start typing...</p>');
            $('#noteLastEdited').text('Last edited ' + new Date(note.updated_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }));
        });
    }

    function addNote() {
        apiPost('/notes', { title: 'Untitled Note', content: '' }).done(() => {
            toast('Note created');
            reload();
        });
    }

    function deleteNote() {
        if (!activeNoteId) return;
        if (!confirm('Delete this note?')) return;
        apiDelete('/notes/' + activeNoteId).done(() => {
            toast('Note deleted');
            reload();
        });
    }

    function saveNoteField(field, value) {
        if (!activeNoteId) return;
        const data = {};
        data[field] = value;
        apiPut('/notes/' + activeNoteId, data);
    }

    function filterNotes(query) {
        const q = query.toLowerCase();
        $('.viso-note-item').each(function () {
            const title = $(this).find('h6').text().toLowerCase();
            $(this).toggle(title.includes(q));
        });
    }

    // ========================
    //  USER MANAGEMENT
    // ========================
    function editUser(id, name, email, role) {
        $('#editUserForm').attr('action', '/users/' + id);
        $('#editUserName').val(name);
        $('#editUserEmail').val(email);
        $('#editUserRole').val(role);
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    // ========================
    //  BACKDROP CLICK
    // ========================
    $(document).on('click', '#taskBackdrop', closeTaskModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeTaskModal();
    });

    // Drag end cleanup
    $(document).on('dragend', function () {
        document.querySelectorAll('.dragging').forEach(el => el.classList.remove('dragging'));
    });

    // ========================
    //  INIT
    // ========================
    $(function () {
        const firstNote = $('.viso-note-item').first();
        if (firstNote.length) {
            activeNoteId = firstNote.data('note-id');
        }

        // Preload users
        apiGet('/users').done(function (users) {
            allUsers = users;
        }).fail(function () {
            // Silently fail if not authenticated for users endpoint
        });
    });

    // ========================
    //  PUBLIC API
    // ========================
    return {
        addTask,
        deleteTask,
        duplicateTask,
        updateTaskField,
        getActiveTaskId,
        quickAddTask,
        addProjectTask,
        openTaskModal,
        closeTaskModal,
        addSubtask,
        toggleSubtask,
        deleteSubtask,
        addAssignee,
        removeAssignee,
        sendChatMessage,
        onDragStart,
        onKanbanDragStart,
        onKanbanDrop,
        onCalendarDrop,
        promptAddTask,
        selectNote,
        addNote,
        deleteNote,
        saveNoteField,
        filterNotes,
        editUser,
        toast,
    };

})(jQuery);
