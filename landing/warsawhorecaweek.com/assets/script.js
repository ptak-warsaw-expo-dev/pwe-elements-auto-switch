(function () {
    // ---- language toggle ----
    var root = document.getElementById('pweLandingWeek');
    var lang = (root && root.getAttribute('data-lang')) || 'en';
    var btn = document.getElementById('langBtn');

    document.querySelectorAll('[data-en]').forEach(function (el) {
        el.setAttribute('data-pl', el.innerHTML);
    });

    function setLang(l) {
        lang = l;

        document.querySelectorAll('[data-en]').forEach(function (el) {
            el.innerHTML = el.getAttribute(l === 'en' ? 'data-en' : 'data-pl');
        });

        var q = document.getElementById('catQ');
        if (q) {
            q.placeholder = l === 'en'
                ? q.getAttribute('data-ph-en')
                : 'Szukaj firmy…';
        }

        if (btn) {
            btn.textContent = l === 'en' ? 'PL' : 'EN';
        }

        document.documentElement.lang = l;
    }

    // ---- nav ----
    var nav = document.getElementById('nav');

    function onScroll() {
        if (nav) {
            nav.classList.toggle('solid', window.scrollY > 60);
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ---- count-up ----
    var counted = false;

    function countUp() {
        if (counted) {
            return;
        }

        counted = true;

        document.querySelectorAll('.stat .num').forEach(function (el) {
            var raw = el.getAttribute('data-count');
            var match = raw.match(/^([\d\s]*\d)(.*)$/);

            if (!match) {
                return;
            }

            var target = parseInt(match[1].replace(/\s/g, ''), 10);
            var suffix = match[2];
            var start = null;

            function formatNumber(n) {
                return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            function step(timestamp) {
                if (!start) {
                    start = timestamp;
                }

                var progress = Math.min(1, (timestamp - start) / 1400);
                var easing = 1 - Math.pow(1 - progress, 3);

                el.textContent = formatNumber(Math.round(target * easing)) + suffix;

                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            }

            requestAnimationFrame(step);
        });
    }

    var stats = document.getElementById('stats');

    if (stats && 'IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            if (entries[0].isIntersecting) {
                countUp();
            }
        }, { threshold: 0.3 }).observe(stats);
    } else if (stats) {
        countUp();
    }

    // ---- halls ----
    var legend = document.getElementById('hallsLegend');

    if (legend) {
        var tip = document.getElementById('hallsTip');
        var svg = document.getElementById('hallsSvg');
        var hallNames = {};

        legend.querySelectorAll('li').forEach(function (li) {
            hallNames[li.getAttribute('data-hall')] = li.querySelector('span:last-child').textContent;
        });

        function highlightHall(hall) {
            ['A', 'B', 'C', 'D', 'E', 'F'].forEach(function (name) {
                var group = svg.querySelector('#' + name);

                if (!group) {
                    return;
                }

                group.classList.toggle('hl', name === hall);
                group.classList.toggle('dim', !!hall && name !== hall);
            });

            legend.querySelectorAll('li').forEach(function (li) {
                li.classList.toggle('on', li.getAttribute('data-hall') === hall);
            });

            if (hall) {
                tip.textContent = (lang === 'en' ? 'Hall ' : 'Hala ') + hall + ' · ' + hallNames[hall];
                tip.classList.add('on');
            } else {
                tip.classList.remove('on');
            }
        }

        ['A', 'B', 'C', 'D', 'E', 'F'].forEach(function (name) {
            var group = svg.querySelector('#' + name);

            if (!group) {
                return;
            }

            group.addEventListener('mouseenter', function () {
                highlightHall(name);
            });

            group.addEventListener('mouseleave', function () {
                highlightHall(null);
            });
        });

        legend.querySelectorAll('li').forEach(function (li) {
            li.addEventListener('mouseenter', function () {
                highlightHall(li.getAttribute('data-hall'));
            });

            li.addEventListener('mouseleave', function () {
                highlightHall(null);
            });
        });
    }

    // ---- photo strip ----
    var strip = document.getElementById('strip');

    if (strip) {
        document.querySelectorAll('.strip-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                strip.scrollBy({
                    left: (button.classList.contains('next') ? 1 : -1) * (strip.clientWidth * 0.8),
                    behavior: 'smooth'
                });
            });
        });
    }

    // ---- catalog ----
    var dataElement = document.getElementById('catData');
    var data = dataElement ? JSON.parse(dataElement.textContent) : { y26: [], y27: [], l26: [], l27: [] };
    var year = 'y26';
    var hall = '';
    var query = '';
    var shown = 0;
    var pageSize = 48;

    var n26 = document.getElementById('n26');
    if (n26) {
        n26.textContent = data.y26.length;
    }

    var grid = document.getElementById('catGrid');
    var more = document.getElementById('catMore');
    var count = document.getElementById('catCount');

    function normalize(value) {
        return (value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[̀-ͯ]/g, '');
    }

    function filteredRows() {
        var rows = data[year];
        var normalizedQuery = normalize(query);

        return rows.filter(function (row) {
            var rowHall = year === 'y27'
                ? (row[1] || '').charAt(0)
                : row[1];

            if (hall && rowHall !== hall) {
                return false;
            }

            if (
                normalizedQuery &&
                normalize(row[0]).indexOf(normalizedQuery) < 0 &&
                (year !== 'y26' || normalize(row[5]).indexOf(normalizedQuery) < 0)
            ) {
                return false;
            }

            return true;
        });
    }

    function createCard(row) {
        var name;
        var stand;
        var hallLabel;
        var logo;
        var website;
        var branch = '';

        if (year === 'y27') {
            name = row[0];
            stand = row[1];
            hallLabel = (row[1] || '').charAt(0);
            logo = row[2] ? data.l27[row[2]] : '';
            website = row[3];
        } else {
            name = row[0];
            hallLabel = row[1];
            stand = row[2];
            logo = row[3] || '';
            website = row[4];
            branch = row[5];
        }

        var logoHtml = logo
            ? '<img src="' + logo + '" alt="" loading="lazy">'
            : '<div class="ph">' + name.charAt(0).toUpperCase() + '</div>';

        var meta = '';

        if (hallLabel) {
            meta += (lang === 'en' ? 'Hall ' : 'Hala ') + '<b>' + hallLabel + '</b>';
        }

        if (stand) {
            meta += ' · ' + (lang === 'en' ? 'Stand ' : 'Stoisko ') + '<b>' + stand + '</b>';
        }

        var element = document.createElement(website ? 'a' : 'div');
        element.className = 'ex';

        if (website) {
            element.href = website;
            element.target = '_blank';
            element.rel = 'noopener';
        }

        element.innerHTML =
            '<div class="lg">' + logoHtml + '</div>' +
            '<div class="nm" title="' + name.replace(/"/g, '&quot;') + '">' + name + '</div>' +
            (branch ? '<div class="st">' + branch + '</div>' : '') +
            '<div class="st">' + meta + '</div>';

        return element;
    }

    function renderCatalog(reset) {
        if (!grid || !more || !count) {
            return;
        }

        var rows = filteredRows();

        if (reset) {
            grid.innerHTML = '';
            shown = 0;
        }

        var fragment = document.createDocumentFragment();

        rows.slice(shown, shown + pageSize).forEach(function (row) {
            fragment.appendChild(createCard(row));
        });

        grid.appendChild(fragment);
        shown = Math.min(rows.length, shown + pageSize);

        more.style.display = shown < rows.length ? '' : 'none';
        count.textContent = shown + ' / ' + rows.length;
    }

    document.querySelectorAll('#hallChips .chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('#hallChips .chip').forEach(function (item) {
                item.classList.remove('active');
            });

            chip.classList.add('active');
            hall = chip.getAttribute('data-h');
            renderCatalog(true);
        });
    });

    var catalogSearch = document.getElementById('catQ');

    if (catalogSearch) {
        catalogSearch.addEventListener('input', function (event) {
            query = event.target.value;
            renderCatalog(true);
        });
    }

    if (more) {
        more.addEventListener('click', function () {
            renderCatalog(false);
        });
    }

    // Initialize translations only after catalog variables exist.
    setLang(lang);
    renderCatalog(true);
}());
