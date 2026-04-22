<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Real-time environmental air quality monitoring dashboard — tracks PM 2.5, temperature, humidity, and CO2 levels.">
    <title>Environmental Overview Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-slate-900 dark:text-white min-h-screen p-8 leading-normal bg-slate-100 dark:bg-[#0B0F19]">

    <div class="max-w-[1400px] mx-auto">

        <!-- ── Controls: Theme & Language ─────────────────────── -->
        <div class="flex justify-start md:justify-end gap-4 mb-4 md:-mb-12 relative z-10 animate-[fadeInDown_0.8s_ease-out]">

            <button id="lang-toggle"
                class="bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/5 text-slate-900 dark:text-white px-3 py-2 rounded-full font-['Outfit'] font-semibold text-sm cursor-pointer backdrop-blur-sm flex items-center gap-2 hover:bg-black/10 dark:hover:bg-white/20"
                title="Switch language">
                <!-- Flag image of the language you will switch TO -->
                <img id="lang-flag"
                    src="https://flagcdn.com/w40/id.png"
                    alt="Switch to Indonesian"
                    class="w-6 h-auto rounded-sm shadow-sm">
                <span id="lang-label" class="text-xs font-bold tracking-wider">ID</span>
            </button>

            <button id="theme-toggle"
                class="bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/5 text-slate-900 dark:text-white px-4 py-2 rounded-full font-['Outfit'] font-semibold text-sm cursor-pointer backdrop-blur-sm flex items-center gap-2 hover:bg-black/10 dark:hover:bg-white/20"
                title="Toggle Theme">
                <i class="ph ph-sun text-xl" id="theme-icon"></i>
                <span id="theme-text">Light</span>
            </button>
        </div>

        <!-- ── Header ──────────────────────────────────────────── -->
        <header class="mb-10 animate-[fadeInDown_0.8s_ease-out]">
            <h1 class="font-['Outfit'] text-4xl md:text-5xl font-bold tracking-tight bg-gradient-to-r from-blue-900 to-blue-500 dark:from-white dark:to-blue-300 bg-clip-text text-transparent mb-1"
                id="t-title">Environmental Overview</h1>
            <div class="subtitle text-sm font-semibold text-emerald-500 tracking-[1.5px] flex items-center gap-2 uppercase"
                id="t-subtitle">REAL-TIME MONITORING ACTIVE</div>
        </header>

        <!-- ── Sensor Cards ────────────────────────────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">

            <!-- Card 1: Air Quality -->
            <div id="card-aq"
                class="bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-6 flex flex-col shadow relative overflow-hidden opacity-0 animate-[fadeInUp_0.8s_ease-out_0.1s_forwards] hover:-translate-y-[5px] hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)] hover:bg-white dark:hover:bg-[rgba(30,58,138,0.4)] transition-[box-shadow,background-color,border-color,transform] duration-300 cursor-default">

                <div class="flex justify-between items-start mb-4">
                    <div class="text-[2rem] leading-none text-blue-500 dark:text-blue-400 bg-blue-500/10 p-3 rounded-2xl">
                        <i class="ph ph-gauge"></i>
                    </div>
                    <div class="w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white"
                        title="Normal"></div>
                </div>

                <div class="text-base text-slate-500 dark:text-slate-400 font-medium mb-2" id="t-aq-title">Air Quality</div>

                <div class="font-['Outfit'] text-[2.25rem] font-bold mb-1 flex items-baseline gap-1">
                    <span id="val-aq">45.2</span>
                    <span class="text-base text-slate-500 dark:text-slate-400 font-medium">PPM</span>
                </div>

                <div class="text-sm font-medium text-emerald-500" id="t-aq-desc">Optimal Range</div>
            </div>

            <!-- Card 2: Temperature -->
            <div id="card-temp"
                class="bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-6 flex flex-col shadow relative overflow-hidden opacity-0 animate-[fadeInUp_0.8s_ease-out_0.2s_forwards] hover:-translate-y-[5px] hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)] hover:bg-white dark:hover:bg-[rgba(30,58,138,0.4)] transition-[box-shadow,background-color,border-color,transform] duration-300 cursor-default">

                <div class="flex justify-between items-start mb-4">
                    <div class="text-[2rem] leading-none text-blue-500 dark:text-blue-400 bg-blue-500/10 p-3 rounded-2xl">
                        <i class="ph ph-thermometer"></i>
                    </div>
                    <div class="w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white"
                        title="Normal"></div>
                </div>

                <div class="text-base text-slate-500 dark:text-slate-400 font-medium mb-2" id="t-temp-title">Temperature</div>

                <div class="font-['Outfit'] text-[2.25rem] font-bold mb-1 flex items-baseline gap-1">
                    <span id="val-temp">22.5</span>
                    <span class="text-base text-slate-500 dark:text-slate-400 font-medium">°C</span>
                </div>

                <div class="text-sm font-medium text-blue-500 dark:text-blue-400" id="t-temp-desc">Stable Ambient</div>
            </div>

            <!-- Card 3: Humidity -->
            <div id="card-hum"
                class="bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-6 flex flex-col shadow relative overflow-hidden opacity-0 animate-[fadeInUp_0.8s_ease-out_0.3s_forwards] hover:-translate-y-[5px] hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)] hover:bg-white dark:hover:bg-[rgba(30,58,138,0.4)] transition-[box-shadow,background-color,border-color,transform] duration-300 cursor-default">

                <div class="flex justify-between items-start mb-4">
                    <div class="text-[2rem] leading-none text-blue-500 dark:text-blue-400 bg-blue-500/10 p-3 rounded-2xl">
                        <i class="ph ph-drop"></i>
                    </div>
                    <div class="w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white"
                        title="Normal"></div>
                </div>

                <div class="text-base text-slate-500 dark:text-slate-400 font-medium mb-2" id="t-hum-title">Humidity</div>

                <div class="font-['Outfit'] text-[2.25rem] font-bold mb-1 flex items-baseline gap-1">
                    <span id="val-hum">62</span>
                    <span class="text-base text-slate-500 dark:text-slate-400 font-medium">%</span>
                </div>

                <div class="text-sm font-medium text-emerald-500" id="t-hum-desc">Optimal Range</div>
            </div>

            <!-- Card 4: CO2 Levels (Critical) -->
            <div id="card-co2" data-critical="true"
                class="bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-6 flex flex-col shadow relative overflow-hidden opacity-0 animate-[fadeInUp_0.8s_ease-out_0.4s_forwards] hover:-translate-y-[5px] hover:shadow-[0_10px_25px_rgba(59,130,246,0.15)] hover:bg-white dark:hover:bg-[rgba(30,58,138,0.4)] transition-[box-shadow,background-color,border-color,transform] duration-300 cursor-default">

                <div class="flex justify-between items-start mb-4">
                    <div id="co2-icon" class="text-[2rem] leading-none text-red-400 bg-red-500/10 p-3 rounded-2xl">
                        <i class="ph ph-warning-octagon"></i>
                    </div>
                    <div id="co2-status-dot"
                        class="w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-red-500 text-red-500 animate-[flash_1.5s_infinite]"
                        title="Critical"></div>
                </div>

                <div class="text-base text-slate-500 dark:text-slate-400 font-medium mb-2" id="t-co2-title">CO2 Levels</div>

                <div class="font-['Outfit'] text-[2.25rem] font-bold mb-1 flex items-baseline gap-1">
                    <span id="val-co2">850</span>
                    <span class="text-base text-slate-500 dark:text-slate-400 font-medium">PPM</span>
                </div>

                <div class="text-sm font-medium text-red-500" id="t-co2-desc">Critical Alert</div>
            </div>

        </div><!-- /cards-grid -->

        <!-- ── Chart Section ───────────────────────────────────── -->
        <section
            class="bg-white/80 dark:bg-[rgba(22,33,62,0.6)] backdrop-blur-xl border border-black/5 dark:border-white/5 rounded-[1.25rem] p-8 opacity-0 animate-[fadeInUp_0.8s_ease-out_0.6s_forwards] relative">

            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-1 gap-4 sm:gap-0">
                    <h2 class="font-['Outfit'] text-2xl font-semibold" id="t-chart-title">24-Hour Air Quality Trend</h2>

                    <div class="flex items-center gap-2 bg-blue-500/10 px-4 py-1.5 rounded-full border border-blue-500/15 text-sm font-semibold text-blue-500 dark:text-blue-400">
                        <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                        <span>PM 2.5</span>
                    </div>
                </div>

                <div class="text-sm text-slate-500 dark:text-slate-400 tracking-[0.5px] uppercase font-semibold"
                    id="t-sensor">SENSOR NODE: REDESMA-01</div>
            </div>

            <div class="relative h-[300px] md:h-[400px] w-full">
                <canvas id="airQualityChart"></canvas>
            </div>
        </section>

    </div><!-- /container -->

    <!-- ── Script ─────────────────────────────────────────────── -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Chart Setup ────────────────────────────────────────
            const ctx = document.getElementById('airQualityChart').getContext('2d');

            function getChartColors() {
                const isDark = document.documentElement.classList.contains('dark');
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, isDark ? 'rgba(59, 130, 246, 0.5)' : 'rgba(37, 99, 235, 0.4)');
                gradient.addColorStop(1, isDark ? 'rgba(59, 130, 246, 0.0)' : 'rgba(37, 99, 235, 0.0)');

                return {
                    gradient,
                    borderColor:  isDark ? '#3B82F6' : '#2563EB',
                    pointBg:      isDark ? '#0B0F19' : '#F1F5F9',
                    gridColor:    isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)',
                    textColor:    isDark ? '#94A3B8'  : '#64748B',
                    tooltipBg:    isDark ? 'rgba(15,23,42,0.9)'     : 'rgba(255,255,255,0.95)',
                    tooltipText:  isDark ? '#FFFFFF'  : '#0F172A',
                };
            }

            const hours      = Array.from({ length: 24 }, (_, i) => `${i}:00`);
            const dataPoints = [42, 45, 48, 50, 47, 44, 43, 41, 39, 38, 40, 42, 45, 49, 52, 55, 53, 50, 48, 46, 44, 45, 46, 45];

            let chartColors = getChartColors();

            let airQualityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: hours,
                    datasets: [{
                        label: 'PM 2.5 (PPM)',
                        data: dataPoints,
                        borderColor: chartColors.borderColor,
                        backgroundColor: chartColors.gradient,
                        borderWidth: 3,
                        pointBackgroundColor: chartColors.pointBg,
                        pointBorderColor: chartColors.borderColor,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: chartColors.tooltipBg,
                            titleColor: chartColors.tooltipText,
                            bodyColor: chartColors.textColor,
                            borderColor: 'rgba(128,128,128,0.2)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: (ctx) => `${ctx.parsed.y} PPM`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: chartColors.gridColor, drawBorder: false },
                            ticks: { color: chartColors.textColor, padding: 10, font: { family: "'Inter', sans-serif", size: 11 } },
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: chartColors.textColor, maxTicksLimit: 12, padding: 10, font: { family: "'Inter', sans-serif", size: 11 } },
                        },
                    },
                    interaction: { intersect: false, mode: 'index' },
                },
            });

            // Helper: push new colors into chart and update
            function applyChartColors() {
                chartColors = getChartColors();
                const ds = airQualityChart.data.datasets[0];
                ds.borderColor            = chartColors.borderColor;
                ds.backgroundColor        = chartColors.gradient;
                ds.pointBackgroundColor   = chartColors.pointBg;
                ds.pointBorderColor       = chartColors.borderColor;
                airQualityChart.options.scales.x.ticks.color             = chartColors.textColor;
                airQualityChart.options.scales.y.ticks.color             = chartColors.textColor;
                airQualityChart.options.scales.y.grid.color              = chartColors.gridColor;
                airQualityChart.options.plugins.tooltip.backgroundColor  = chartColors.tooltipBg;
                airQualityChart.options.plugins.tooltip.titleColor       = chartColors.tooltipText;
                airQualityChart.options.plugins.tooltip.bodyColor        = chartColors.textColor;
                airQualityChart.update();
            }

            // ── Theme Toggle ───────────────────────────────────────
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon      = document.getElementById('theme-icon');
            const themeText      = document.getElementById('theme-text');

            themeToggleBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');

                themeIcon.className  = isDark ? 'ph ph-sun text-xl' : 'ph ph-moon text-xl';
                themeText.textContent = isDark
                    ? translations[currentLang].theme_light
                    : translations[currentLang].theme_dark;

                applyChartColors();
            });

            // ── Translations ───────────────────────────────────────
            const translations = {
                en: {
                    theme_light:      'Light',
                    theme_dark:       'Dark',
                    title:            'Environmental Overview',
                    subtitle:         'REAL-TIME MONITORING ACTIVE',
                    aq_title:         'Air Quality',
                    aq_desc:          'Optimal Range',
                    temp_title:       'Temperature',
                    temp_desc:        'Stable Ambient',
                    hum_title:        'Humidity',
                    hum_desc:         'Optimal Range',
                    co2_title:        'CO2 Levels',
                    co2_desc_critical:'Critical Alert',
                    co2_desc_stable:  'Stable Range',
                    chart_title:      '24-Hour Air Quality Trend',
                    sensor:           'SENSOR NODE: REDESMA-01',
                },
                id: {
                    theme_light:      'Terang',
                    theme_dark:       'Gelap',
                    title:            'Ikhtisar Lingkungan',
                    subtitle:         'PEMANTAUAN REAL-TIME AKTIF',
                    aq_title:         'Kualitas Udara',
                    aq_desc:          'Rentang Optimal',
                    temp_title:       'Suhu',
                    temp_desc:        'Suhu Stabil',
                    hum_title:        'Kelembapan',
                    hum_desc:         'Rentang Optimal',
                    co2_title:        'Kadar CO2',
                    co2_desc_critical:'Peringatan Kritis',
                    co2_desc_stable:  'Rentang Stabil',
                    chart_title:      'Tren Kualitas Udara 24 Jam',
                    sensor:           'TITIK SENSOR: REDESMA-01',
                },
            };

            // Flags: country code of the language you will switch TO
            // flagcdn.com serves flags by ISO 3166-1 alpha-2 code
            const langFlags  = { en: 'id', id: 'gb' };   // en → show 🇮🇩, id → show 🇬🇧
            const langAlts   = { en: 'Switch to Indonesian', id: 'Switch to English' };
            const langLabels = { en: 'ID', id: 'EN' };

            let currentLang = 'en';
            const langToggleBtn = document.getElementById('lang-toggle');
            const langFlag      = document.getElementById('lang-flag');  // <img>
            const langLabel     = document.getElementById('lang-label');

            // Apply all UI strings for `currentLang`
            function applyTranslations() {
                const t = translations[currentLang];

                document.getElementById('t-title').textContent       = t.title;
                document.getElementById('t-subtitle').textContent    = t.subtitle;
                document.getElementById('t-aq-title').textContent    = t.aq_title;
                document.getElementById('t-aq-desc').textContent     = t.aq_desc;
                document.getElementById('t-temp-title').textContent  = t.temp_title;
                document.getElementById('t-temp-desc').textContent   = t.temp_desc;
                document.getElementById('t-hum-title').textContent   = t.hum_title;
                document.getElementById('t-hum-desc').textContent    = t.hum_desc;
                document.getElementById('t-co2-title').textContent   = t.co2_title;
                document.getElementById('t-chart-title').textContent = t.chart_title;
                document.getElementById('t-sensor').textContent      = t.sensor;

                const isCo2Critical = document.getElementById('card-co2').getAttribute('data-critical') === 'true';
                document.getElementById('t-co2-desc').textContent = isCo2Critical
                    ? t.co2_desc_critical
                    : t.co2_desc_stable;

                const isDark = document.documentElement.classList.contains('dark');
                themeText.textContent = isDark ? t.theme_light : t.theme_dark;

                // Update flag image to show the target language
                langFlag.src          = `https://flagcdn.com/w40/${langFlags[currentLang]}.png`;
                langFlag.alt          = langAlts[currentLang];
                langLabel.textContent = langLabels[currentLang];
            }

            // ── Auto-detect browser language on first load ─────────
            (function detectLang() {
                const browserLang = (navigator.language || navigator.userLanguage || 'en').toLowerCase();
                currentLang = browserLang.startsWith('id') ? 'id' : 'en';
                applyTranslations();
            })();

            // ── Manual toggle ──────────────────────────────────────
            langToggleBtn.addEventListener('click', () => {
                currentLang = currentLang === 'en' ? 'id' : 'en';
                applyTranslations();
            });

            // ── Data Updater (for DB / MQTT hook-in) ──────────────
            window.updateDashboardData = function (data) {
                if (data.airQuality  !== undefined) document.getElementById('val-aq').textContent   = data.airQuality;
                if (data.temperature !== undefined) document.getElementById('val-temp').textContent = data.temperature;
                if (data.humidity    !== undefined) document.getElementById('val-hum').textContent  = data.humidity;

                if (data.co2 !== undefined) {
                    document.getElementById('val-co2').textContent = data.co2;

                    const co2Card = document.getElementById('card-co2');
                    const co2Dot  = document.getElementById('co2-status-dot');
                    const co2Desc = document.getElementById('t-co2-desc');
                    const co2Icon = document.getElementById('co2-icon');

                    if (data.co2 < 800) {
                        co2Card.setAttribute('data-critical', 'false');
                        co2Dot.className  = 'w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-emerald-500 text-emerald-500 dark:bg-white dark:text-white';
                        co2Desc.className = 'text-sm font-medium text-blue-500 dark:text-blue-400';
                        co2Desc.textContent = translations[currentLang].co2_desc_stable;
                        co2Icon.className = 'text-[2rem] leading-none text-blue-500 dark:text-blue-400 bg-blue-500/10 p-3 rounded-2xl';
                    } else {
                        co2Card.setAttribute('data-critical', 'true');
                        co2Dot.className  = 'w-3 h-3 rounded-full shadow-[0_0_10px_currentColor] bg-red-500 text-red-500 animate-[flash_1.5s_infinite]';
                        co2Desc.className = 'text-sm font-medium text-red-500';
                        co2Desc.textContent = translations[currentLang].co2_desc_critical;
                        co2Icon.className = 'text-[2rem] leading-none text-red-400 bg-red-500/10 p-3 rounded-2xl';
                    }
                }
            };

        });
    </script>
</body>
</html>
