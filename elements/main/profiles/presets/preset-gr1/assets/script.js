document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".pwe-profiles__tab");
    const tabContents = document.querySelectorAll(".pwe-profiles__tabs-content");

    function openTab(tabName, clickedTab) {
        tabContents.forEach(content => content.classList.remove("active"));
        tabs.forEach(tab => tab.classList.remove("active"));

        const activeContent = document.getElementById(tabName);
        if (activeContent) activeContent.classList.add("active");
        clickedTab.classList.add("active");
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            const tabName = this.dataset.tab;
            openTab(tabName, this);
        });
    });

    document.querySelectorAll(".pwe-profiles__tab-text").forEach(tabText => {
        const ul = tabText.querySelector("ul");
        const btn = tabText.querySelector(".pwe-profiles__show-more-btn");

        if (ul && btn) {
            const items = ul.querySelectorAll("li");

            let limit = window.innerWidth < 570 ? 6 : 10;

            if (items.length > limit) {
                ul.classList.add("collapsed");

                btn.addEventListener("click", function () {
                    ul.classList.toggle("collapsed");
                    if (ul.classList.contains("collapsed")) {
                        btn.innerHTML = "więcej ▼";
                    } else {
                        btn.innerHTML = "zwiń ▲";
                    }
                });
            } else {
                btn.style.display = "none";
            }
        } else if (btn) {
            btn.style.display = "none";
        }
    });
});