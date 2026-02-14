/**
 * VisoApp — Visobotics SaaS Frontend Controller
 * Handles AJAX interactions, drag-and-drop, slide-over modal, and client-side UI state.
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

    // Currently open task id
    let activeTaskId = null;
    let activeNoteId = null;
    let dragTaskId = null;

    // ========================
    //  AJAX HELPERS (DRY)
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

    function reload() {
        window.location.reload();
    }

    function toast(msg, type = 'success') {
        // Simple toast — could be enhanced with a snackbar library
        const el = $(`<div class="position-fixed bottom-0 end-0 m-3 p-3 rounded shadow-lg text-white fw-medium small" style="z-index:9999;animation:visoSlideUp .3s ease;background:${type === 'success' ? 'var(--viso-success)' : 'var(--viso-danger)'}">${msg}</div>`);
        $('body').append(el);
        setTimeout(() => el.fadeOut(300, () => el.remove()), 2500);
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
            toast('Updated');
        });
    }

    // ========================
    //  TASK MODAL (SLIDE-OVER)
    // ========================
    function openTaskModal(id) {
        activeTaskId = id;
        apiGet('/tasks/' + id).done(function (task) {
            // Populate modal
            $('#taskModalProject').text(task.project ? task.project.name : 'Personal');
            $('#taskModalTitle').text(task.title);
            $('#taskDetailTitle').text(task.title);
            $('#taskModalDate').text('');
            $('#taskStatusSelect').val(task.status);
            $('#taskPrioritySelect').val(task.priority);
            $('#taskRecurrenceSelect').val(task.recurrence || 'none');

            // Subtasks
            renderSubtasks(task.subtasks || []);

            // Chat
            renderChat(task.chat_messages || []);

            // Show modal
            $('#taskBackdrop').addClass('show');
            $('#taskSlideOver').addClass('show');
        });
    }

    function closeTaskModal() {
        $('#taskBackdrop').removeClass('show');
        $('#taskSlideOver').removeClass('show');
        activeTaskId = null;
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
                <div class="d-flex align-items-center gap-3 p-2 rounded hover-bg-light">
                    <input type="checkbox" class="form-check-input mt-0 cursor-pointer"
                           ${s.completed ? 'checked' : ''}
                           onchange="VisoApp.toggleSubtask(${s.id}, ${s.completed ? 'true' : 'false'})">
                    <span class="small flex-grow-1 ${s.completed ? 'text-muted text-decoration-line-through' : ''}">${s.title}</span>
                    <button onclick="VisoApp.deleteSubtask(${s.id})" class="btn btn-sm btn-link text-muted p-0" style="opacity:0.5">
                        <i class="icon-trash-2" style="font-size:14px"></i>
                    </button>
                </div>
            `);
        });
    }

    function addSubtask(title) {
        if (!title.trim() || !activeTaskId) return;
        apiPost('/tasks/' + activeTaskId + '/subtasks', { title: title.trim() }).done(function (sub) {
            openTaskModal(activeTaskId); // Refresh
            $('#newSubtaskInput').val('');
        });
    }

    function toggleSubtask(subtaskId, currentState) {
        apiPut('/tasks/' + activeTaskId + '/subtasks/' + subtaskId, {}).done(function () {
            openTaskModal(activeTaskId);
        });
    }

    function deleteSubtask(subtaskId) {
        apiDelete('/tasks/' + activeTaskId + '/subtasks/' + subtaskId).done(function () {
            openTaskModal(activeTaskId);
        });
    }

    // ========================
    //  CHAT / COMMENTS
    // ========================
    function renderChat(messages) {
        const $chat = $('#chatMessages').empty();
        messages.forEach(function (msg) {
            const avatar = msg.user ? msg.user.avatar : '';
            const name = msg.user ? msg.user.name : 'User';
            const time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            $chat.append(`
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <img src="${avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&size=24&background=3b82f6&color=fff'}"
                             class="rounded-circle" width="24" height="24">
                        <span class="small fw-bold text-dark">${name}</span>
                        <span class="text-muted fs-10">${time}</span>
                    </div>
                    <div class="ms-4 small text-dark bg-white p-2 rounded shadow-sm border border-light">
                        ${msg.content}
                    </div>
                </div>
            `);
        });
        // Scroll to bottom
        const chatEl = document.getElementById('chatMessages');
        if (chatEl) chatEl.scrollTop = chatEl.scrollHeight;
    }

    function sendChatMessage() {
        const content = $('#chatInput').val().trim();
        if (!content || !activeTaskId) return;
        apiPost('/tasks/' + activeTaskId + '/messages', { content }).done(function () {
            $('#chatInput').val('');
            openTaskModal(activeTaskId); // Refresh
        });
    }

    // ========================
    //  DRAG & DROP
    // ========================
    function onDragStart(event, taskId) {
        dragTaskId = taskId;
        event.dataTransfer.setData('text/plain', taskId);
        event.dataTransfer.effectAllowed = 'move';
    }

    function onKanbanDrop(event, status) {
        event.preventDefault();
        event.currentTarget.classList.remove('drag-over');
        if (!dragTaskId) return;
        apiPut('/tasks/' + dragTaskId, { status: status }).done(reload);
    }

    function onCalendarDrop(event, dateStr) {
        event.preventDefault();
        event.currentTarget.classList.remove('bg-primary', 'bg-opacity-10');
        if (!dragTaskId) return;
        apiPut('/tasks/' + dragTaskId, {
            due_date: dateStr,
            status: 'Scheduled'
        }).done(reload);
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
        // Update sidebar active
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
    //  BACKDROP CLICK
    // ========================
    $(document).on('click', '#taskBackdrop', closeTaskModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeTaskModal();
    });

    // ========================
    //  INIT
    // ========================
    $(function () {
        // Set first note active if exists
        const firstNote = $('.viso-note-item').first();
        if (firstNote.length) {
            activeNoteId = firstNote.data('note-id');
        }
    });

    // ========================
    //  PUBLIC API
    // ========================
    return {
        addTask,
        deleteTask,
        duplicateTask,
        updateTaskField,
        openTaskModal,
        closeTaskModal,
        addSubtask,
        toggleSubtask,
        deleteSubtask,
        sendChatMessage,
        onDragStart,
        onKanbanDrop,
        onCalendarDrop,
        promptAddTask,
        selectNote,
        addNote,
        deleteNote,
        saveNoteField,
        filterNotes,
    };

})(jQuery);
