(function () {
    'use strict';

    function initialize() {
        if (typeof window.Chart !== 'function') return;

        function draw(element) {
            const canvas = element.querySelector('canvas');
            if (Chart.getChart(canvas)) return;
            const rows = JSON.parse(element.dataset.statistics);
            const values = rows.map(row => row.value);
            const unit = element.dataset.unit;
            const format = value => new Intl.NumberFormat('pl-PL').format(value);
            const labels = values.map(value => format(value) + (unit ? ' ' + unit : ''));
            const style = getComputedStyle(element.closest('.chart-stat'));
            const colors = rows.map(row => row.edition === 'current'
                ? (style.getPropertyValue('--accent-color').trim())
                : (style.getPropertyValue('--accent_light_color').trim()));
            const font = '700 12px sans-serif';
            const context = canvas.getContext('2d');
            if (!context) return;
            context.font = font;
            const minBarLength = Math.ceil(Math.max(...labels.map(label => context.measureText(label).width))) + 18;
            canvas.hidden = false;

            try {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: rows.map(row => row.year),
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderSkipped: false,
                            borderRadius: 5,
                            barThickness: 28,
                            minBarLength: minBarLength,
                            hoverBackgroundColor: colors
                        }]
                    },
                    plugins: [{
                        id: 'summaryValueLabels',
                        afterDatasetsDraw(chart) {
                            const ctx = chart.ctx;
                            ctx.save();
                            ctx.font = font;
                            ctx.textBaseline = 'middle';
                            ctx.textAlign = 'right';
                            ctx.fillStyle = '#fff';
                            chart.getDatasetMeta(0).data.forEach((bar, index) => {
                                if (Math.abs(bar.x - bar.base) >= ctx.measureText(labels[index]).width + 18) {
                                    ctx.fillText(labels[index], bar.x - 9, bar.y);
                                }
                            });
                            ctx.restore();
                        }
                    }],
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : {duration: 1100},
                        layout: {padding: {top: 3, right: 15}},
                        plugins: {legend: {display: false}, tooltip: {enabled: false}},
                        scales: {
                            y: {display: false},
                            x: {
                                min: 0,
                                max: Math.max(...values, 1) * 1.17,
                                border: {display: false},
                                grid: {color: style.getPropertyValue('--chart-grid').trim(), drawTicks: false},
                                ticks: {
                                    maxTicksLimit: 5,
                                    color: style.getPropertyValue('--chart-text').trim(),
                                    font: {size: 10},
                                    callback: value => new Intl.NumberFormat('pl-PL', {notation: 'compact', maximumFractionDigits: 1}).format(value)
                                }
                            }
                        }
                    }
                });
                element.querySelector('.statistics-fallback').hidden = true;
            } catch (error) {
                const chart = Chart.getChart(canvas);
                if (chart) chart.destroy();
                canvas.hidden = true;
            }
        }

        const charts = document.querySelectorAll('.pwe-post-summary .stat-chart[data-statistics]');

        if (!('IntersectionObserver' in window)) {
            charts.forEach(draw);
            return;
        }

        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                observer.unobserve(entry.target);
                draw(entry.target);
            });
        }, {threshold: 0.15});

        charts.forEach(function (element) {
            observer.observe(element);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }
})();
