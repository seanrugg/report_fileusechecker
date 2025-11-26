# File Use Checker - Maintenance and Upgrade Guide

This document provides guidance for maintaining and upgrading the File Use Checker plugin.

## Overview

The File Use Checker plugin currently has **no custom database tables** in version 1.0.0. This means:

- ✅ Simple installation with no schema creation required
- ✅ Easy uninstallation with no cleanup needed
- ✅ No data persistence between upgrades
- ⚠️ Caching is in-memory only (5-minute TTL)

## Database Files

### db/install.php - **NOT INCLUDED**
Not needed because:
- No custom database tables are created
- No initialization data is required
- Plugin uses Moodle's existing file storage

If future versions add custom tables, this would:
- Execute once on initial installation
- Create any required database tables
- Initialize default settings

### db/uninstall.php - **NOT INCLUDED**
Not needed because:
- No custom database tables to remove
- No plugin-specific data to clean up
- Moodle automatically removes plugin files and cache

If future versions add data storage, this would:
- Remove custom database tables
- Clean up plugin-specific settings
- Delete any cached data

### db/upgrade.php - **INCLUDED (with examples)**
Included in case future versions need database changes.

## Upgrading the Plugin

### Version Format

The plugin uses date-based versioning: `YYYYMMDDNN`

Example: `2025022500`
- `2025` = Year
- `02` = Month
- `25` = Day
- `00` = Build number (00-99 for multiple releases same day)

### Upgrade Process

#### Step 1: Update version.php

When releasing a new version:

```php
$plugin->version = 2025030100;  // New date
$plugin->release = '1.1.0 (Build: 2025030100)';
```

#### Step 2: Add upgrade code to db/upgrade.php

If your upgrade includes database changes:

```php
if ($oldversion < 2025030100) {
    // Your upgrade code here
    upgrade_plugin_savepoint(true, 2025030100, 'report', 'fileusechecker');
}
```

#### Step 3: Commit and tag in Git

```bash
git tag v1.1.0
git push origin main v1.1.0
```

#### Step 4: Users trigger upgrade

Users visit **Site Administration > Notifications** and the upgrade runs automatically.

## Common Upgrade Scenarios

### Scenario 1: Bug Fixes Only

**Changes needed:**
- Update `version.php` with new version
- No db/upgrade.php changes needed
- Update CHANGELOG with fixes

**Example:**
```php
// version.php
$plugin->version = 2025030100;
$plugin->release = '1.0.1 (Build: 2025030100)';
```

### Scenario 2: New Features (No Database)

**Changes needed:**
- Update `version.php`
- Add new language strings to lang/en/report_fileusechecker.php
- Update templates or classes as needed
- No db/upgrade.php changes

### Scenario 3: Adding Configuration Settings

If you want to add plugin settings that users can configure:

1. Create `settings.php` in plugin root
2. Users can configure via **Site Administration > Plugins > Reports > File Use Checker**
3. No database schema needed (Moodle stores settings in config_plugins table)

### Scenario 4: Adding Event Logging

Future enhancement to log file deletions:

1. Create `classes/event/file_deleted.php`
2. Trigger events in `report.php`
3. No database migration needed (uses Moodle's event system)

### Scenario 5: Adding Performance Cache

If you want to cache scan results in the database:

```php
// db/upgrade.php - add to xmldb_report_fileusechecker_upgrade()

if ($oldversion < 2025030100) {
    $table = new xmldb_table('report_fileusechecker_cache');
    
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
    $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
    $table->add_field('data', XMLDB_TYPE_LONGTEXT);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
    
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
    
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
    
    upgrade_plugin_savepoint(true, 2025030100, 'report', 'fileusechecker');
}
```

Then create a caching class and update file_scanner.php to use database cache instead of in-memory cache.

## Rollback Strategy

### If an upgrade fails:

1. **Stop the upgrade** - Don't proceed past the error screen
2. **Check error logs** - Review Moodle error logs at `/admin/tool/log/`
3. **Restore backup** - Restore database from backup before upgrade
4. **Downgrade version** - Revert version.php to previous version
5. **Test in development** - Reproduce and fix issue before re-upgrading

### Manual Rollback

```sql
-- Example: Remove cache table if creation failed partially
DROP TABLE IF EXISTS mdl_report_fileusechecker_cache;
```

Then update version.php to previous version number.

## Testing Upgrades

### Local Development Environment

1. Create a backup of your test Moodle
2. Increment the version number in version.php
3. Add upgrade code to db/upgrade.php
4. Visit **Site Administration > Notifications**
5. Verify upgrade runs without errors
6. Verify functionality still works
7. Check Moodle logs for warnings

### Staging Environment

Before deploying to production:

1. Test on a staging server with production-like data
2. Run upgrade twice to catch issues:
   - First upgrade: v1.0 → v1.1
   - Reinstall test: v1.1 → v1.1 (should detect no changes)
3. Verify performance hasn't degraded
4. Verify no data loss occurred

## Performance Considerations

### Current Version (1.0.0)

- **File scanning**: O(n) where n = number of files
- **Memory usage**: Low (in-memory cache only)
- **Database queries**: Only reads (no writes)

### Future Versions

If adding database caching:

- **Cache hit**: O(1) constant time lookup
- **Cache miss**: O(n) full scan + cache write
- **Cache invalidation**: 5-minute TTL or manual trigger

## Monitoring After Upgrade

After deploying an upgrade, monitor:

1. **Error logs** - Check `/admin/tool/log/` for plugin errors
2. **Performance** - Monitor page load times for the report
3. **File operations** - Verify file deletion still works correctly
4. **User feedback** - Monitor course dashboards for issues

## Breaking Changes

The plugin commits to:

- ✅ Backward compatibility for public methods
- ✅ Stable report URL structure
- ✅ Consistent permission model
- ⚠️ May change internal class structure (treat as implementation details)

If a breaking change becomes necessary:

1. Major version bump (1.0 → 2.0)
2. Migration guide in release notes
3. Deprecation warning period in previous release

## Deprecation Policy

- **Announcement**: Mark deprecated features for 2+ releases
- **Warning**: Display deprecation notices in admin panel
- **Removal**: Only remove after 6+ months notice

Example:
```php
// Deprecated since v1.2.0, to be removed in v2.0.0
debugging('get_unused_files_old() is deprecated, use get_unused_files() instead', DEBUG_DEVELOPER);
```

## Release Checklist

Before releasing a new version:

- [ ] Update version.php with new version number
- [ ] Update CHANGELOG.md with changes
- [ ] Update db/upgrade.php if needed
- [ ] Test upgrade from previous version
- [ ] Test fresh installation
- [ ] Verify all functionality works
- [ ] Update language strings if needed
- [ ] Run code style checks
- [ ] Test on mobile devices
- [ ] Check browser compatibility
- [ ] Document any breaking changes
- [ ] Get code review if possible
- [ ] Tag release in Git
- [ ] Create GitHub release with notes
- [ ] Update README if needed
- [ ] Announce on Moodle forums if significant

## Support for Old Versions

Maintenance schedule:

- **v1.0.x**: Active support (bug fixes)
- **v1.1.x**: Active support (bug fixes + features)
- **v2.0.x**: Latest (security + features)
- **Older**: Security fixes only if critical

## Resources

- [Moodle Upgrade API Documentation](https://docs.moodle.org/dev/Upgrade_API)
- [XMLDB Documentation](https://docs.moodle.org/dev/XMLDB)
- [Moodle Plugin Release Notes](https://docs.moodle.org/dev/Release_notes)
- [Moodle Coding Guidelines](https://docs.moodle.org/dev/Coding_guidelines)

---

**For questions about maintenance or upgrades, please open an issue on GitHub.**
