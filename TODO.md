- [x] Change session_time column to store absolute end UNIX timestamp instead of remaining seconds.
- [x] Update main/billing.php: Modify purchase_time action to add purchased seconds to current end_timestamp (or set if 0), return calculated remaining time in JSON response.
- [x] Update main/memberhome.php, main/food.php, main/anno.php: Modify get_session_time to calculate remaining time as max(0, session_time - time()).
- [x] Update js/billing.js, js/memberhome.js, js/food.js, js/anno.js: Remove updateSessionTime calls every second, add periodic sync or on page unload to update DB with remaining time.
- [x] Fix timer to not display negative time and handle expiration properly.
- [x] Add periodic sync of remaining time to DB every 10 seconds to persist time across pages.
- [x] Add update_session_time action in PHP files to allow syncing.
- [x] Test full flow: purchase, time runs, syncs, expires correctly.
    