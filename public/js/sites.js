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
 */
async function loadSites() {
    await axios
        .get("/api/sites")
        .then((response) => {
            for (const site of response.data.sites) {
                appendSite(site);
            }
            if (response.data.error.length) {
                showError(response.data.error[0]);
                // Create example data
                axios.get("/api/site/add-example").then(() => {
                    location.reload();
                });
            }
        })
        .catch((ex) => {
            showError(ex.message);
            throw ex;
        });

    await loadStatus();
    setTimeout(() => monitor(), 1000);
}

/**
 * Add a site to list.
 *
 * @param {string} site A string that identifies the site
 */
function appendSite(site) {
    console.log("sites", sites);
    console.log("site", site);
    sites.push(site);
    // Clone template
    const template = document.querySelector("#site-list-item");
    const clone = template.content.cloneNode(true);
    // Create a link
    const anker = clone.querySelector("a");
    const siteNameObject = anker.querySelector(".site-name");
    anker.id = "site-" + site.id;
    anker.href = "/monitor/" + site.slug;
    siteNameObject.textContent = site.name;
    // Add link to list
    const listElement = document.querySelector("#site-list");
    listElement.appendChild(anker);
}

/**
 * Display a status of sites.
 *
 * @returns void
 */
async function loadStatus() {
    for (let index = 0; index < sites.length; index++) {
        const site = sites[index];
        await axios.get(`/api/site/${site.slug}/status`).then((response) => {
            displaySiteStatus(site.id, response.data.status);
        });
    }
}

/**
 * Recursively display a status of sites.
 *
 * @returns void
 */
function monitor() {
    if (!sites.length) {
        return;
    }

    const totalInterval = 30_000;
    const minInterval = 500;
    const interval = Math.max(totalInterval / sites.length, minInterval);

    const site = sites.shift();
    sites.push(site);
    axios
        .get(`/api/site/${site.slug}/status`)
        .then((response) => {
            displaySiteStatus(site.id, response.data.status);
        })
        .finally(() => {
            setTimeout(() => {
                monitor();
            }, interval);
        });
}

/**
 * Display a status of site.
 *
 * @returns void
 */
function displaySiteStatus(siteId, status) {
    const statusLabel = status.toUpperCase();
    const badge = document.querySelector(`#site-${siteId} .badge`);
    badge.textContent = statusLabel;
    switchBadgeStyle(badge, statusLabel);
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
