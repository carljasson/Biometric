<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" 
              action="{{ route('announcements.store') }}"
              class="modal-content border-0 shadow-sm rounded-3">
            @csrf

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top">
                <h5 class="modal-title" id="broadcastModalLabel">
                    <i class="bi bi-megaphone-fill me-2"></i> Broadcast Announcement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body bg-light">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Announcement Title</label>
                    <input type="text" name="title" class="form-control" placeholder="Enter title..." required>
                </div>

                <!-- Message -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Message</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Enter your message..." required></textarea>
                </div>

                <!-- Expiration -->
                <div class="mb-3">
                    <label for="expired_at" class="form-label fw-semibold">Expiration Date & Time</label>
                    <input type="datetime-local"
                           name="expired_at"
                           id="expired_at"
                           class="form-control"
                           required
                           min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-white rounded-bottom">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill me-1"></i> Send Broadcast
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Subtle custom touch for consistent system style */
.modal-content {
    border-radius: 0.75rem;
    overflow: hidden;
}
.modal-body label {
    color: #495057;
}
.modal-header, .modal-footer {
    border: none;
}
</style>
