// document.addEventListener("DOMContentLoaded", function () {

//     /* ================= Sidebar Toggle & Persistence ================= */
//     const sidebarToggleBtns = document.querySelectorAll(".sidebar-toggle");
//     const sidebar = document.querySelector(".sidebar");

//     // ১. পেজ লোড হওয়ার সময় আগের স্টেট চেক করা
//     const sidebarState = localStorage.getItem("sidebar-state");

//     if (sidebarState === "collapsed") {
//         sidebar.classList.add("collapsed");
//     } else if (sidebarState === "expanded") {
//         sidebar.classList.remove("collapsed");
//     } else {
//         // যদি আগে কখনো সেট করা না থাকে, তবে মোবাইল ও ডেস্কটপের জন্য ডিফল্ট লজিক
//         if (window.innerWidth < 768) {
//             sidebar.classList.add("collapsed");
//         } else {
//             sidebar.classList.remove("collapsed");
//         }
//     }

//     // ২. বাটনে ক্লিক করলে স্টেট সেভ করা
//     sidebarToggleBtns.forEach(btn => {
//         btn.addEventListener("click", () => {
//             sidebar.classList.toggle("collapsed");

//             // স্টেটটি localStorage এ সেভ করা
//             if (sidebar.classList.contains("collapsed")) {
//                 localStorage.setItem("sidebar-state", "collapsed");
//             } else {
//                 localStorage.setItem("sidebar-state", "expanded");
//             }
//         });
//     });

//     /* ================= Submenu Logic ================= */
//     const submenuParents = document.querySelectorAll(".has-submenu > .menu-link");

//     submenuParents.forEach(link => {
//         link.addEventListener("click", function (e) {
//             e.preventDefault();

//             const parent = this.parentElement;
//             const submenu = parent.querySelector(".submenu");

//             document.querySelectorAll(".has-submenu").forEach(item => {
//                 if (item !== parent) {
//                     item.classList.remove("open");
//                     const sub = item.querySelector(".submenu");
//                     if (sub) sub.style.maxHeight = null;
//                 }
//             });

//             if (parent.classList.contains("open")) {
//                 parent.classList.remove("open");
//                 submenu.style.maxHeight = null;
//             } else {
//                 parent.classList.add("open");
//                 submenu.style.maxHeight = submenu.scrollHeight + "px";
//             }
//         });
//     });

//     // Active submenu open on load
//     const activeSubmenuLink = document.querySelector(".submenu .menu-link.active");
//     if (activeSubmenuLink) {
//         const parentMenu = activeSubmenuLink.closest(".has-submenu");
//         if (parentMenu) {
//             parentMenu.classList.add("open");
//             const submenu = parentMenu.querySelector(".submenu");
//             if (submenu) submenu.style.maxHeight = submenu.scrollHeight + "px";
//         }
//     }

//     /* ================= Logout Button Loading ================= */
//     const logoutForm = document.getElementById("logout-form");
//     if (logoutForm) {
//         logoutForm.addEventListener("submit", function () {
//             const btn = document.getElementById("logout-btn");
//             const text = document.getElementById("logout-text");
//             if (btn && text) {
//                 btn.disabled = true;
//                 text.innerText = "Logging out...";
//             }
//         });
//     }

//     /* ================= Delete Modal ================= */
//     const deleteModal = document.getElementById("deleteModal");
//     if (deleteModal) {
//         deleteModal.addEventListener("show.bs.modal", function (event) {
//             const button = event.relatedTarget;
//             const deleteUrl = button?.getAttribute("data-url");
//             const deleteForm = document.getElementById("delete-form");

//             if (deleteForm && deleteUrl) {
//                 deleteForm.setAttribute("action", deleteUrl);
//             }
//         });
//     }
// });

document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.querySelector(".sidebar");
    const sidebarState = localStorage.getItem("sidebar-state");
    const isMobile = window.innerWidth < 768;

    if (sidebarState === "collapsed" || (!sidebarState && isMobile)) {
        sidebar.classList.add("collapsed");
    } else {
        sidebar.classList.remove("collapsed");
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.documentElement.classList.remove('sidebar-is-collapsed');
        });
    });

    const sidebarToggleBtns = document.querySelectorAll(".sidebar-toggle");

    sidebarToggleBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");

            if (sidebar.classList.contains("collapsed")) {
                localStorage.setItem("sidebar-state", "collapsed");
            } else {
                localStorage.setItem("sidebar-state", "expanded");
            }
        });
    });

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

    const activeSubmenuLink = document.querySelector(".submenu .menu-link.active");
    if (activeSubmenuLink) {
        const parentMenu = activeSubmenuLink.closest(".has-submenu");
        if (parentMenu) {
            parentMenu.classList.add("open");
            const submenu = parentMenu.querySelector(".submenu");
            if (submenu) submenu.style.maxHeight = submenu.scrollHeight + "px";
        }
    }

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
});