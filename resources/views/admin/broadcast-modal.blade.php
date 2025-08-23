<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('announcements.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Broadcast Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input name="title" class="form-control mb-2" placeholder="Announcement Title" required>

                <textarea name="message" class="form-control mb-2" rows="4" placeholder="Enter your message here..." required></textarea>

                <!-- Expired Date and Time Field -->
                <label for="expired_at" class="form-label">Expired Date & Time</label>
                <input
                    type="datetime-local"
                    name="expired_at"
                    id="expired_at"
                    class="form-control"
                    required
                    min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                >
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>
</div>
