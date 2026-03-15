// Populate children when parent dropdown changes
function populateChildren(parent, selectedInclude = [], selectedExclude = []) {
    if (!parent) {
        jQuery('#rise_bld_include_children').html('');
        jQuery('#rise_bld_exclude_children').html('');
        return;
    }
    jQuery.post(rise_bld_ajax.ajax_url, { action: 'rise_bld_get_child_categories', parent: parent, nonce: rise_bld_ajax.nonce }, function (html) {
        jQuery('#rise_bld_include_children').html(html);
        jQuery('#rise_bld_exclude_children').html(html);

        // pre-select existing children (for edit)
        selectedInclude.forEach(function (id) {
            jQuery('#rise_bld_include_children option[value="' + id + '"]').prop('selected', true);
        });
        selectedExclude.forEach(function (id) {
            jQuery('#rise_bld_exclude_children option[value="' + id + '"]').prop('selected', true);
        });
    });
}
function enableToggleSelect(selector) {
    jQuery(document).on('mousedown', selector + ' option', function (e) {
        e.preventDefault();

        this.selected = !this.selected;

        jQuery(this).parent().trigger('change');
        return false;
    });
}
jQuery(function (jQuery) {
    jQuery(document).on('change', 'select[name="parent_cat"]', function () {
        var parent = jQuery(this).val();
        populateChildren(parent);
    });

    // Also support old settings page inputs if any
    jQuery(document).on('change', "select[name='rise_bld_category']", function () {
        var parent = jQuery(this).val();
        populateChildren(parent);
    });

    // On page load: prefill children if editing
    if (typeof rise_bld_editing !== 'undefined') {
        var parentCat = jQuery('select[name="parent_cat"]').val();
        var includeChildren = rise_bld_editing.include_children || [];
        var excludeChildren = rise_bld_editing.exclude_children || [];
        populateChildren(parentCat, includeChildren, excludeChildren);
    }

    enableToggleSelect('#rise_bld_include_children');
    enableToggleSelect('#rise_bld_exclude_children');

});
