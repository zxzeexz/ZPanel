/* themes/default/js/theme.js */
/*	ZPanel Default theme javascripts */
/*	Revision 1 [9-12-2025] */
/*	Zee ^_~ */

document.addEventListener("DOMContentLoaded", function () {
    console.log("Default theme loaded.");

    // For later: Sidebar toggle
	/*
    const sidebarToggle = document.querySelector("#sidebarToggle");
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", function (e) {
            e.preventDefault();
            document.body.classList.toggle("sidebar-collapsed");
        });
    }
	*/

    // Smooth scroll for internal anchors
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute("href")).scrollIntoView({
                behavior: "smooth"
            });
        });
    });

    // Tooltip styling
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

    // -----------------------------
    // Auto-dismiss alerts
    // -----------------------------
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.classList.contains("show")) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000); // dismiss after 5s
    });
});
