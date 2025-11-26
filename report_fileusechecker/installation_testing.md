# File Use Checker - Installation & Testing Guide

Complete step-by-step guide for installing and testing the File Use Checker plugin in a development environment.

## Prerequisites

Before starting, ensure you have:

- ✅ Moodle 5.1 or later installed and running
- ✅ Command line access to your Moodle server
- ✅ Write permissions to the `/report/` directory
- ✅ Basic understanding of Moodle plugin structure
- ✅ A test course with some files already uploaded
- ✅ Admin access to Moodle

## Installation Steps

### Step 1: Clone the Repository

Navigate to your Moodle installation's report directory and clone the plugin:

```bash
cd /path/to/moodle/report
git clone https://github.com/your-org/moodle-report_fileusechecker.git fileusechecker
cd fileusechecker
```

**Or manually download and extract:**

```bash
# Download as ZIP from GitHub
# Extract to: /path/to/moodle/report/fileusechecker
```

### Step 2: Verify Directory Structure

Ensure the plugin is in the correct location:

```bash
ls -la /path/to/moodle/report/fileusechecker/
```

You should see:
```
ajax_delete.php
index.php
settings.php
styles.css
version.php
classes/
db/
lang/
templates/
README.md
MAINTENANCE.md
INSTALLATION_TESTING.md
```

### Step 3: Set File Permissions

Ensure Moodle can read all files (typically owned by www-data or your web server user):

```bash
cd /path/to/moodle/report/fileusechecker
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

### Step 4: Trigger Installation in Moodle

**Method A: Via Web Interface (Recommended)**

1. Log in as administrator
2. Navigate to **Site Administration > Notifications**
3. Moodle will detect the new plugin and display an "Install" button
4. Click "Install" to complete installation
5. Follow any on-screen prompts

**Method B: Via Command Line**

```bash
cd /path/to/moodle
php admin/cli/maintenance.php --enable
php admin/tool/phpunit/cli/init.php
```

### Step 5: Verify Installation

1. Navigate to **Site Administration > Plugins > Plugins overview**
2. Search for "fileusechecker"
3. Confirm status shows "OK" (green)
4. Check that "File Use Checker" appears in **Site Administration > Plugins > Reports**

## Configuration Steps

### Step 1: Configure Plugin Settings

1. Go to **Site Administration > Plugins > Reports > File Use Checker**
2. Review and adjust settings as needed:
   - Cache TTL: 300 (default)
   - Max files per scan: 5000
   - Allow deletion: Enabled
   - Include hidden files: Disabled
   - Include backup files: Disabled

3. Click "Save changes"

### Step 2: Verify Capabilities

1. Go to **Site Administration > Users > Permissions > Define roles**
2. Check that the "Teacher" role has:
   - `report/fileusechecker:view` = Allow
3. Check that the "Editing teacher" role has:
   - `report/fileusechecker:view` = Allow
   - `report/fileusechecker:delete` = Allow
4. Check that the "Manager" role has both capabilities = Allow

## Testing Steps

### Test 1: Access the Report

**Objective:** Verify the report page loads without errors

1. Create or navigate to a test course
2. Log in as a teacher or editing teacher
3. Click **Course Administration > Reports > File Use Checker**
4. Verify the report page loads with:
   - ✅ Header with title and description
   - ✅ Refresh button
   - ✅ Summary cards (4 cards with statistics)
   - ✅ File table with columns
   - ✅ No PHP errors or warnings

**Expected Result:** Report displays clean interface with "No unused files" message (if course has no unused files)

**Check Logs:**
```bash
tail -f /path/to/moodle/var/log/apache2/error.log
tail -f /path/to/moodle/var/www/html/moodledata/debug.log
```

### Test 2: Upload Test Files

**Objective:** Create unused files for testing

1. In your test course, go to **Course Administration > Files**
2. Create a folder called "Test Files"
3. Upload several test files:
   - `test_document.pdf`
   - `test_image.jpg`
   - `test_video.mp4`
   - `test_spreadsheet.xlsx`
4. Do NOT attach these files to any activities
5. Save and close

### Test 3: Scan for Unused Files

**Objective:** Verify the plugin detects unused files

1. Navigate back to the File Use Checker report
2. You should see the uploaded files in the list
3. Verify the summary shows:
   - ✅ Correct number of unused files (should be 4)
   - ✅ Correct total file size
   - ✅ Correct number of affected activities (should be 0)

**If files don't appear:**
- Clear the cache: `php admin/cli/purge_caches.php`
- Refresh the report page
- Check the browser console for JavaScript errors (F12)
- Check Moodle error logs

### Test 4: File Table Display

**Objective:** Verify file information is displayed correctly

1. In the File Use Checker report, verify the file table shows:
   - ✅ File names correctly displayed
   - ✅ File sizes in human-readable format (e.g., "2.5 MB")
   - ✅ File type icons displaying properly
   - ✅ Activity types showing (should be blank or "Course Files")
   - ✅ Location link clickable and opening file management area

### Test 5: File Selection (Checkbox Functionality)

**Objective:** Verify bulk selection works

1. Click "Select All" button
   - ✅ All checkboxes should become checked
   - ✅ Delete button should become enabled
   - ✅ Badge showing selected count should appear

2. Click individual checkboxes to deselect
   - ✅ Delete button state should update
   - ✅ Count badge should decrease

3. Click "Deselect All" button
   - ✅ All checkboxes should clear
   - ✅ Delete button should disable again

### Test 6: File Deletion

**Objective:** Verify file deletion workflow

1. Select 1-2 test files
2. Click "Delete Selected" button
3. Verify confirmation modal appears with:
   - ✅ Warning icon
   - ✅ Confirmation message showing correct count
   - ✅ Information about deletion being permanent
   - ✅ "Cancel" and "Delete" buttons

4. Click "Cancel" - modal should close without deletion
5. Select files again and click "Delete Selected"
6. Click "Delete" in the confirmation modal
7. Verify:
   - ✅ Loading indicator appears
   - ✅ Success message shows "X file(s) deleted successfully"
   - ✅ Page automatically refreshes after 2 seconds
   - ✅ Deleted files no longer appear in the list

**Check Audit Log:**
```bash
# Check event logs for file deletion
php admin/cli/mysql.php < "SELECT * FROM mdl_logstore_standard_log WHERE eventname LIKE '%file_deleted%' ORDER BY timecreated DESC LIMIT 10;"
```

### Test 7: Permission Checks

**Objective:** Verify permission restrictions work

**Test A: Regular Teacher (can view, cannot delete)**

1. Create a user with "Teacher" role (not "Editing Teacher")
2. Enroll them in the test course
3. Log in as this user
4. Navigate to File Use Checker report
5. Verify:
   - ✅ Report displays correctly
   - ✅ File table is visible
   - ✅ Delete button and checkboxes are NOT visible
   - ✅ "Select All" and "Deselect All" buttons NOT visible

**Test B: Non-Enrolled User (no access)**

1. Create a user not enrolled in the course
2. Try to access the report directly via URL
3. Verify:
   - ✅ Access denied error or redirect to login

**Test C: Editing Teacher (can view and delete)**

1. Log in as an editing teacher
2. Navigate to File Use Checker report
3. Verify:
   - ✅ All features visible and functional
   - ✅ Can select and delete files

### Test 8: File with Activities

**Objective:** Verify files attached to activities are NOT marked as unused

1. In the test course, add an Assignment activity
2. Upload a file to the assignment's description/content
3. Create another file that is NOT attached to any activity
4. Go to File Use Checker report
5. Verify:
   - ✅ Only the unattached file shows as unused
   - ✅ The assignment-attached file does NOT appear
   - ✅ "Affected Activities" count is 0 (since the used file isn't counted)

### Test 9: Responsive Design

**Objective:** Verify UI works on different screen sizes

**Desktop (1920x1080):**
1. All elements clearly visible
2. Summary cards in 4-column layout
3. Table displays all columns

**Tablet (768x1024):**
1. Summary cards in 2-column layout
2. Table remains readable
3. Buttons stack appropriately

**Mobile (375x667):**
1. Summary cards in 1-column layout
2. Table scrolls horizontally
3. Buttons are touch-friendly

**Test via DevTools:**
```
Press F12 in browser
Click device toggle (Ctrl+Shift+M)
Test at different viewport sizes
```

### Test 10: Error Handling

**Objective:** Verify graceful error handling

**Test A: Delete without selecting files**

1. Try to click "Delete Selected" without selecting any files
   - ✅ Should be disabled (grayed out)

**Test B: Permission denied scenario**

1. Try to access `/report/fileusechecker/ajax_delete.php` directly
   - ✅ Should return error (requires POST, proper course, permission)

**Test C: Invalid session key**

1. In browser console, modify the deletion AJAX call
2. Provide invalid sesskey parameter
3. Verify:
   - ✅ Error message appears
   - ✅ No files are deleted

### Test 11: Caching

**Objective:** Verify caching works correctly

1. Access File Use Checker report (first load)
   - Note the time taken
2. Refresh immediately
   - Should load faster (cache hit)
3. Upload new file to course
4. Refresh report within cache TTL (5 minutes)
   - ✅ New file might not appear (cached result)
5. Click "Refresh" button
   - ✅ Should bypass cache and show new file
6. Wait 5+ minutes
7. Access report again
   - ✅ New file should appear (cache expired)

### Test 12: Settings Configuration

**Objective:** Verify settings page works

1. Go to **Site Administration > Plugins > Reports > File Use Checker**
2. Verify all settings display correctly:
   - ✅ Text fields show defaults
   - ✅ Checkboxes show current state
   - ✅ Select dropdowns show options
   - ✅ Descriptions are visible

3. Change a setting (e.g., Cache TTL to 600)
4. Click "Save changes"
5. Verify:
   - ✅ No errors
   - ✅ Setting is saved
   - ✅ Refresh report and it uses new cache TTL

## Browser Compatibility Testing

Test on multiple browsers:

- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (if macOS available)
- [ ] Mobile browsers (Chrome mobile, Safari mobile)

In each browser, verify:
- ✅ All buttons clickable
- ✅ Modals display correctly
- ✅ Tables render properly
- ✅ Icons display
- ✅ No JavaScript console errors (F12 > Console tab)

## Performance Testing

### Large File Set Test

1. Prepare a test course with 500+ files (mix used and unused)
2. Access File Use Checker report
3. Measure:
   - ✅ Initial load time (should be < 10 seconds)
   - ✅ Refresh load time (should be faster due to cache)
   - ✅ Table rendering time
   - ✅ Deletion operation time

4. Check server resources during scan:
   - CPU usage (should not spike)
   - Memory usage (should not be excessive)

```bash
# Monitor during testing
top -u www-data
ps aux | grep php
```

## Debugging

### Enable Debug Mode in Moodle

```php
// In /path/to/moodle/config.php, add:
@error_reporting(E_ALL | E_STRICT);
@ini_set('display_errors', '1');
$CFG->debug = DEBUG_DEVELOPER;
$CFG->debugdisplay = 1;
$CFG->debuglogdb = 1;
```

### View Debug Logs

```bash
tail -f /path/to/moodledata/debug.log
```

### Check PHP Syntax

```bash
php -l /path/to/moodle/report/fileusechecker/index.php
php -l /path/to/moodle/report/fileusechecker/classes/report.php
# etc for all PHP files
```

### Test AJAX Directly

```bash
# Simulate AJAX delete request
curl -X POST \
  -d "courseid=2&action=delete&fileids[]=1&sesskey=YOUR_SESSKEY" \
  http://localhost/moodle/report/fileusechecker/ajax_delete.php
```

## Checklist: Before Declaring Ready

- [ ] Plugin installs without errors
- [ ] Settings page accessible and configurable
- [ ] Report displays without warnings
- [ ] Can view unused files
- [ ] Can select files
- [ ] Can delete files
- [ ] Confirmation dialog works
- [ ] Success/error messages display correctly
- [ ] Permissions work correctly
- [ ] Teacher (view only) vs Editing Teacher (view + delete) roles work
- [ ] Mobile layout is responsive
- [ ] No JavaScript console errors
- [ ] No PHP errors in Moodle logs
- [ ] Cache works (results cached for 5 minutes)
- [ ] Refresh button bypasses cache
- [ ] Settings are saved correctly
- [ ] Test with 500+ files (performance acceptable)
- [ ] File icons display correctly
- [ ] Location links work and open correct pages
- [ ] All language strings display correctly

## Troubleshooting

### Report Not Appearing in Reports Menu

**Solution:**
```bash
# Clear Moodle cache
cd /path/to/moodle
php admin/cli/purge_caches.php

# Check plugin installation
php admin/cli/mysql.php < "SELECT * FROM mdl_config_plugins WHERE plugin='report_fileusechecker';"
```

### "Permission denied" Error

**Solution:**
- Verify user has `report/fileusechecker:view` capability
- Check Course Administration > Roles > Define roles
- Verify role is assigned to user in course

### Files Not Showing as Unused

**Solution:**
```bash
# Check if files exist in file storage
php admin/cli/mysql.php < "SELECT * FROM mdl_files WHERE component='course' AND filearea='legacy' LIMIT 10;"

# Clear cache
php admin/cli/purge_caches.php
```

### AJAX Delete Not Working

**Solution:**
- Check browser console (F12) for JavaScript errors
- Check Moodle error logs
- Verify AJAX request includes correct sesskey
- Try in a different browser (may be cache issue)

### Settings Not Saving

**Solution:**
- Verify you're in the correct settings page
- Check for PHP errors in Moodle logs
- Try clearing browser cache
- Try in private/incognito browser window

## Next Steps After Successful Testing

1. Document any issues found and fixes applied
2. Update code if bugs discovered
3. Create release notes for v1.0.0
4. Tag release in Git: `git tag v1.0.0`
5. Push to GitHub: `git push origin main v1.0.0`
6. Consider submitting to Moodle Plugins Directory

## Questions or Issues?

If you encounter problems during testing:

1. Check Moodle error logs: `/path/to/moodledata/debug.log`
2. Review PHP syntax: `php -l filename.php`
3. Check browser console: Press F12 in browser
4. Review this guide again
5. Open an issue on GitHub with details and error messages

---

**Good luck with testing! Let us know how it goes.**
