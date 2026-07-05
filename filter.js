jQuery(document).ready(function($) {
    // When dropdown changes
    $('#org-filter').on('change', function() {
        var organization = $(this).val();
        
        // Show loading state
        $('.speakers-grid').html('<p style="text-align:center;">Loading speakers...</p>');
        
        // Send AJAX request
        $.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_speakers',
                organization: organization,
                nonce: ajax_obj.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.speakers-grid').html(response.data);
                } else {
                    alert('Error loading speakers');
                }
            },
            error: function() {
                alert('AJAX error. Please try again.');
            }
        });
    });
});
// ============================================
// BOOKING FORM - Add to filter.js

jQuery(document).ready(function($) {
    // Existing filter code here...
    console.log(ajax_obj); // <-- ADD THIS LINE

    // ===== BOOKING MODAL =====
    
    // Open modal when booking button is clicked
    $(document).on('click', '.booking-btn', function() {
        var speakerId = $(this).data('speaker-id');
        var speakerName = $(this).data('speaker-name');
        
        $('#booking-speaker-id').val(speakerId);
        $('#modal-speaker-name').text('Booking with: ' + speakerName);
        $('#booking-modal').css('display', 'flex');
        $('#booking-message').html('');
        $('#visitor-name').val('');
        $('#visitor-email').val('');
    });
    
    // Close modal
    $('#close-modal').on('click', function() {
        $('#booking-modal').css('display', 'none');
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(event) {
        if ($(event.target).is('#booking-modal')) {
            $('#booking-modal').css('display', 'none');
        }
    });
    
    // Submit booking form
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        
        var speakerId = $('#booking-speaker-id').val();
        var visitorName = $('#visitor-name').val();
        var visitorEmail = $('#visitor-email').val();
        
        if (!visitorName || !visitorEmail) {
            $('#booking-message').html('<p style="color:red;">Please fill in all fields.</p>');
            return;
        }
        
        $('#booking-message').html('<p style="color:#0073aa;">Sending booking...</p>');
        
        $.ajax({
            url: ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'save_booking',
                speaker_id: speakerId,
                visitor_name: visitorName,
                visitor_email: visitorEmail,
                nonce: ajax_obj.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#booking-message').html('<p style="color:green;">' + response.data + '</p>');
                    $('#visitor-name').val('');
                    $('#visitor-email').val('');
                } else {
                    $('#booking-message').html('<p style="color:red;">' + response.data + '</p>');
                }
            },
            error: function() {
                $('#booking-message').html('<p style="color:red;">Error submitting booking. Please try again.</p>');
            }
        });
    });
});