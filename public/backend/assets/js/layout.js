document.addEventListener("DOMContentLoaded", function () {

    /* ================= Sidebar Toggle ================= */
    const sidebarToggleBtns = document.querySelectorAll(".sidebar-toggle");
    const sidebar = document.querySelector(".sidebar");

    sidebarToggleBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
        });
    });

    if (window.innerWidth > 768) {
        sidebar.classList.remove("collapsed");
    }

    /* ================= Submenu ================= */
    const submenuParents = document.querySelectorAll(".has-submenu > .menu-link");

    submenuParents.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const parent = this.parentElement;
            const submenu = parent.querySelector(".submenu");

            document.querySelectorAll(".has-submenu").forEach(item => {
                if (item !== parent) {
                    item.classList.remove("open");
                    const sub = item.querySelector(".submenu");
                    if (sub) sub.style.maxHeight = null;
                }
            });

            if (parent.classList.contains("open")) {
                parent.classList.remove("open");
                submenu.style.maxHeight = null;
            } else {
                parent.classList.add("open");
                submenu.style.maxHeight = submenu.scrollHeight + "px";
            }
        });
    });

    // Open active submenu on page load
    const activeSubmenuLink = document.querySelector(".submenu .menu-link.active");
    if (activeSubmenuLink) {
        const parentMenu = activeSubmenuLink.closest(".has-submenu");
        if (parentMenu) {
            parentMenu.classList.add("open");
            const submenu = parentMenu.querySelector(".submenu");
            if (submenu) submenu.style.maxHeight = submenu.scrollHeight + "px";
        }
    }

    /* ================= Logout Button Loading ================= */
    const logoutForm = document.getElementById("logout-form");
    if (logoutForm) {
        logoutForm.addEventListener("submit", function () {
            const btn = document.getElementById("logout-btn");
            const text = document.getElementById("logout-text");
            if (btn && text) {
                btn.disabled = true;
                text.innerText = "Logging out...";
            }
        });
    }

    /* ================= Delete Modal ================= */
    const deleteModal = document.getElementById("deleteModal");
    if (deleteModal) {
        deleteModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const deleteUrl = button?.getAttribute("data-url");
            const deleteForm = document.getElementById("delete-form");

            if (deleteForm && deleteUrl) {
                deleteForm.setAttribute("action", deleteUrl);
            }
        });
    }

});
