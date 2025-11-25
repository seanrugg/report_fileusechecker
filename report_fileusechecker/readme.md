# Moodle File Use Checker Plugin (`report_fileusechecker`)

## 📄 Overview

The Moodle **File Use Checker** is a course report plugin designed to help course managers, teachers, and administrators identify and manage **unused (orphaned) files** within a Moodle course. These are files uploaded to the course context (including activity file areas) that are not currently linked to or displayed by any resource, activity, or embedded text content visible to learners.

This plugin provides a comprehensive report with statistics and an easy-to-use interface to clean up the course by removing these unnecessary files, helping to reduce course size and improve backup/restore performance.

## ✨ Features

* **Visibility:** Provides a dedicated report under the Course Administration > Reports tab.
* **Identification:** Scans all course files and compares them against files actively used by course modules and sections.
* **Detailed List:** Displays a list of unused files, including:
    * File Name
    * File Size (formatted)
    * Location (Activity/Resource title)
    * A link to the activity/resource's file management area for context.
* **Summary Statistics:** Shows totals for:
    * Number of unused files
    * Total size of unused files
    * Number of affected activities/resources.
* **Bulk Deletion:** Allows users with the necessary permissions to select individual or all unused files for deletion via a single action button.
* **Permissions:** Report visibility requires `report/fileusechecker:view`, and file deletion requires `report/fileusechecker:delete`.

## ⚙️ Requirements

* **Moodle Version:** 5.1 or later.
* **PHP Version:** Moodle 5.1 compatible requirements.

## 💾 Installation

### 1. Download

Clone the repository or download the ZIP file.

### 2. Deployment

Place the plugin contents into the Moodle installation's report directory:

```bash
[moodle_root]/report/fileusechecker/
````

### 3\. Installation via Moodle

1.  Log in as a site administrator.
2.  Navigate to **Site Administration** \> **Notifications**.
3.  Moodle will detect the new plugin and prompt you to install it.
4.  Follow the on-screen prompts to complete the installation. No new database tables are created as the plugin utilizes existing Moodle file APIs.

## 🚀 Usage

### 1\. Accessing the Report

1.  Navigate to the desired Moodle course.
2.  Go to **Course Administration** (or gear icon) \> **Reports**.
3.  Click on **File Use Checker**.

### 2\. Using the Report

1.  **Review the Summary:** Check the summary cards at the top for the total count and size of all unused files.
2.  **Inspect the List:** Review the table of unused files, paying attention to the file name, size, and the activity location where the file currently resides (typically in a hidden or unreferenced state).
3.  **View Context:** Click the **Location** link to navigate directly to the activity settings or file management area where the file is stored, allowing you to verify if the file is truly unneeded.
4.  **Delete Files (Manager/Teacher role with permission):**
      * Use the **Select All** button or check individual file boxes.
      * Click the **Delete Selected** button.
      * Confirm the deletion in the pop-up warning. This action is irreversible.

## 🔒 Capabilities

The plugin defines two capabilities:

| Capability | Purpose | Default Roles |
| :--- | :--- | :--- |
| `report/fileusechecker:view` | View the File Use Checker Report | Teacher, Course Creator, Manager |
| `report/fileusechecker:delete` | Delete unused files from the course | Editing Teacher, Course Creator, Manager |

-----

## 🏗️ Development Notes

This plugin relies heavily on Moodle's core File API and context system.

  * **File Detection:** The detection algorithm involves scanning all course-context file areas and comparing them against a comprehensive index of files explicitly referenced by course modules (activities, resources, book chapters, etc.).
  * **Classes:** Key logic resides in `report.php`, `file_scanner.php`, and `file_analyzer.php`.
  * **Templates:** The interface is rendered using Mustache templates (`report_view.mustache` and `file_list_item.mustache`).

