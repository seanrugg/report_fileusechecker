/**
 * File Use Checker Report - JavaScript Module
 *
 * @package    report_fileusechecker
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    return {
        init: function(courseid, can_delete, ajaxUrl) {
            const fileChecker = {
                courseid: courseid,
                can_delete: can_delete,
                ajaxUrl: ajaxUrl,
                selectedFiles: new Set(),

                init: function() {
                    if (this.can_delete) {
                        this.bindSelectAllCheckbox();
                        this.bindFileCheckboxes();
                        this.bindDeleteButton();
                        this.bindConfirmDeleteButton();
                    }
                },

                bindSelectAllCheckbox: function() {
                    const self = this;
                    $('#select-all-checkbox').on('change', function() {
                        const isChecked = $(this).is(':checked');
                        $('.file-checkbox').prop('checked', isChecked).trigger('change');
                    });

                    $('#select-all-btn').on('click', function(e) {
                        e.preventDefault();
                        $('#select-all-checkbox').prop('checked', true).trigger('change');
                    });

                    $('#deselect-all-btn').on('click', function(e) {
                        e.preventDefault();
                        $('#select-all-checkbox').prop('checked', false).trigger('change');
                        $('.file-checkbox').prop('checked', false).trigger('change');
                    });
                },

                bindFileCheckboxes: function() {
                    const self = this;
                    $(document).on('change', '.file-checkbox', function() {
                        const fileid = $(this).val();
                        if ($(this).is(':checked')) {
                            self.selectedFiles.add(fileid);
                        } else {
                            self.selectedFiles.delete(fileid);
                        }
                        self.updateDeleteButton();
                        self.updateSelectAllCheckbox();
                    });
                },

                bindDeleteButton: function() {
                    const self = this;
                    $('#delete-selected-btn').on('click', function(e) {
                        e.preventDefault();
                        if (self.selectedFiles.size > 0) {
                            self.showDeleteConfirmation();
                        }
                    });
                },

                bindConfirmDeleteButton: function() {
                    const self = this;
                    $('#confirmDeleteBtn').on('click', function(e) {
                        e.preventDefault();
                        $('#deleteConfirmModal').modal('hide');
                        self.deleteSelectedFiles();
                    });
                },

                showDeleteConfirmation: function() {
                    const message = M.util.get_string('delete_confirmation', 'report_fileusechecker', this.selectedFiles.size);
                    $('#deleteConfirmMessage').html(message);
                    $('#deleteConfirmModal').modal('show');
                },

                deleteSelectedFiles: function() {
                    const self = this;
                    const fileids = Array.from(this.selectedFiles);

                    this.showLoading();

                    $.ajax({
                        type: 'POST',
                        url: this.ajaxUrl,
                        data: {
                            action: 'delete',
                            fileids: fileids
                        },
                        dataType: 'json',
                        success: function(response) {
                            self.hideLoading();
                            if (response.success) {
                                self.showSuccess(response.message);
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                self.showError(response.message);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            self.hideLoading();
                            self.showError(M.util.get_string('delete_error', 'report_fileusechecker'));
                        }
                    });
                },

                updateDeleteButton: function() {
                    const count = this.selectedFiles.size;
                    if (count > 0) {
                        $('#delete-selected-btn').prop('disabled', false);
                        $('#selected-count').text(count).show();
                    } else {
                        $('#delete-selected-btn').prop('disabled', true);
                        $('#selected-count').hide();
                    }
                },

                updateSelectAllCheckbox: function() {
                    const total = $('.file-checkbox').length;
                    const checked = $('.file-checkbox:checked').length;
                    if (checked === total && total > 0) {
                        $('#select-all-checkbox').prop('checked', true).prop('indeterminate', false);
                    } else if (checked > 0) {
                        $('#select-all-checkbox').prop('indeterminate', true);
                    } else {
                        $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
                    }
                },

                showLoading: function() {
                    $('#loadingIndicator').show();
                    $('#delete-selected-btn').prop('disabled', true);
                },

                hideLoading: function() {
                    $('#loadingIndicator').hide();
                    this.updateDeleteButton();
                },

                showError: function(message) {
                    $('#errorMessage').text(message);
                    $('#errorAlert').show();
                    setTimeout(function() {
                        $('#errorAlert').fadeOut();
                    }, 5000);
                },

                showSuccess: function(message) {
                    $('#successMessage').text(message);
                    $('#successAlert').show();
                }
            };

            fileChecker.init();
        }
    };
});
