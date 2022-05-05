const sites = [];

/**
 * Display an error message.
 *
 * @param {string} message Alert message
 */
function showError(message) {
    const div = document.createElement("div");
    div.classList.add("alert");
    div.classList.add("alert-danger");
    div.setAttribute("role", "alert");
    div.textContent = message;
    document.querySelector("main").appendChild(div);
}

/**
 * Load the site list.
 *
 * @return Promise object
 */
function loadSites() {
    console.log("loadSites");
    return axios
        .get("/api/sites")
        .then((response) => {
            for (const site of response.data) {
                appendSite(site);
            }
            reloadStatus();
        })
        .catch((ex) => {
            showError(ex.message);
        });
}

/**
 * Add a site to list.
 *
 * @param {string} site A string that identifies the site
 */
function appendSite(site) {
    sites.push(site);
    // Clone template
    const template = document.querySelector("#site-list-item");
    const clone = template.content.cloneNode(true);
    // Create a link
    const anker = clone.querySelector("a");
    const label = anker.querySelector(".status");
    anker.id = "site-" + site;
    anker.href = "/monitor/" + site;
    label.textContent = site;
    // Add link to list
    const listElement = document.querySelector("#site-list");
    listElement.appendChild(anker);
}

/**
 * Recursively display a status of sites.
 *
 * @returns void
 */
function reloadStatus() {
    if (!sites.length) {
        return;
    }
    const totalInterval = 30_000;
    const interval = totalInterval / sites.length;
    const site = sites.shift();
    sites.push(site);
    axios
        .get(`/api/alive-log/${site}/status`)
        .then((response) => {
            const status = response.data.status.toUpperCase();
            const badge = document.querySelector(`#site-${site} .badge`);
            badge.textContent = site;
            badge.textContent = status;
            switchBadgeStyle(badge, status);
        })
        .finally(() => {
            setInterval(() => {
                reloadStatus();
            }, interval);
        });
}

/**
 * Switch status badge styles.
 *
 * @param {Element} badge A status badge
 * @param {string} status A status
 */
function switchBadgeStyle(badge, status) {
    switch (status.toUpperCase()) {
        case "ALIVE":
        case "OK":
            badge.classList.add("bg-success");
            badge.classList.remove("bg-warning");
            badge.classList.remove("bg-danger");
            badge.classList.remove("text-dark");
            break;
        case "WARNING":
            badge.classList.remove("bg-success");
            badge.classList.add("bg-warning");
            badge.classList.remove("bg-danger");
            badge.classList.add("text-dark");
            break;
        default:
            badge.classList.remove("bg-success");
            badge.classList.remove("bg-warning");
            badge.classList.add("bg-danger");
            badge.classList.remove("text-dark");
            break;
    }
}
