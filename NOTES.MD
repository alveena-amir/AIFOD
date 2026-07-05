# AIFOD Speakers Theme - Development Notes

## What I Completed

### Task 1 - Speaker Directory
- ✅ Registered "Speaker" custom post type.
- ✅ Added custom fields: **Job Title** and **Organization** (using native WordPress meta boxes).
- ✅ Displayed speakers on the homepage using `WP_Query`.
- ✅ Built a responsive card grid: 3 columns on desktop, 1 column on mobile.
- ✅ Added hover effects on cards.
- ✅ Implemented proper escaping (`esc_html`, `esc_attr`, `esc_url`) for security.
- ✅ Enqueued all CSS and JS via `wp_enqueue_scripts`.

### Task 2 - Filtering & Booking
- ✅ Built an **AJAX Organization Filter** (server-side filtering using `WP_Query`).
- ✅ Dynamically populated the filter dropdown from existing speakers.
- ✅ Added a **"Book a Meeting"** button to each speaker card.
- ✅ Created a booking form modal with **Name** and **Email** fields.
- ✅ Saved bookings to a **"Booking"** custom post type.
- ✅ Linked each booking to a specific speaker via meta data.
- ✅ Made bookings viewable in the WordPress admin.
- ✅ Implemented **Nonce protection** for all AJAX requests.
- ✅ Applied input sanitization (`sanitize_text_field`, `sanitize_email`) and server-side validation (`is_email`).

---

## Architectural Choices

### admin-ajax vs REST API
**Choice:** `admin-ajax.php`

**Why:** For this specific test, `admin-ajax` was the simpler and more straightforward choice. It integrates seamlessly with the WordPress nonce system (`check_ajax_referer`), requires less boilerplate code than a full REST API setup, and is perfectly adequate for the current use case. It also supports both logged-in and non-logged-in users (using `nopriv` hooks).

**Trade-off:** While the REST API is considered more modern, `admin-ajax` is still widely used and reliable for internal WordPress tasks.

### CPT vs Custom Table for Bookings
**Choice:** Custom Post Type (CPT) - "Booking"

**Why:** Using a CPT leverages WordPress's built-in features. It automatically provides:
- A functional admin interface (list table, edit screens).
- Easy querying using `WP_Query` or meta queries.
- The ability to easily add more fields using the same meta system.
- A future-proof and extendable structure.

**Trade-off:** CPTs can be slightly less performant than custom tables for massive datasets, but for this application, the development speed and built-in admin UI made the CPT the clear winner.

---

## Security Measures Implemented
- ✅ **Nonces:** Used `wp_create_nonce` and `check_ajax_referer` to protect all AJAX requests from CSRF attacks.
- ✅ **Input Sanitization:** All user inputs (`$_POST` data) are sanitized using `sanitize_text_field` and `sanitize_email`.
- ✅ **Output Escaping:** All data displayed on the frontend is escaped using `esc_html`, `esc_attr`, and `esc_url`.
- ✅ **Email Validation:** Server-side validation of email addresses using WordPress's built-in `is_email()` function.

---

## What I Would Improve with More Time
1.  **Design Polish:** Refine the CSS to more closely match the af.net design language (colors, fonts, spacing).
2.  **Email Notifications:** Send a confirmation email to the visitor after a booking is saved.
3.  **Admin Enhancements:** Add a custom admin page to view and manage bookings more effectively.
4.  **Pagination:** Add pagination to the speaker grid for better performance with a large number of speakers.
5.  **Caching:** Implement transients to cache the speaker query and filter results.
6.  **Loading States:** Add better UX feedback (spinners/disable buttons) during AJAX requests.
7.  **Accessibility:** Improve accessibility with ARIA labels and keyboard navigation.
8.  **Date/Time Picker:** Add a date and time picker to the booking form.
9.  **Unit Tests:** Write unit tests for critical PHP functions.

---

## File Structure