MEALMATE PHP + MYSQL — FULL PROJECT PACKAGE
===========================================

This package preserves the existing MealMate project files and assets and includes the requested updates:
1. Home page shows only a text offer for college students. The image banner and step-by-step student offer instructions appear on the Student Dashboard only; Student Login has no promotional image banner.
2. Student Dashboard lets a student upload college ID proof (image/PDF) and displays verification status.
3. Admin can review, approve, or reject uploaded college IDs and add a note.
4. Student 50% discount eligibility is tied to the approved verification status where supported by the checkout flow.
5. Vendor login/dashboard, food listing create/edit/delete with photo upload, and vendor order tracking pages are included.
6. Pink theme, home food search (including pizza, burger, pasta, dosa), and logo links back to Home are included. If a search has no exact match, Smart Recommendations display other available foods instead of an empty result.
7. Admin navigation places the Admin label beside the Dashboard control on the restaurant management screen.

INSTALL ON XAMPP (WINDOWS)
--------------------------
1. Extract the ZIP.
2. Copy the included Mealmate folder to C:\\xampp\\htdocs\\Mealmate
3. Start Apache and MySQL in XAMPP Control Panel.
4. Open http://localhost/phpmyadmin/ and select/import your existing mealmate_db database.
5. BACK UP THE DATABASE before running any migration.
6. Import database/student_verification_migration.sql once. If any column already exists, remove/skip that duplicate ADD COLUMN line before importing.
7. Import database/vendor_order_tracking_migration.sql only if vendor support is not already set up. If vendor_id, restaurant_id, or the index already exists, skip the duplicate statement.
8. Open database/database.php and includes/db.php. They currently use MySQL port 3307. If your XAMPP MySQL port is 3306, change the port argument to 3306 in both files.
9. Open http://localhost/Mealmate/

TEST CHECKLIST
--------------
- Home page loads with pink theme, text-only student offer, and food search. The banner image should appear on Student Dashboard only.
- Search for pizza, burger, pasta, or dosa; if no exact match exists, confirm other available foods are recommended.
- Click the logo and confirm it returns to Home.
- Register/login as a student, open Student Dashboard, and upload a college ID image/PDF.
- Login as admin, open Student ID Reviews, and approve/reject the submission.
- Confirm the student sees the updated status and only an approved student can use the student discount flow.
- Login as a vendor and add/edit/delete a food listing with an optional photo.
- Check the admin navigation and vendor order tracking.

IMPORTANT
---------
The PHP files were syntax-checked with PHP CLI when this package was assembled. A complete runtime test against your own MySQL database was not possible here; database schema, port, and login credentials must be checked on your machine. Keep uploaded ID files private and do not publish the uploads folder on a public host without access controls.

CHECKOUT + STUDENT COUPON + UPI
-------------------------------
- Student must upload college ID from Student Dashboard before checkout/order placement.
- The student can see Pending/Approved status on the dashboard. Only when verification_status is Approved, checkout automatically applies 50% off (STUDENT50 eligibility).
- Checkout includes delivery address, UPI payment option (8744851505@ybl), Cash on Delivery, and Place Order button.
- UPI app links open a payment app but this project does NOT automatically verify payment. Admin/merchant must verify the UPI transaction reference separately. Do not treat an order as paid based only on clicking the UPI link.
- Make sure database/student_verification_migration.sql was imported once before testing ID upload and student approval.
