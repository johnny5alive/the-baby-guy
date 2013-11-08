document.observe('dom:loaded', function () {
    var dashboardEnabledSelect = $('awaheadmetrics_dashboard_enabled');
    if (dashboardEnabledSelect) {
        var checkIsDashboardEnabled = function () {
            if (dashboardEnabledSelect.value == true) {
                $('awaheadmetrics_dashboard_domain').addClassName('required-entry');
            } else {
                $('awaheadmetrics_dashboard_domain').removeClassName('required-entry');
            }
        }
        dashboardEnabledSelect.observe('change', checkIsDashboardEnabled);
        checkIsDashboardEnabled();
    }
});
