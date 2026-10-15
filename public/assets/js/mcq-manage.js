/**
 * MCQ Management - Question & Options CRUD
 * Handles add/edit/delete questions with drag-and-drop reordering
 */

(function() {
    'use strict';

    let questionModal = null;
    let editingQuestionUuid = null;
    let optionCounter = 0;

    function mcqRoute(name, replacements = {}) {
        let url = window.mcqRoutes?.[name];

        if (!url) {
            console.error(`Missing MCQ route: ${name}`);
            return '#';
        }

        Object.keys(replacements).forEach(placeholder => {
            url = url.replace(placeholder, encodeURIComponent(replacements[placeholder]));
        });

        return normalizeAdminRoute(url);
    }

    function normalizeAdminRoute(url) {
        const adminIndex = window.location.pathname.indexOf('/admin');
        const currentAdminPrefix = adminIndex >= 0 ? window.location.pathname.slice(0, adminIndex) : '';

        if (!currentAdminPrefix) {
            return url;
        }

        const parsedUrl = new URL(url, window.location.origin);

        if (parsedUrl.pathname.startsWith('/admin')) {
            parsedUrl.pathname = `${currentAdminPrefix}${parsedUrl.pathname}`;
        }

        return parsedUrl.toString();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeModal();
        initializeEventListeners();
        initializeSortable();
    });

    /**
     * Initialize question modal
     */
    function initializeModal() {
        const modalElement = document.getElementById('questionModal');
        if (!modalElement) return;

        questionModal = new bootstrap.Modal(modalElement);

        // Add option button
        document.getElementById('addOptionBtn').addEventListener('click', addOption);

        // Form submission
        document.getElementById('questionForm').addEventListener('submit', handleQuestionSubmit);

        // Initialize with 4 default options
        for (let i = 0; i < 4; i++) {
            addOption();
        }
    }

    /**
     * Initialize event listeners
     */
    function initializeEventListeners() {
        // Add question button
        document.getElementById('addQuestionBtn').addEventListener('click', openAddQuestionModal);

        // Edit question buttons (event delegation)
        document.getElementById('questionsContainer').addEventListener('click', function(e) {
            if (e.target.closest('.edit-question-btn')) {
                const btn = e.target.closest('.edit-question-btn');
                const questionUuid = btn.getAttribute('data-question-uuid');
                openEditQuestionModal(questionUuid);
            }

            if (e.target.closest('.delete-question-btn')) {
                const btn = e.target.closest('.delete-question-btn');
                const questionUuid = btn.getAttribute('data-question-uuid');
                deleteQuestion(questionUuid);
            }
        });
    }

    /**
     * Initialize drag and drop sorting
     */
    function initializeSortable() {
        const questionsContainer = document.getElementById('questionsContainer');

        if (!questionsContainer || typeof Sortable === 'undefined') {
            return;
        }

        new Sortable(questionsContainer, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                updateQuestionNumbers();
                saveQuestionsOrder();
            }
        });
    }

    /**
     * Add option input field
     */
    function addOption(optionData = null) {
        const container = document.getElementById('optionsContainer');
        const optionId = optionCounter++;

        const optionHtml = `
            <div class="option-item" data-option-id="${optionId}" data-option-uuid="${optionData?.uuid || ''}">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="correct_option" value="${optionId}"
                           ${optionData?.is_correct ? 'checked' : ''} required>
                </div>
                <input type="text" class="form-control option-text" placeholder="Option ${optionId + 1}"
                       value="${optionData?.option_text || ''}" required>
                <button type="button" class="btn btn-sm btn-outline-danger remove-option-btn">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = optionHtml;
        const optionElement = tempDiv.firstElementChild;

        container.appendChild(optionElement);

        // Remove option button
        optionElement.querySelector('.remove-option-btn').addEventListener('click', function() {
            if (container.children.length > 2) {
                optionElement.remove();
                updateOptionPlaceholders();
            } else {
                alert('At least 2 options are required');
            }
        });
    }

    /**
     * Update option placeholders after removal
     */
    function updateOptionPlaceholders() {
        const options = document.querySelectorAll('.option-item');
        options.forEach((option, index) => {
            const input = option.querySelector('.option-text');
            input.placeholder = `Option ${index + 1}`;

            const radio = option.querySelector('input[type="radio"]');
            radio.value = index;
        });
    }

    /**
     * Open add question modal
     */
    function openAddQuestionModal() {
        editingQuestionUuid = null;
        resetQuestionForm();
        document.getElementById('questionModalLabel').textContent = window.lang.addQuestion;
        questionModal.show();
    }

    /**
     * Open edit question modal
     */
    function openEditQuestionModal(questionUuid) {
        editingQuestionUuid = questionUuid;

        // Find question data from DOM
        const questionItem = document.querySelector(`[data-question-uuid="${questionUuid}"]`);
        if (!questionItem) return;

        // Fetch question data via AJAX
        $.ajax({
            url: mcqRoute('questionShow', { __QUESTION__: questionUuid }),
            method: 'GET',
            success: function(data) {
                if (data.success) {
                    populateQuestionForm(data.question);
                    document.getElementById('questionModalLabel').textContent = window.lang.editQuestion;
                    questionModal.show();
                }
            },
            error: function(error) {
                console.error('Error fetching question:', error);
                alert('Failed to load question data');
            }
        });
    }

    /**
     * Populate form with question data
     */
    function populateQuestionForm(question) {
        document.getElementById('questionUuid').value = question.uuid;
        document.getElementById('questionText').value = question.question;
        document.getElementById('questionExplanation').value = question.explanation || '';

        // Clear existing options
        const container = document.getElementById('optionsContainer');
        container.innerHTML = '';
        optionCounter = 0;

        // Add options
        question.options.forEach((option, index) => {
            addOption({
                uuid: option.uuid,
                option_text: option.option_text,
                is_correct: option.is_correct
            });
        });
    }

    /**
     * Reset question form
     */
    function resetQuestionForm() {
        document.getElementById('questionForm').reset();
        document.getElementById('questionUuid').value = '';
        document.getElementById('questionText').value = '';
        document.getElementById('questionExplanation').value = '';

        const container = document.getElementById('optionsContainer');
        container.innerHTML = '';
        optionCounter = 0;

        // Add 4 default options
        for (let i = 0; i < 4; i++) {
            addOption();
        }

        // Clear validation errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    /**
     * Handle question form submission
     */
    function handleQuestionSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = document.getElementById('saveQuestionBtn');

        // Collect form data
        const questionText = document.getElementById('questionText').value;
        const explanation = document.getElementById('questionExplanation').value;
        // const correctOptionIndex = parseInt(document.querySelector('input[name="correct_option"]:checked').value);
        const selectedOption = document.querySelector(
            'input[name="correct_option"]:checked'
        );

        if (!selectedOption) {
            alert('Please select the correct option');
            return;
        }

        const correctOptionIndex = parseInt(selectedOption.value);

        const options = [];
        const optionItems = document.querySelectorAll('.option-item');
        optionItems.forEach((item, index) => {
            const uuid = item.getAttribute('data-option-uuid');
            const text = item.querySelector('.option-text').value;
            options.push({
                uuid: uuid || null,
                text: text
            });
        });

        const formData = {
            question: questionText,
            explanation: explanation,
            options: options,
            correct_option: correctOptionIndex
        };

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

        // Determine URL based on add/edit mode
        const url = editingQuestionUuid
            ? mcqRoute('questionUpdate', { __QUESTION__: editingQuestionUuid })
            : mcqRoute('questionStore', { __LESSON__: lessonUuid });

        const method = editingQuestionUuid ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: JSON.stringify(formData),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
            if (data.success) {
                questionModal.hide();
                Swal.fire({
                    icon: 'success',
                    title: window.lang.success,
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                alert(data.message || 'Failed to save question');
            }
            },
            error: function(error) {
            console.error('Error:', error);
            alert('An error occurred while saving the question');
            },
            complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-save"></i> Save Question';
            }
        });
    }

    /**
     * Delete question
     */
    function deleteQuestion(questionUuid) {
        Swal.fire({
            title: '{{ __("mcq.are_you_sure") }}',
            text: '{{ __("mcq.question_delete_warning") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("mcq.yes_delete") }}',
            cancelButtonText: '{{ __("mcq.cancel") }}',
            reverseButtons: true
        }).then((result) => {

            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: mcqRoute('questionDelete', { __QUESTION__: questionUuid }),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .done(function(data) {
                if (data.success) {
                    const questionItem = document.querySelector(`[data-question-uuid="${questionUuid}"]`);
                    if (questionItem) {
                        questionItem.remove();
                    }

                    updateQuestionNumbers();
                    showSuccessMessage(data.message);

                    // Check if no questions left
                    const container = document.getElementById('questionsContainer');
                    if (container.children.length === 0) {
                        container.innerHTML = `
                            <div class="text-center text-muted py-4" id="noQuestionsMessage">
                                <i class="fa fa-info-circle"></i> No questions added yet
                            </div>
                        `;
                    }
                } else {
                    alert(data.message || 'Failed to delete question');
                }
            })
            .fail(function(error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the question');
            });
        });
    }

    /**
     * Save questions order after drag-and-drop
     */
    function saveQuestionsOrder() {
        const questions = [];
        document.querySelectorAll('.question-item').forEach(item => {
            questions.push(item.getAttribute('data-question-uuid'));
        });

        $.ajax({
            url: mcqRoute('questionReorder', { __LESSON__: lessonUuid }),
            method: 'POST',
            data: JSON.stringify({ questions: questions }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(data) {
            if (data.success) {
                showSuccessMessage('Questions reordered successfully');
            }
        })
        .fail(function(error) {
            console.error('Error reordering questions:', error);
        });
    }

    /**
     * Update question numbers
     */
    function updateQuestionNumbers() {
        const questions = document.querySelectorAll('.question-item');
        questions.forEach((question, index) => {
            const numberSpan = question.querySelector('.question-number');
            if (numberSpan) {
                numberSpan.textContent = `Question ${index + 1}`;
            }
        });
    }

    /**
     * Show success message
     */
    function showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: window.lang.success,
            text: message,
            confirmButtonText: 'OK'
        });
    }

})();
