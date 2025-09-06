{{-- resources/views/components/user-statistic.blade.php --}}
<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="card-title mb-0 fw-semibold text-dark">
                <i class="bi bi-people text-primary me-2"></i>
                User Monitoring
            </h6>
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#userSelectionModal">
                <i class="bi bi-gear me-1"></i>
                Manage Users
            </button>
        </div>

        {{-- Selected Users Count --}}
        <div class="d-flex align-items-center justify-content-between">
            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                {{ count($userStats) }} Users Selected
            </span>
            @if (count($userStats) > 0)
                <button type="button" class="btn btn-link btn-sm text-decoration-none p-0"
                    onclick="refreshUserStats()">
                    <i class="bi bi-arrow-repeat me-1"></i>
                    Refresh
                </button>
            @endif
        </div>
    </div>

    <div class="card-body p-0" id="userStatsContainer">
        @if ($userStats && $userStats->count() > 0)
            @foreach ($userStats as $index => $user)
                <div class="border-bottom border-light-subtle {{ $loop->last ? '' : 'border-bottom' }}"
                    id="user-{{ $user['id'] }}">
                    <div class="p-4 hover-bg-light transition-all">
                        {{-- User Header with Remove Button --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle bg-primary-subtle text-primary me-3">
                                    {{ strtoupper(substr($user['name'], 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $user['name'] }}</h6>
                                    <small class="text-muted">{{ Str::limit($user['email'], 25) }}</small>
                                </div>
                            </div>
                            {{-- Balance and Remove Button --}}
                            <div class="d-flex align-items-center">
                                <div class="text-end me-3">
                                    <div class="h5 mb-0 fw-bold text-success">{{ number_format($user['balance']) }}
                                    </div>
                                    <small class="text-muted">Points</small>
                                </div>
                                {{-- <button type="button" class="btn btn-outline-danger btn-sm remove-user-btn"
                                    data-user-id="{{ $user['id'] }}" title="Remove user">
                                    <i class="bi bi-x"></i>
                                </button> --}}
                            </div>
                        </div>

                        {{-- Circular Progress Bars --}}
                        <div class="row g-4">
                            {{-- Recycle Progress --}}
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="circular-progress mb-2"
                                        data-percentage="{{ $user['recycle_percentage'] }}">
                                        <svg class="progress-ring" width="80" height="80">
                                            <circle class="progress-ring-bg" cx="40" cy="40" r="30"
                                                stroke="#e9ecef" stroke-width="6" fill="transparent" />
                                            <circle class="progress-ring-fill progress-recycle" cx="40"
                                                cy="40" r="30" stroke="#198754" stroke-width="6"
                                                fill="transparent" stroke-dasharray="188.5"
                                                stroke-dashoffset="{{ 188.5 - (188.5 * $user['recycle_percentage']) / 100 }}"
                                                stroke-linecap="round" />
                                        </svg>
                                        <div class="progress-text">
                                            <span class="percentage">{{ $user['recycle_percentage'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="bi bi-recycle text-success me-1"></i>
                                        <small class="fw-medium text-success">Recycle</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Non-Recycle Progress --}}
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="circular-progress mb-2"
                                        data-percentage="{{ $user['non_recycle_percentage'] }}">
                                        <svg class="progress-ring" width="80" height="80">
                                            <circle class="progress-ring-bg" cx="40" cy="40" r="30"
                                                stroke="#e9ecef" stroke-width="6" fill="transparent" />
                                            <circle class="progress-ring-fill progress-non-recycle" cx="40"
                                                cy="40" r="30" stroke="#dc3545" stroke-width="6"
                                                fill="transparent" stroke-dasharray="188.5"
                                                stroke-dashoffset="{{ 188.5 - (188.5 * $user['non_recycle_percentage']) / 100 }}"
                                                stroke-linecap="round" />
                                        </svg>
                                        <div class="progress-text">
                                            <span class="percentage">{{ $user['non_recycle_percentage'] }}%</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i class="bi bi-trash text-danger me-1"></i>
                                        <small class="fw-medium text-danger">Non-Recycle</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="p-4 text-center" id="emptyState">
                <div class="mb-3">
                    <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                </div>
                <p class="text-muted mb-2">No users selected</p>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                    data-bs-target="#userSelectionModal">
                    Select Users
                </button>
            </div>
        @endif
    </div>
</div>

{{-- User Selection Modal --}}
<div class="modal fade" id="userSelectionModal" tabindex="-1" aria-labelledby="userSelectionModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userSelectionModalLabel">
                    <i class="bi bi-people me-2"></i>
                    Select Users to Monitor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Search Input --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="userSearch"
                            placeholder="Search users by name or email...">
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="selectAll()">Select
                            All</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            onclick="deselectAll()">Deselect All</button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectTop(5)">Top
                            5</button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="selectTop(10)">Top
                            10</button>
                    </div>
                </div>

                {{-- User List --}}
                <div class="user-selection-list" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($availableUsers as $user)
                        <div class="form-check user-item p-3 border rounded mb-2 hover-bg-light"
                            data-user-name="{{ strtolower($user->name) }}"
                            data-user-email="{{ strtolower($user->email) }}">
                            <input class="form-check-input user-checkbox" type="checkbox"
                                value="{{ $user->id }}" id="user_{{ $user->id }}"
                                {{ in_array($user->id, $selectedUserIds ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label w-100 cursor-pointer" for="user_{{ $user->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary-subtle text-secondary me-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="applyUserSelection()">
                    <i class="bi bi-check me-1"></i>
                    Apply Selection
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .hover-bg-light:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .transition-all {
        transition: all 0.2s ease;
    }

    .bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .bg-secondary-subtle {
        background-color: rgba(108, 117, 125, 0.1);
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .text-secondary {
        color: #6c757d !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Circular Progress Styles */
    .circular-progress {
        position: relative;
        display: inline-block;
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-fill {
        transition: stroke-dashoffset 0.5s ease-in-out;
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .progress-text .percentage {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    /* Animation for progress rings */
    @keyframes progress-animation {
        from {
            stroke-dashoffset: 188.5;
        }

        to {
            stroke-dashoffset: var(--progress-offset);
        }
    }

    .progress-ring-fill {
        animation: progress-animation 1s ease-out;
    }

    /* User selection styles */
    .user-item {
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
    }

    .user-item:hover {
        background-color: rgba(13, 110, 253, 0.05);
        border-color: #0d6efd;
    }

    .user-item .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .progress-ring {
            width: 60px;
            height: 60px;
        }

        .progress-ring circle {
            r: 22px;
            cx: 30px;
            cy: 30px;
        }

        .progress-text .percentage {
            font-size: 12px;
        }

        .modal-dialog {
            margin: 0.5rem;
        }

        .btn-group .btn {
            font-size: 12px;
            padding: 0.25rem 0.5rem;
        }
    }
</style>

<script>
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize remove user buttons with event delegation
        initializeRemoveUserButtons();

        // Initialize other components
        initializeUserSearch();
        initializeModalEvents();
    });

    // Function to initialize remove user buttons with event delegation
    function initializeRemoveUserButtons() {
        // Use event delegation to handle dynamically added buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-user-btn')) {
                e.preventDefault();
                const button = e.target.closest('.remove-user-btn');
                const userId = button.getAttribute('data-user-id');

                if (userId) {
                    removeUser(userId);
                }
            }
        });
    }

    // Initialize search functionality
    function initializeUserSearch() {
        const searchInput = document.getElementById('userSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const userItems = document.querySelectorAll('.user-item');

                userItems.forEach(item => {
                    const userName = item.dataset.userName;
                    const userEmail = item.dataset.userEmail;

                    if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }

    // Initialize modal events
    function initializeModalEvents() {
        const modal = document.getElementById('userSelectionModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function() {
                // Reset search
                const searchInput = document.getElementById('userSearch');
                if (searchInput) {
                    searchInput.value = '';
                }

                // Show all user items
                document.querySelectorAll('.user-item').forEach(item => {
                    item.style.display = 'block';
                });
            });
        }
    }

    // Selection functions
    function selectAll() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('.user-item').style.display !== 'none') {
                checkbox.checked = true;
            }
        });
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    }

    function selectTop(count) {
        deselectAll();
        const visibleCheckboxes = Array.from(document.querySelectorAll('.user-checkbox'))
            .filter(checkbox => checkbox.closest('.user-item').style.display !== 'none');

        visibleCheckboxes.slice(0, count).forEach(checkbox => checkbox.checked = true);
    }

    // Apply user selection
    function applyUserSelection() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const selectedUserIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

        if (selectedUserIds.length === 0) {
            alert('Please select at least one user to monitor.');
            return;
        }

        // Show loading state
        const applyBtn = document.querySelector('[onclick="applyUserSelection()"]');
        if (applyBtn) {
            const originalText = applyBtn.innerHTML;
            applyBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Applying...';
            applyBtn.disabled = true;
        }

        // Create form and submit
        submitUserSelection(selectedUserIds);
    }

    // Remove user function - Fixed version
    function removeUser(userId) {
        if (!userId) {
            console.error('User ID not provided');
            return;
        }

        if (confirm('Are you sure you want to remove this user from monitoring?')) {
            // Get current selected users from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const currentUsers = urlParams.getAll('selected_users[]');

            // Remove the specified user
            const updatedUsers = currentUsers.filter(id => id != userId);

            // Submit the updated selection
            submitUserSelection(updatedUsers);
        }
    }

    // Centralized function to submit user selection
    function submitUserSelection(selectedUserIds) {
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = window.location.pathname;

        // Add current URL parameters except selected_users
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.forEach((value, key) => {
            if (key !== 'selected_users[]') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
        });

        // Add selected users
        selectedUserIds.forEach(userId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_users[]';
            input.value = userId;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // Refresh user stats
    function refreshUserStats() {
        window.location.reload();
    }
</script>
