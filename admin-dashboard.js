const modals = document.querySelectorAll(".admin-modal");

const openModal = (id) => {
    const modal = document.getElementById(id);
    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
};

const closeModals = () => {
    modals.forEach((modal) => {
        modal.classList.remove("is-open");
        modal.setAttribute("aria-hidden", "true");
    });
};

const setValue = (form, name, value = "") => {
    const field = form.elements[name];
    if (field) field.value = value;
};

document.querySelectorAll("[data-close-modal]").forEach((button) => {
    button.addEventListener("click", closeModals);
});

modals.forEach((modal) => {
    modal.addEventListener("click", (event) => {
        if (event.target === modal) closeModals();
    });
});

document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeModals();
});

document.querySelectorAll("[data-open-event-modal]").forEach((button) => {
    button.addEventListener("click", () => {
        const form = document.querySelector("#event-modal form");
        form.reset();
        setValue(form, "id", button.dataset.id || "");
        setValue(form, "title", button.dataset.title || "");
        setValue(form, "event_date", button.dataset.eventDate || "");
        setValue(form, "description", button.dataset.description || "");
        setValue(form, "location", button.dataset.location || "");
        setValue(form, "location_icon", button.dataset.locationIcon || "location_on");
        setValue(form, "sort_order", button.dataset.sortOrder || "0");
        document.getElementById("event-modal-title").textContent = button.dataset.mode === "add" ? "Add Event" : "Edit Event";
        openModal("event-modal");
    });
});

document.querySelectorAll("[data-open-project-modal]").forEach((button) => {
    button.addEventListener("click", () => {
        const form = document.querySelector("#project-modal form");
        form.reset();
        setValue(form, "id", button.dataset.id || "");
        setValue(form, "title", button.dataset.title || "");
        setValue(form, "description", button.dataset.description || "");
        setValue(form, "tags", button.dataset.tags || "");
        setValue(form, "sort_order", button.dataset.sortOrder || "0");
        document.getElementById("project-modal-title").textContent = button.dataset.mode === "add" ? "Add Project" : "Edit Project";
        openModal("project-modal");
    });
});

document.querySelectorAll("[data-open-team-modal]").forEach((button) => {
    button.addEventListener("click", () => {
        const form = document.querySelector("#team-modal form");
        const imageWrap = document.getElementById("team-current-image-wrap");
        const image = document.getElementById("team-current-image");
        form.reset();
        setValue(form, "id", button.dataset.id || "");
        setValue(form, "name", button.dataset.name || "");
        setValue(form, "role", button.dataset.role || "");
        setValue(form, "photo_path", button.dataset.photoPath || "");
        setValue(form, "bio", button.dataset.bio || "");
        setValue(form, "sort_order", button.dataset.sortOrder || "0");
        document.getElementById("team-modal-title").textContent = button.dataset.mode === "add" ? "Add Team Member" : "Edit Team Member";
        image.src = button.dataset.photoPath || "";
        imageWrap.hidden = !button.dataset.photoPath;
        openModal("team-modal");
    });
});

const teamImageInput = document.querySelector("#team-modal input[name='team_image']");
teamImageInput.addEventListener("change", () => {
    const file = teamImageInput.files[0];
    if (!file) return;
    const imageWrap = document.getElementById("team-current-image-wrap");
    const image = document.getElementById("team-current-image");
    image.src = URL.createObjectURL(file);
    imageWrap.hidden = false;
});

document.querySelectorAll("[data-open-preview]").forEach((button) => {
    button.addEventListener("click", () => {
        document.getElementById("preview-kicker").textContent = button.dataset.kicker || "";
        document.getElementById("preview-heading").textContent = button.dataset.title || "";
        document.getElementById("preview-description").textContent = button.dataset.description || "";
        document.getElementById("preview-meta").textContent = button.dataset.meta || "";
        const previewImage = document.getElementById("preview-image");
        previewImage.src = button.dataset.image || "";
        previewImage.alt = button.dataset.title || "";
        previewImage.hidden = !button.dataset.image;
        openModal("preview-modal");
    });
});

document.querySelectorAll("[data-open-member-preview]").forEach((button) => {
    button.addEventListener("click", () => {
        const photo = document.getElementById("member-preview-photo");
        const avatar = document.getElementById("member-preview-avatar");
        const name = button.dataset.name || "";

        document.getElementById("member-preview-name").textContent = name;
        document.getElementById("member-preview-email").textContent = button.dataset.email || "No email";
        const memberStatus = document.getElementById("member-preview-status");
        const status = button.dataset.status || "";
        memberStatus.textContent = status;
        memberStatus.className = `status-pill status-pill-${status === "approved" ? "success" : status === "rejected" ? "danger" : "warning"}`;
        document.getElementById("member-preview-phone").textContent = button.dataset.phone || "No phone";
        document.getElementById("member-preview-student-id").textContent = button.dataset.studentId || "Not provided";
        document.getElementById("member-preview-department").textContent = button.dataset.department || "Not provided";
        document.getElementById("member-preview-batch").textContent = button.dataset.batch || "Not provided";
        document.getElementById("member-preview-submitted").textContent = button.dataset.submitted || "";
        document.getElementById("member-preview-skills").textContent = button.dataset.skills || "Not provided";
        document.getElementById("member-preview-reason").textContent = button.dataset.reason || "Not provided";

        if (button.dataset.photo) {
            photo.src = button.dataset.photo;
            photo.alt = name;
            photo.hidden = false;
            avatar.hidden = true;
        } else {
            photo.hidden = true;
            avatar.hidden = false;
            avatar.textContent = name.charAt(0).toUpperCase();
        }

        openModal("member-preview-modal");
    });
});

document.querySelectorAll("[data-open-delete]").forEach((button) => {
    button.addEventListener("click", () => {
        const form = document.querySelector("#delete-modal form");
        setValue(form, "type", button.dataset.type);
        setValue(form, "id", button.dataset.id);
        document.getElementById("delete-copy").textContent = `Delete "${button.dataset.title}"? This cannot be undone.`;
        openModal("delete-modal");
    });
});

const tabTargets = document.querySelectorAll("[data-admin-tab-target]");
const tabPanels = document.querySelectorAll("[data-admin-panel]");
const validTabs = ["members", "events", "projects", "teams"];

const showTab = (target, updateHash = true) => {
    const nextTarget = validTabs.includes(target) ? target : "members";

    tabPanels.forEach((panel) => {
        panel.classList.toggle("is-active", panel.dataset.adminPanel === nextTarget);
    });

    document.querySelectorAll(".tab-btn[data-admin-tab-target]").forEach((tab) => {
        tab.classList.toggle("active", tab.dataset.adminTabTarget === nextTarget);
    });

    if (updateHash) {
        history.replaceState(null, "", `#${nextTarget}`);
    }
};

tabTargets.forEach((tab) => {
    tab.addEventListener("click", (event) => {
        event.preventDefault();
        showTab(tab.dataset.adminTabTarget);
    });
});

showTab(window.location.hash.replace("#", ""), false);
